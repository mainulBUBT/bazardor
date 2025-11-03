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
                                    <label class="custom-file-label" for="companyLogo" id="companyLogoLabel">{{ translate('messages.Choose file') }}</label>
                                </div>

                                <div class="image-preview-container mt-3" id="logoPreviewContainer">
                                    <div class="image-preview" id="logoPreview" style="height: 150px;">
                                        @if($logo = $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'company_logo')->first()?->value)
                                            <img id="logoPreviewImg" src="{{ asset('storage/app/public/company/' . $logo) }}" alt="{{ translate('messages.Company Logo') }}" style="max-height: 130px; max-width: 100%; object-fit: contain;"/>
                                        @else
                                            <i class="fas fa-image"></i>
                                            <span>{{ translate('messages.Click to Upload Logo') }}</span>
                                            <img id="logoPreviewImg" src="#" alt="{{ translate('messages.Logo Preview') }}" class="d-none" style="max-height: 130px; max-width: 100%; object-fit: contain;"/>
                                        @endif
                                    </div>
                                </div>
                                <small class="text-muted d-block text-center mt-2">{{ translate('messages.Recommended size') }}: 300x100px</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="companyFavicon">{{ translate('messages.Favicon') }}</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="companyFavicon" name="company_favicon" accept="image/x-icon,image/png">
                                    <label class="custom-file-label" for="companyFavicon" id="companyFaviconLabel">{{ translate('messages.Choose file') }}</label>
                                </div>

                                <div class="image-preview-container mt-3" id="faviconPreviewContainer">
                                    <div class="image-preview" id="faviconPreview" style="height: 150px;">
                                        @if($favicon = $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'company_favicon')->first()?->value)
                                            <img id="faviconPreviewImg" src="{{ asset('storage/app/public/company/' . $favicon) }}" alt="{{ translate('messages.Favicon') }}" style="max-height: 32px; object-fit: contain;"/>
                                        @else
                                            <i class="fas fa-image"></i>
                                            <span>{{ translate('messages.Click to Upload Favicon') }}</span>
                                            <img id="faviconPreviewImg" src="#" alt="{{ translate('messages.Favicon Preview') }}" class="d-none" style="max-height: 32px; object-fit: contain;"/>
                                        @endif
                                    </div>
                                </div>
                                <small class="text-muted d-block text-center mt-2">{{ translate('messages.Recommended size') }}: 32x32px</small>
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
                            <i class="fas fa-money-bill-wave"></i> {{ translate('messages.Show price comparison between markets') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="showPriceComparison" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => GENERAL_SETTINGS]) }}" name="show_price_comparison" {{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'show_price_comparison')->first()?->value ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-chart-line"></i> {{ translate('messages.Enable price trend indicators') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="enablePriceTrends" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => GENERAL_SETTINGS]) }}" name="enable_price_trend_indicators" {{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'enable_price_trend_indicators')->first()?->value ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-star"></i> {{ translate('messages.Enable market ratings') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="enableMarketRatings" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => GENERAL_SETTINGS]) }}" name="enable_market_ratings" {{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'enable_market_ratings')->first()?->value ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-award"></i> {{ translate('messages.Enable volunteer points system') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="enableVolunteerPoints" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => GENERAL_SETTINGS]) }}" name="enable_volunteer_points_system" {{ $settings->where('settings_type', GENERAL_SETTINGS)->where('key_name', 'enable_volunteer_points_system')->first()?->value ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    
    </div>

@endsection

@push('scripts')

<script>
$(document).ready(function() {
    // Logo upload functionality
    $('#companyLogo').on('change', function() {
        const file = this.files[0];
        const reader = new FileReader();
        const $previewElement = $('#logoPreviewImg');
        const $previewPlaceholder = $('#logoPreview').find('i, span');
        const $label = $('#companyLogoLabel');

        if (file) {
            reader.onload = function(e) {
                $previewElement.attr('src', e.target.result).removeClass('d-none');
                $previewPlaceholder.addClass('d-none');
            }
            reader.readAsDataURL(file);
            $label.text(file.name);
        } else {
            $previewElement.attr('src', '#').addClass('d-none');
            $previewPlaceholder.removeClass('d-none');
            $label.text('{{ translate("messages.Choose file") }}');
        }
    });

    // Favicon upload functionality
    $('#companyFavicon').on('change', function() {
        const file = this.files[0];
        const reader = new FileReader();
        const $previewElement = $('#faviconPreviewImg');
        const $previewPlaceholder = $('#faviconPreview').find('i, span');
        const $label = $('#companyFaviconLabel');

        if (file) {
            reader.onload = function(e) {
                $previewElement.attr('src', e.target.result).removeClass('d-none');
                $previewPlaceholder.addClass('d-none');
            }
            reader.readAsDataURL(file);
            $label.text(file.name);
        } else {
            $previewElement.attr('src', '#').addClass('d-none');
            $previewPlaceholder.removeClass('d-none');
            $label.text('{{ translate("messages.Choose file") }}');
        }
    });

    // Trigger file input when preview container is clicked
    $('#logoPreviewContainer').on('click', function() {
        $('#companyLogo').click();
    });

    $('#faviconPreviewContainer').on('click', function() {
        $('#companyFavicon').click();
    });
});
</script>
@endpush