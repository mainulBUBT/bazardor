@section('title', translate('messages.Notification Settings'))
@extends('layouts.admin.app')
@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Notification Settings') }}</h1>
    <p class="mb-4">{{ translate('messages.Configure notification preferences and delivery settings') }}</p>

    @include('admin.settings._partials.tabs')

    <!-- Settings Container -->
    <div id="settingsContainer">
        <div class="settings-section notification-settings {{ request()->query('tab') == NOTIFICATION_SETTINGS ? 'active' : '' }}" id="notification-settings">
            <style>
             #notification-settings .toggle-item { transition: none !important; animation: none !important; }
             #notification-settings .toggle-item:hover { background-color: transparent !important; transform: none !important; box-shadow: none !important; }
             #notification-settings .toggle-switch .toggle-slider { transition: none !important; animation: none !important; }
             #notification-settings .toggle-switch .toggle-slider:before { transition: none !important; animation: none !important; }
             #notification-settings .toggle-item:hover .toggle-switch .toggle-slider { background-color: inherit !important; }
             /* Prevent label text/icon from animating or shifting on hover */
             #notification-settings .toggle-label,
             #notification-settings .toggle-label *,
             #notification-settings .toggle-item .toggle-label i {
               transition: none !important;
               animation: none !important;
               transform: none !important;
             }
             #notification-settings .toggle-item:hover .toggle-label {
               transform: none !important;
               padding-left: 0 !important;
               font-weight: inherit !important;
               color: inherit !important;
             }
            </style>

            <!-- Firebase Cloud Messaging Configuration -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Firebase Cloud Messaging Setup') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update', ['tab' => NOTIFICATION_SETTINGS]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="tab" value="{{ NOTIFICATION_SETTINGS }}">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="firebaseServiceFile">{{ translate('messages.Service file content') }}</label>
                                <textarea class="form-control" id="firebaseServiceFile" name="firebase_service_file" rows="4" placeholder="{{ translate('messages.Paste your Firebase service file JSON here') }}">{{ old('firebase_service_file', optional($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'firebase_service_file')->first())->value) }}</textarea>
                                <small class="form-text text-muted">{{ translate('messages.Optional: include the Firebase web config JSON for client apps') }}</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="firebaseApiKey">{{ translate('messages.API Key') }}</label>
                                <input type="text" class="form-control" id="firebaseApiKey" name="firebase_api_key" value="{{ old('firebase_api_key', optional($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'firebase_api_key')->first())->value) }}" placeholder="{{ translate('messages.Enter your Firebase API key') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="firebaseAuthDomain">{{ translate('messages.Auth domain') }}</label>
                                <input type="text" class="form-control" id="firebaseAuthDomain" name="firebase_auth_domain" value="{{ old('firebase_auth_domain', optional($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'firebase_auth_domain')->first())->value) }}" placeholder="{{ translate('messages.Enter auth domain') }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="firebaseStorageBucket">{{ translate('messages.Storage bucket') }}</label>
                                <input type="text" class="form-control" id="firebaseStorageBucket" name="firebase_storage_bucket" value="{{ old('firebase_storage_bucket', optional($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'firebase_storage_bucket')->first())->value) }}" placeholder="{{ translate('messages.Enter storage bucket URL') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="firebaseMeasurementId">{{ translate('messages.Measurement ID') }}</label>
                                <input type="text" class="form-control" id="firebaseMeasurementId" name="firebase_measurement_id" value="{{ old('firebase_measurement_id', optional($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'firebase_measurement_id')->first())->value) }}" placeholder="{{ translate('messages.Example') }}: F-12345678">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="firebaseMessagingSenderId">{{ translate('messages.Messaging sender ID') }}</label>
                                <input type="text" class="form-control" id="firebaseMessagingSenderId" name="firebase_messaging_sender_id" value="{{ old('firebase_messaging_sender_id', optional($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'firebase_messaging_sender_id')->first())->value) }}" placeholder="{{ translate('messages.Enter sender ID') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="firebaseAppId">{{ translate('messages.App ID') }}</label>
                                <input type="text" class="form-control" id="firebaseAppId" name="firebase_app_id" value="{{ old('firebase_app_id', optional($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'firebase_app_id')->first())->value) }}" placeholder="{{ translate('messages.Enter app ID') }}">
                            </div>
                        </div>
                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>{{ translate('messages.Save Firebase Configuration') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Core Notification Toggles -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Notification Options') }}</h6>
                </div>
                <div class="card-body">
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-envelope"></i> {{ translate('messages.Email notifications') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="enableEmailNotifications" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="enable_email_notifications" {{ $settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'enable_email_notifications')->first()?->value ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-mobile-alt"></i> {{ translate('messages.Push notifications') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="enablePushNotifications" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="enable_push_notifications" {{ $settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'enable_push_notifications')->first()?->value ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-exclamation-triangle"></i> {{ translate('messages.System errors and warnings') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="notifySystemErrorsWarnings" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="notify_system_errors_warnings" {{ $settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'notify_system_errors_warnings')->first()?->value ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection