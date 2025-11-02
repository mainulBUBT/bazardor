@extends('layouts.admin.app')
@section('title', translate('messages.Social Connect'))

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Social Connect') }}</h1>
    <p class="mb-4">{{ translate('messages.Manage social login credentials and availability for the platform.') }}</p>

    @include('admin.settings._partials.tabs')

    <div id="settingsContainer">
        <div class="settings-section active" id="social-settings">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Google Login Configuration') }}</h6>
                    <span class="badge badge-{{ !empty($settings['enable_google_login']) ? 'success' : 'secondary' }}">
                        {{ !empty($settings['enable_google_login']) ? translate('messages.Active') : translate('messages.Inactive') }}
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update', ['tab' => SOCIAL_SETTINGS]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="enable_google_login" value="0">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="googleClientId">{{ translate('messages.Google Client ID') }}</label>
                                <input type="text" class="form-control" id="googleClientId" name="google_client_id" value="{{ old('google_client_id', $settings['google_client_id'] ?? '') }}" placeholder="xxxxxxxxxx.apps.googleusercontent.com">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="googleClientSecret">{{ translate('messages.Google Client Secret') }}</label>
                                <input type="text" class="form-control" id="googleClientSecret" name="google_client_secret" value="{{ old('google_client_secret', $settings['google_client_secret'] ?? '') }}">
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="enableGoogleLogin" name="enable_google_login" value="1" {{ !empty($settings['enable_google_login']) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="enableGoogleLogin">{{ translate('messages.Enable Google Login') }}</label>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>{{ translate('messages.Save Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Facebook Login Configuration') }}</h6>
                    <span class="badge badge-{{ !empty($settings['enable_facebook_login']) ? 'success' : 'secondary' }}">
                        {{ !empty($settings['enable_facebook_login']) ? translate('messages.Active') : translate('messages.Inactive') }}
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update', ['tab' => SOCIAL_SETTINGS]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="enable_facebook_login" value="0">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="facebookClientId">{{ translate('messages.Facebook App ID') }}</label>
                                <input type="text" class="form-control" id="facebookClientId" name="facebook_client_id" value="{{ old('facebook_client_id', $settings['facebook_client_id'] ?? '') }}" placeholder="1234567890">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="facebookClientSecret">{{ translate('messages.Facebook App Secret') }}</label>
                                <input type="text" class="form-control" id="facebookClientSecret" name="facebook_client_secret" value="{{ old('facebook_client_secret', $settings['facebook_client_secret'] ?? '') }}">
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="enableFacebookLogin" name="enable_facebook_login" value="1" {{ !empty($settings['enable_facebook_login']) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="enableFacebookLogin">{{ translate('messages.Enable Facebook Login') }}</label>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>{{ translate('messages.Save Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
