@extends('layouts.admin.app')
@section('title', translate('messages.Other Settings'))

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Other Settings') }}</h1>
    <p class="mb-4">{{ translate('messages.Manage reCAPTCHA and other miscellaneous settings') }}</p>

    @include('admin.settings._partials.tabs')

    <div id="settingsContainer">
        <div class="settings-section {{ request()->query('tab') == OTHER_SETTINGS ? 'active' : '' }}" id="others-settings">
            
            <!-- reCAPTCHA v3 Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shield-alt mr-2"></i>{{ translate('messages.Google reCAPTCHA v3') }}
                    </h6>
                    <a href="https://www.google.com/recaptcha/admin" target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-external-link-alt mr-1"></i>{{ translate('messages.Get reCAPTCHA Keys') }}
                    </a>
                </div>
                <div class="card-body">
                    <!-- Info Alert -->
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>{{ translate('messages.How to get reCAPTCHA keys') }}:</strong>
                        <ol class="mb-0 mt-2">
                            <li>{{ translate('messages.Visit') }} <a href="https://www.google.com/recaptcha/admin" target="_blank">{{ translate('messages.Google reCAPTCHA Console') }}</a></li>
                            <li>{{ translate('messages.Click on') }} <strong>"+"</strong> {{ translate('messages.to create a new site') }}</li>
                            <li>{{ translate('messages.Select') }} <strong>reCAPTCHA v3</strong> {{ translate('messages.as the type') }}</li>
                            <li>{{ translate('messages.Add your domains') }} (localhost, 127.0.0.1, {{ translate('messages.and your production domain') }})</li>
                            <li>{{ translate('messages.Copy the Site Key and Secret Key below') }}</li>
                        </ol>
                    </div>

                    <form action="{{ route('admin.settings.update', ['tab' => OTHER_SETTINGS]) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- reCAPTCHA Site Key -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recaptcha_site_key">
                                        {{ translate('messages.reCAPTCHA Site Key') }}
                                        <i class="fas fa-info-circle text-info" 
                                           data-toggle="tooltip" 
                                           data-placement="top" 
                                           title="{{ translate('messages.This is the public key used in your frontend forms') }}"></i>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="recaptcha_site_key" 
                                        name="recaptcha_site_key" 
                                        class="form-control @error('recaptcha_site_key') is-invalid @enderror" 
                                        value="{{ $settings['recaptcha_site_key'] ?? '' }}" 
                                        placeholder="6Lc..."
                                    >
                                    @error('recaptcha_site_key')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        {{ translate('messages.Used in the login form and other public forms') }}
                                    </small>
                                </div>
                            </div>

                            <!-- reCAPTCHA Secret Key -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recaptcha_secret_key">
                                        {{ translate('messages.reCAPTCHA Secret Key') }}
                                        <i class="fas fa-info-circle text-info" 
                                           data-toggle="tooltip" 
                                           data-placement="top" 
                                           title="{{ translate('messages.This is the private key used for server-side validation') }}"></i>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="recaptcha_secret_key" 
                                        name="recaptcha_secret_key" 
                                        class="form-control @error('recaptcha_secret_key') is-invalid @enderror" 
                                        value="{{ $settings['recaptcha_secret_key'] ?? '' }}" 
                                        placeholder="6Lc..."
                                    >
                                    @error('recaptcha_secret_key')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        {{ translate('messages.Keep this key secret and secure') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- reCAPTCHA Status -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input 
                                            type="checkbox" 
                                            class="custom-control-input" 
                                            id="recaptcha_enabled" 
                                            name="recaptcha_enabled" 
                                            value="1"
                                            {{ ($settings['recaptcha_enabled'] ?? false) ? 'checked' : '' }}
                                        >
                                        <label class="custom-control-label" for="recaptcha_enabled">
                                            {{ translate('messages.Enable reCAPTCHA') }}
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        {{ translate('messages.When enabled, reCAPTCHA will be active on login and other forms') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Current Status -->
                        <div class="alert {{ ($settings['recaptcha_site_key'] ?? false) && ($settings['recaptcha_secret_key'] ?? false) ? 'alert-success' : 'alert-warning' }} mt-3">
                            <i class="fas {{ ($settings['recaptcha_site_key'] ?? false) && ($settings['recaptcha_secret_key'] ?? false) ? 'fa-check-circle' : 'fa-exclamation-triangle' }} mr-2"></i>
                            <strong>{{ translate('messages.Status') }}:</strong>
                            @if(($settings['recaptcha_site_key'] ?? false) && ($settings['recaptcha_secret_key'] ?? false))
                                {{ translate('messages.reCAPTCHA is configured and ready to use') }}
                            @else
                                {{ translate('messages.reCAPTCHA is not configured. Please add your keys above.') }}
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>{{ translate('messages.Save Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Settings Card (for future use) -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs mr-2"></i>{{ translate('messages.Additional Settings') }}
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle mr-2"></i>
                        {{ translate('messages.Additional miscellaneous settings will appear here in future updates') }}
                    </p>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Show/hide secret key toggle
        $('#toggleSecretKey').on('click', function() {
            const input = $('#recaptcha_secret_key');
            const icon = $(this).find('i');
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
</script>
@endpush
