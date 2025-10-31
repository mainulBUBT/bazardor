<?php

namespace App\Services;

use App\Models\PushNotification;
use App\Models\User;

class PushNotificationService
{
    public function __construct(
        private PushNotification $pushNotification,
        private User $user
    ) {}

    public function getNotifications($with = [], $search = null)
    {
        return $this->pushNotification
            ->when(!empty($with), function ($query) use ($with) {
                $query->with($with);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(pagination_limit());
    }

    public function findNotification(int $id, array $with = [])
    {
        return $this->pushNotification->with($with)->findOrFail($id);
    }

    public function store(array $data)
    {
        $data['created_by'] = auth()->id();
        $data['status'] = 'sent';
        $data['sent_at'] = now();

        // Handle image upload
        if (isset($data['image']) && $data['image']->isValid()) {
            $data['image'] = handle_file_upload('push-notifications/', $data['image']->getClientOriginalExtension(), $data['image']);
        }
        unset($data['image']);

        // Get target users and set recipient count
        $zoneId = $data['zone_id'] ?? null;
        $users = $this->getTargetUsers($data['target_audience'], $zoneId);
        $data['recipients_count'] = $users->count();

        $notification = $this->pushNotification->create($data);

        // Here you would integrate with actual push notification service
        // like Firebase Cloud Messaging, OneSignal, etc.
        
        return $notification;
    }

    public function update(PushNotification $notification, array $data)
    {
        // Handle image upload
        if (isset($data['image']) && $data['image']->isValid()) {
            $oldImagePath = $notification->image;
            $data['image'] = handle_file_upload('push-notifications/', $data['image']->getClientOriginalExtension(), $data['image'], $oldImagePath);
            
            // Delete old image if it exists
            if ($oldImagePath) {
                $filename = basename($oldImagePath);
                handle_file_upload('push-notifications/', '', null, $filename);
            }
        }
        unset($data['image']);

        $notification->update($data);
        return $notification;
    }

    public function delete(PushNotification $notification)
    {
        // Delete image if exists
        if ($notification->image) {
            $filename = basename($notification->image);
            handle_file_upload('push-notifications/', '', null, $filename);
        }

        $notification->delete();
    }

    public function sendNotification(PushNotification $notification)
    {
        // Get target users
        $zoneId = $notification->zone_id;
        $users = $this->getTargetUsers($notification->target_audience, $zoneId);
        
        $notification->update([
            'status' => 'sent',
            'sent_at' => now(),
            'recipients_count' => $users->count(),
        ]);

        // Here you would integrate with actual push notification service
        // like Firebase Cloud Messaging, OneSignal, etc.
        // For now, we'll just simulate the sending
        
        return $users->count();
    }

    public function resendNotification(PushNotification $notification, string $target = 'all')
    {
        $zoneId = $notification->zone_id;
        $users = match($target) {
            'all' => $this->getTargetUsers($notification->target_audience, $zoneId),
            'unopened' => $this->getUnopenedUsers($notification),
            default => collect(),
        };

        // Here you would resend the notification
        return $users->count();
    }

    private function getTargetUsers(string $targetAudience, ?int $zoneId = null)
    {
        $query = match($targetAudience) {
            'all' => $this->user->where('status', 'active'),
            'volunteers' => $this->user->where('status', 'active')->where('is_volunteer', true),
            'inactive' => $this->user->where('status', 'active')->where('last_login_at', '<', now()->subDays(30)),
            'new' => $this->user->where('status', 'active')->where('created_at', '>', now()->subDays(7)),
            default => $this->user->whereRaw('1 = 0'), // Return empty query
        };

        // Apply zone filter if specified, last_login ow ana lagbe
        // if ($zoneId) {
        //     $query->where('zone_id', $zoneId);
        // }

        return $query->get();
    }

    private function getUnopenedUsers(PushNotification $notification)
    {
        // This would require tracking opened notifications
        // For now, return empty collection
        return collect();
    }

    public function getEstimatedReach(string $targetAudience, ?int $zoneId = null)
    {
        $query = match($targetAudience) {
            'all' => $this->user->where('status', 'active'),
            'volunteers' => $this->user->where('status', 'active')->where('is_volunteer', true),
            'inactive' => $this->user->where('status', 'active')->where('last_login_at', '<', now()->subDays(30)),
            'new' => $this->user->where('status', 'active')->where('created_at', '>', now()->subDays(7)),
            default => $this->user->whereRaw('1 = 0'), // Return empty query
        };

        // Apply zone filter if specified
        if ($zoneId) {
            $query->where('zone_id', $zoneId);
        }

        return $query->count();
    }
}
