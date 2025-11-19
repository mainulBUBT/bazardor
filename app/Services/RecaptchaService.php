<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    /**
     * Default score threshold for reCAPTCHA v3
     */
    private const SCORE_THRESHOLD = 0.5;

    /**
     * Cache duration for reCAPTCHA settings (in seconds)
     */
    private const CACHE_DURATION = 3600; // 1 hour

    /**
     * Check if reCAPTCHA is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->getSetting('recaptcha_enabled');
    }

    /**
     * Get reCAPTCHA site key
     *
     * @return string|null
     */
    public function getSiteKey(): ?string
    {
        return $this->getSetting('recaptcha_site_key');
    }

    /**
     * Get reCAPTCHA secret key
     *
     * @return string|null
     */
    private function getSecretKey(): ?string
    {
        return $this->getSetting('recaptcha_secret_key');
    }

    /**
     * Validate reCAPTCHA token
     *
     * @param  string|null  $token
     * @return bool
     */
    public function validate(?string $token): bool
    {
        // If reCAPTCHA is not enabled, skip validation
        if (!$this->isEnabled()) {
            return true;
        }

        // Token is required when reCAPTCHA is enabled
        if (empty($token)) {
            Log::warning('reCAPTCHA token is missing');
            return false;
        }

        $secretKey = $this->getSecretKey();

        if (empty($secretKey)) {
            Log::warning('reCAPTCHA secret key not configured');
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->asForm()
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $token,
                ]);

            if (!$response->successful()) {
                Log::error('reCAPTCHA API request failed', [
                    'status' => $response->status()
                ]);
                return false;
            }

            $result = $response->json();

            return $this->validateResponse($result);

        } catch (\Exception $e) {
            Log::error('reCAPTCHA validation exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Validate reCAPTCHA API response
     *
     * @param  array  $result
     * @return bool
     */
    private function validateResponse(array $result): bool
    {
        // Check if the request was successful
        if (empty($result['success'])) {
            Log::warning('reCAPTCHA validation failed', [
                'error-codes' => $result['error-codes'] ?? []
            ]);
            return false;
        }

        // For reCAPTCHA v3, check the score
        $score = $result['score'] ?? 0;

        if ($score < self::SCORE_THRESHOLD) {
            Log::warning('reCAPTCHA score too low', [
                'score' => $score,
                'threshold' => self::SCORE_THRESHOLD,
                'action' => $result['action'] ?? 'N/A',
                'hostname' => $result['hostname'] ?? 'N/A'
            ]);
            return false;
        }

        // Log successful validation
        Log::info('reCAPTCHA validation successful', [
            'score' => $score,
            'action' => $result['action'] ?? 'N/A'
        ]);

        return true;
    }

    /**
     * Get reCAPTCHA setting with caching
     *
     * @param  string  $key
     * @return mixed
     */
    private function getSetting(string $key)
    {
        $cacheKey = "recaptcha_setting_{$key}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($key) {
            return Setting::where('settings_type', OTHER_SETTINGS)
                ->where('key_name', $key)
                ->value('value');
        });
    }

    /**
     * Clear cached reCAPTCHA settings
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('recaptcha_setting_recaptcha_enabled');
        Cache::forget('recaptcha_setting_recaptcha_site_key');
        Cache::forget('recaptcha_setting_recaptcha_secret_key');
    }

    /**
     * Get reCAPTCHA settings for view
     *
     * @return array
     */
    public function getSettingsForView(): array
    {
        return [
            'recaptchaSiteKey' => $this->getSiteKey(),
            'recaptchaEnabled' => $this->isEnabled(),
        ];
    }
}
