<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

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
        $tab = $request->query('tab', 'general');
        $settings = $this->settingService->getSettings($tab);
        
        return view("admin.settings.index", compact('tab', 'settings'));
    }


    /**
     * Update the specified settings.
     *
     * @param  \App\Http\Requests\UpdateSettingsRequest  $updateSettingsRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(UpdateSettingsRequest $updateSettingsRequest): RedirectResponse
    {
        $tab = $updateSettingsRequest->query('tab', 'general');
        $validated = $updateSettingsRequest->validated();
        
        $success = $this->settingService->updateSettings($validated, $tab);
        
        if ($success) {
            return redirect()->route('admin.settings.index', ['tab' => $tab])
                ->with('success', translate('messages.Settings updated successfully!'));
        }
        
        return redirect()->route('admin.settings.index', ['tab' => $tab])
            ->with('error', translate('messages.Failed to update settings.'));
    }
}
