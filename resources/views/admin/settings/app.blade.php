@extends('layouts.admin.app')
@section('title', translate('messages.App Settings'))

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.App Settings') }}</h1>
    <p class="mb-4">{{ translate('messages.Manage mobile app version control and download URLs') }}</p>

    @include('admin.settings._partials.tabs')

    <div id="settingsContainer">
        <div class="settings-section {{ request()->query('tab') == 'app' ? 'active' : '' }}" id="app-settings">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.User App Version Control') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update', ['tab' => 'app']) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Android Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="mb-3 text-success">
                                    <i class="fab fa-android mr-2"></i>
                                    {{ translate('messages.For android') }}
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="android_min_version">{{ translate('messages.Minimum User App Version (Android)') }}</label>
                                    <input type="text" id="android_min_version" name="android_min_version" 
                                           class="form-control @error('android_min_version') is-invalid @enderror" 
                                           value="{{ $settings['android_min_version'] ?? '' }}" 
                                           placeholder="e.g., 1.0.0">
                                    @error('android_min_version')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="android_download_url">{{ translate('messages.Download URL for User App (Android)') }}</label>
                                    <input type="url" id="android_download_url" name="android_download_url" 
                                           class="form-control @error('android_download_url') is-invalid @enderror" 
                                           value="{{ $settings['android_download_url'] ?? '' }}" 
                                           placeholder="https://play.google.com/store/apps/details?id=com.example.app">
                                    @error('android_download_url')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- iOS Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="mb-3 text-info">
                                    <i class="fab fa-apple mr-2"></i>
                                    {{ translate('messages.For iOS') }}
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ios_min_version">{{ translate('messages.Minimum User App Version (Ios)') }}</label>
                                    <input type="text" id="ios_min_version" name="ios_min_version" 
                                           class="form-control @error('ios_min_version') is-invalid @enderror" 
                                           value="{{ $settings['ios_min_version'] ?? '' }}" 
                                           placeholder="e.g., 1.0.0">
                                    @error('ios_min_version')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ios_download_url">{{ translate('messages.Download URL for User App (Ios)') }}</label>
                                    <input type="url" id="ios_download_url" name="ios_download_url" 
                                           class="form-control @error('ios_download_url') is-invalid @enderror" 
                                           value="{{ $settings['ios_download_url'] ?? '' }}" 
                                           placeholder="https://apps.apple.com/us/app/example-app/id123456789">
                                    @error('ios_download_url')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>{{ translate('messages.Save Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
