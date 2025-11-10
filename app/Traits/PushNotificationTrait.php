<?php

namespace App\Traits;

use App\Services\SettingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait PushNotificationTrait
{
    /**
     * Send notification to a topic
     *
     * @param string $topic Topic name
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data
     * @return bool
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        try {
            $token = $this->getServerKey();
            $projectId = $this->getProjectId();

            if (!$token || !$projectId) {
                Log::warning('FCM config missing');
                return false;
            }

            $payload = [
                'message' => [
                    'topic' => $topic,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data,
                    'android' => ['priority' => 'HIGH'],
                    'apns' => ['headers' => ['apns-priority' => '10']],
                ],
            ];

            $response = Http::withToken($token)->post(
                "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                $payload
            );

            return $response->successful();

        } catch (\Throwable $e) {
            Log::error('FCM send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send to multiple topics
     *
     * @param array $topics Topic names
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data
     * @return int Count of successful sends
     */
    public function sendToMultipleTopics(array $topics, string $title, string $body, array $data = []): int
    {
        $count = 0;
        foreach ($topics as $topic) {
            if ($this->sendToTopic($topic, $title, $body, $data)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Subscribe tokens to topic
     *
     * @param string|array $tokens Device token(s)
     * @param string $topic Topic name
     * @return bool
     */
    public function subscribeToTopic($tokens, string $topic): bool
    {
        try {
            $token = $this->getServerKey();
            if (!$token) {
                return false;
            }

            $tokens = is_array($tokens) ? $tokens : [$tokens];

            $response = Http::withToken($token)->post(
                'https://iid.googleapis.com/iid/v1:batchAdd',
                [
                    'to' => '/topics/' . $topic,
                    'registration_tokens' => $tokens,
                ]
            );

            return $response->successful();

        } catch (\Throwable $e) {
            Log::error('FCM subscribe failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Unsubscribe tokens from topic
     *
     * @param string|array $tokens Device token(s)
     * @param string $topic Topic name
     * @return bool
     */
    public function unsubscribeFromTopic($tokens, string $topic): bool
    {
        try {
            $token = $this->getServerKey();
            if (!$token) {
                return false;
            }

            $tokens = is_array($tokens) ? $tokens : [$tokens];

            $response = Http::withToken($token)->post(
                'https://iid.googleapis.com/iid/v1:batchRemove',
                [
                    'to' => '/topics/' . $topic,
                    'registration_tokens' => $tokens,
                ]
            );

            return $response->successful();

        } catch (\Throwable $e) {
            Log::error('FCM unsubscribe failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get server key (access token) with caching
     *
     * @return string|null
     */
    protected function getServerKey(): ?string
    {
        return Cache::remember('fcm_access_token', 3600, function () {
            $settingService = app(SettingService::class);
            $serviceFile = $settingService->getSetting('firebase_service_file', NOTIFICATION_SETTINGS);

            if (!$serviceFile) {
                return null;
            }

            $config = is_string($serviceFile) ? json_decode($serviceFile, true) : $serviceFile;

            if (!is_array($config) || !isset($config['private_key'], $config['client_email'])) {
                return null;
            }

            return $this->generateJWT($config);
        });
    }

    /**
     * Generate JWT token from service account
     *
     * @param array $config Firebase config
     * @return string|null
     */
    protected function generateJWT(array $config): ?string
    {
        try {
            $now = time();
            $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64_encode(json_encode([
                'iss' => $config['client_email'],
                'scope' => 'https://www.googleapis.com/auth/cloud-platform',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]));

            $signature = '';
            openssl_sign("{$header}.{$payload}", $signature, $config['private_key'], 'sha256');
            $signature = base64_encode($signature);

            $jwt = "{$header}.{$payload}." . strtr(rtrim($signature, '='), '+/', '-_');

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            return $response->json('access_token');

        } catch (\Throwable $e) {
            Log::error('JWT generation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get project ID
     *
     * @return string|null
     */
    protected function getProjectId(): ?string
    {
        $settingService = app(SettingService::class);
        $serviceFile = $settingService->getSetting('firebase_service_file', NOTIFICATION_SETTINGS);

        if ($serviceFile) {
            $config = is_string($serviceFile) ? json_decode($serviceFile, true) : $serviceFile;
            if (is_array($config) && isset($config['project_id'])) {
                return $config['project_id'];
            }
        }

        return $settingService->getSetting('firebase_project_id', NOTIFICATION_SETTINGS);
    }


    // public function notify()
    // {
    //     // Send to single topic
    //     $this->sendToTopic('all_users', 'New Update', 'Check out our latest features');

    //     // Broadcast to multiple topics
    //     $count = $this->sendToMultipleTopics(
    //         ['users', 'vendors'],
    //         'System Maintenance',
    //         'Scheduled maintenance tonight'
    //     );

    //     // Subscribe/unsubscribe
    //     $this->subscribeToTopic('device_token_123', 'market_updates');
    // }
}
