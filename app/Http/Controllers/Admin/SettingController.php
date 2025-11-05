<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{

    public function __construct(protected SettingService $settingService)
    {
    }

    /**
     * Display the settings page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', GENERAL_SETTINGS);
        $settings = $this->settingService->getSettings($tab);
        
        if ($tab === GENERAL_SETTINGS) {
            // Keep settings as collection for the general.blade.php template
            return view('admin.settings.general', compact('tab', 'settings'));
        } else if ($tab === BUSINESS_RULES) {
            $settings = $settings->keyBy('key_name')->map(function ($setting) {
                return [
                    'value' => $setting->value,
                    'status' => $setting->is_active,
                ];
            });
            return view('admin.settings.business-rules', compact('tab', 'settings'));
        } else if ($tab === NOTIFICATION_SETTINGS) {
            // Keep settings as collection for the notifications.blade.php template
            return view('admin.settings.notifications', compact('tab', 'settings'));
        } else if ($tab === SOCIAL_SETTINGS) {
            $settings = $settings->keyBy('key_name')->map(function ($setting) {
                return $setting->value;
            });
            return view('admin.settings.social', compact('tab', 'settings'));
        } 
        else {
            $settings = $settings->mapWithKeys(function ($setting) {
                return [$setting->key_name => $setting->value];
            });
        }
        return view('admin.settings.'.$tab, compact('tab', 'settings'));
    }


    /**
     * Update the specified settings.
     *
     * @param  \App\Http\Requests\UpdateSettingsRequest  $updateSettingsRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(UpdateSettingsRequest $updateSettingsRequest): RedirectResponse
    {
        $tab = $updateSettingsRequest->query('tab', GENERAL_SETTINGS);
        $validated = $updateSettingsRequest->validated();
        unset($validated['tab']); // Remove tab from validated data 
        $success = $this->settingService->updateSettings($validated, $tab);
        
        if ($success) {
            Toastr::success(translate('messages.Settings updated successfully!'));
            return redirect()->back()->withInput(['tab' => $tab]);
        }
        
        Toastr::error(translate('messages.Failed to update settings.'));
        return redirect()->back()->withInput(['tab' => $tab]);
    }

    /**
     * Update the specified settings status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|boolean',   
            'id' => 'required',
            'tab' => 'sometimes|string'  
        ]);
        
        $tab = $request->input('tab', $request->query('tab', GENERAL_SETTINGS));
        
        $success = $this->settingService->updateStatus($validated['id'], $validated['status'], $tab);
        
        if ($success) {
            return response()->json([
                'success' => true,
                'message' => translate('messages.status_updated_successfully')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => translate('messages.failed_to_update_status')
        ], 400);
    }

    /**
     * Clear application cache
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');
        
        try {
            switch ($type) {
                case 'view':
                    \Artisan::call('view:clear');
                    $message = translate('messages.View cache cleared successfully');
                    break;
                case 'all':
                default:
                    \Artisan::call('cache:clear');
                    \Artisan::call('config:clear');
                    \Artisan::call('route:clear');
                    \Artisan::call('view:clear');
                    $message = translate('messages.All cache cleared successfully');
                    break;
            }
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('messages.Failed to clear cache') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create database backup
     */
    public function createBackup()
    {
        try {
            $filename = 'backup-' . date('Y-m-d-His') . '.sql';
            $backupPath = storage_path('app/backups');
            
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            $fullPath = $backupPath . '/' . $filename;
            
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            
            $command = sprintf(
                'mysqldump -h %s -u %s -p%s %s > %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbName),
                escapeshellarg($fullPath)
            );
            
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0 && file_exists($fullPath)) {
                return response()->json([
                    'success' => true,
                    'message' => translate('messages.Backup created successfully'),
                    'filename' => $filename
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => translate('messages.Failed to create backup')
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('messages.Failed to create backup') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle maintenance mode flag
     */
    public function toggleMaintenance(Request $request)
    {
        $enable = $request->input('enable', false);
        
        try {
            $this->settingService->updateSettings([
                'maintenance_mode' => $enable ? '1' : '0'
            ], 'general');
            
            $message = $enable 
                ? translate('messages.Maintenance mode enabled') 
                : translate('messages.Maintenance mode disabled');
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('messages.Failed to toggle maintenance mode') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function socialConnectUpdate(Request $request)
    {

        $validated = $request->validate([
            'login_type' => 'required|string',
            'enable_google_login' => 'required_if:login_type,google|boolean',
            'google_client_id' => 'required_if:login_type,google|string',
            'google_client_secret' => 'required_if:login_type,google|string',
            'enable_facebook_login' => 'required_if:login_type,facebook|boolean',
            'facebook_client_id' => 'required_if:login_type,facebook|string',
            'facebook_client_secret' => 'required_if:login_type,facebook|string',
        ]);

        if ($request->login_type == 'google') {
            Setting::updateOrCreate(
                ['key_name' => 'google_login', 'settings_type' => SOCIAL_SETTINGS],
                ['value' => [
                    'enabled' => (bool)$validated['enable_google_login'],
                    'client_id' => $validated['google_client_id'] ?? '',
                    'client_secret' => $validated['google_client_secret'] ?? '',
                ]]
            );
        }

        if ($request->login_type == 'facebook') {
            Setting::updateOrCreate(
                ['key_name' => 'facebook_login', 'settings_type' => SOCIAL_SETTINGS],
                ['value' => [
                    'enabled' => (bool)$validated['enable_facebook_login'],
                    'client_id' => $validated['facebook_client_id'] ?? '',
                    'client_secret' => $validated['facebook_client_secret'] ?? '',
                ]]
            );
        }

        Toastr::success(translate('messages.Social connect settings updated successfully'));
        return redirect()->back();
    }
}
