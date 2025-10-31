<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PushNotificationStoreUpdateRequest;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use App\Services\ZoneService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function __construct(protected PushNotificationService $pushNotificationService, protected ZoneService $zoneService)
    {
    }

    /**
     * Display a listing of the push notifications.
     */
    public function index()
    {
        $notifications = $this->pushNotificationService->getNotifications(['creator']);
        return view('admin.push-notification.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new push notification.
     */
    public function create()
    {
        $zones = $this->zoneService->getActiveZones();
        return view('admin.push-notification.create', compact('zones'));
    }

    /**
     * Store a newly created push notification in storage.
     */
    public function store(PushNotificationStoreUpdateRequest $request)
    {
        $notification = $this->pushNotificationService->store($request->validated());
        Toastr::success(translate('messages.notification_sent_successfully') . " to {$notification->recipients_count} users");
        return redirect()->route('admin.push-notifications.index');
    }

    /**
     * Display the specified push notification.
     */
    public function show(PushNotification $pushNotification)
    {
        $notification = $this->pushNotificationService->findNotification($pushNotification->id, ['creator']);
        return view('admin.push-notification.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified push notification.
     */
    public function edit(PushNotification $pushNotification)
    {
        $notification = $this->pushNotificationService->findNotification($pushNotification->id);
        $zones = $this->zoneService->getActiveZones();
        return view('admin.push-notification.edit', compact('notification', 'zones'));
    }

    /**
     * Update the specified push notification in storage.
     */
    public function update(PushNotificationStoreUpdateRequest $request, PushNotification $pushNotification)
    {
        $this->pushNotificationService->update($pushNotification, $request->validated());
        Toastr::success(translate('messages.notification_updated_successfully'));
        return redirect()->route('admin.push-notifications.index');
    }

    /**
     * Remove the specified push notification from storage.
     */
    public function destroy(PushNotification $pushNotification)
    {
        $this->pushNotificationService->delete($pushNotification);
        Toastr::success(translate('messages.notification_deleted_successfully'));
        return redirect()->route('admin.push-notifications.index');
    }

    /**
     * Send the specified push notification.
     */
    public function send(PushNotification $pushNotification)
    {
        $recipients = $this->pushNotificationService->sendNotification($pushNotification);
        Toastr::success(translate('messages.notification_sent_successfully') . " to {$recipients} users");
        return redirect()->route('admin.push-notifications.index');
    }

    /**
     * Resend the specified push notification.
     */
    public function resend(Request $request, PushNotification $pushNotification)
    {
        $target = $request->input('target', 'all');
        $recipients = $this->pushNotificationService->resendNotification($pushNotification, $target);
        Toastr::success(translate('messages.notification_resent_successfully') . " to {$recipients} users");
        return redirect()->route('admin.push-notifications.index');
    }

    /**
     * Get estimated reach for target audience.
     */
    public function getEstimatedReach(Request $request)
    {
        $targetAudience = $request->input('target_audience');
        $zoneId = $request->input('zone_id');
        $reach = $this->pushNotificationService->getEstimatedReach($targetAudience, $zoneId);
        
        return response()->json([
            'count' => $reach,
            'text' => $reach . ' ' . translate('messages.users')
        ]);
    }
}
