<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
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
        } else {
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
        ], 500);
    }
}
