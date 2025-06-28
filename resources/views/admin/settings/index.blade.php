@section('title', translate('messages.Settings'))
@extends('layouts.admin.app')
@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Settings') }}</h1>
    <p class="mb-4">{{ translate('messages.Configure your system settings for Bazar-dor application') }}</p>

    @include('admin.settings._partials.tabs')

    <!-- Settings Container -->
    <div id="settingsContainer">
        <!-- General Settings Section -->
        <div class="settings-section {{ request()->query('tab') == GENERAL_SETTINGS || !request()->query('tab') ? 'active' : '' }}" id="general-settings">
            <!-- Company Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Company Information') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update', ['tab' => GENERAL_SETTINGS]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="companyName">{{ translate('messages.Company Name') }}</label>
                                <input type="text" class="form-control" id="companyName" name="company_name" value="{{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'company_name')->first()->value ?? '' }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="companyEmail">{{ translate('messages.Company Email') }}</label>
                                <input type="email" class="form-control" id="companyEmail" name="company_email" value="{{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'company_email')->first()->value ?? '' }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="companyPhone">{{ translate('messages.Phone Number') }}</label>
                                <input type="tel" class="form-control" id="companyPhone" name="company_phone" value="{{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'company_phone')->first()->value ?? '' }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="companyAddress">{{ translate('messages.Address') }}</label>
                                <input type="text" class="form-control" id="companyAddress" name="company_address" value="{{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'company_address')->first()->value ?? '' }}">
                            </div>
                        </div>

                        <!-- Logo and Favicon Upload -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="companyLogo">{{ translate('messages.Company Logo') }}</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="companyLogo" name="company_logo" accept="image/*">
                                    <label class="custom-file-label" for="companyLogo">{{ translate('messages.Choose file') }}</label>
                                </div>
                                @if($logo = $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'company_logo')->first()?->value)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/app/public/company/' . $logo) }}" alt="Company Logo" class="img-thumbnail" style="max-height: 100px">
                                    </div>
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                <label for="companyFavicon">{{ translate('messages.Favicon') }}</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="companyFavicon" name="company_favicon" accept="image/x-icon,image/png">
                                    <label class="custom-file-label" for="companyFavicon">{{ translate('messages.Choose file') }}</label>
                                </div>
                                <small class="form-text text-muted">{{ translate('messages.Recommended size: 32x32 pixels, Formats: .ico, .png') }}</small>
                                @if($favicon = $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'company_favicon')->first()?->value)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/app/public/company/' . $favicon) }}" alt="Favicon" class="img-thumbnail" style="max-height: 32px">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Save Settings Button -->
                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-primary" id="saveSettings">
                                <i class="fas fa-save mr-1"></i>{{ translate('messages.Save Settings') }}
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>

            <!-- General Toggle Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.General Options') }}</h6>
                </div>
                <div class="card-body">
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-user-check"></i> {{ translate('messages.Auto-approve new user registrations') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="autoApproveUsers" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => GENERAL_SETTINGS]) }}" name="auto_approve_users" {{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'auto_approve_users')->first()?->value ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-store-alt"></i> {{ translate('messages.Auto-approve market submissions') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="autoApproveMarkets" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => GENERAL_SETTINGS]) }}" name="auto_approve_markets" {{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'auto_approve_markets')->first()?->value ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-box"></i> {{ translate('messages.Auto-approve product submissions') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="autoApproveProducts" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => GENERAL_SETTINGS]) }}" name="auto_approve_products" {{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'auto_approve_products')->first()?->value ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-globe"></i> {{ translate('messages.Enable multi-language support') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="enableMultiLanguage" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => GENERAL_SETTINGS]) }}" name="enable_multi_language" {{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'enable_multi_language')->first()?->value ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-map-marked-alt"></i> {{ translate('messages.Enable geolocation services') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="enableGeolocation" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => GENERAL_SETTINGS]) }}" name="enable_geolocation" {{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'enable_geolocation')->first()?->value ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    
    </div>

@endsection

@push('scripts')
    
@endpush