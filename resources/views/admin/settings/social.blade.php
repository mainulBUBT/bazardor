@extends('layouts.admin.app')
@section('title', translate('messages.Social Connect'))

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Social Connect') }}</h1>
    <p class="mb-4">{{ translate('messages.Manage social login credentials and availability for the platform.') }}</p>

    @include('admin.settings._partials.tabs')

    @php
        $googleSettings = $settings['google_login'] ?? ['enabled' => false, 'client_id' => '', 'client_secret' => ''];
        $facebookSettings = $settings['facebook_login'] ?? ['enabled' => false, 'client_id' => '', 'client_secret' => ''];
    @endphp

    <div id="settingsContainer"
         data-copy-copied="{{ translate('messages.Copied') }}"
         data-copy-success="{{ translate('messages.Callback URL copied to clipboard') }}"
         data-copy-failure="{{ translate('messages.Failed to copy. Please copy manually.') }}">
        <div class="settings-section active" id="social-settings">
            <!-- Google Login Configuration -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ translate('messages.Google Login Configuration') }}
                        <i class="fas fa-info-circle info-icon" 
                           onclick="showSetupModal('google')"
                           title="Click for setup instructions"></i>
                    </h6>
                    <span class="badge badge-{{ !empty($googleSettings['enabled']) ? 'success' : 'secondary' }}">
                        {{ !empty($googleSettings['enabled']) ? translate('messages.Active') : translate('messages.Inactive') }}
                    </span>
                </div>
                <div class="card-body">

                    <!-- Callback URL -->
                    <div class="form-group">
                        <label class="font-weight-bold">{{ translate('messages.Callback URI') }}</label>
                        <div class="callback-url-box">
                            <code id="googleCallbackUrl">{{ url('/customer/auth/login/google/callback') }}</code>
                            <button type="button" class="btn btn-sm btn-outline-primary copy-btn" onclick="copyToClipboard('googleCallbackUrl', this)">
                                <i class="fas fa-copy mr-1"></i>{{ translate('messages.Copy') }}
                            </button>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.social-connect-update', ['tab' => SOCIAL_SETTINGS]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="enable_google_login" value="0">
                        <input type="hidden" name="login_type" value="google">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="googleClientId">{{ translate('messages.Google Client ID') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="googleClientId" name="google_client_id" value="{{ old('google_client_id', $googleSettings['client_id'] ?? '') }}" placeholder="xxxxxxxxxx.apps.googleusercontent.com" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="googleClientSecret">{{ translate('messages.Google Client Secret') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="googleClientSecret" name="google_client_secret" value="{{ old('google_client_secret', $googleSettings['client_secret'] ?? '') }}" placeholder="GOCSPX-xxxxxxxxxxxxxxxxxxxx" required>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="enableGoogleLogin" name="enable_google_login" value="1" {{ old('enable_google_login', $googleSettings['enabled'] ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="enableGoogleLogin">{{ translate('messages.Enable Google Login') }}</label>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>{{ translate('messages.Save Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Facebook Login Configuration -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ translate('messages.Facebook Login Configuration') }}
                        <i class="fas fa-info-circle info-icon" 
                           onclick="showSetupModal('facebook')"
                           title="Click for setup instructions"></i>
                    </h6>
                    <span class="badge badge-{{ !empty($facebookSettings['enabled']) ? 'success' : 'secondary' }}">
                        {{ !empty($facebookSettings['enabled']) ? translate('messages.Active') : translate('messages.Inactive') }}
                    </span>
                </div>
                <div class="card-body">

                    <!-- Callback URL -->
                    <div class="form-group">
                        <label class="font-weight-bold">{{ translate('messages.Callback URI') }}</label>
                        <div class="callback-url-box">
                            <code id="facebookCallbackUrl">{{ url('/customer/auth/login/facebook/callback') }}</code>
                            <button type="button" class="btn btn-sm btn-outline-primary copy-btn" onclick="copyToClipboard('facebookCallbackUrl', this)">
                                <i class="fas fa-copy mr-1"></i>{{ translate('messages.Copy') }}
                            </button>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.social-connect-update', ['tab' => SOCIAL_SETTINGS]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="enable_facebook_login" value="0">
                        <input type="hidden" name="login_type" value="facebook">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="facebookClientId">{{ translate('messages.Facebook App ID') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="facebookClientId" name="facebook_client_id" value="{{ old('facebook_client_id', $facebookSettings['client_id'] ?? '') }}" placeholder="1234567890123456" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="facebookClientSecret">{{ translate('messages.Facebook App Secret') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="facebookClientSecret" name="facebook_client_secret" value="{{ old('facebook_client_secret', $facebookSettings['client_secret'] ?? '') }}" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" required>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="enableFacebookLogin" name="enable_facebook_login" value="1" {{ old('enable_facebook_login', $facebookSettings['enabled'] ?? false) ? 'checked' : '' }}>
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

    <!-- Setup Instructions Modal -->
    <div class="modal fade setup-modal" id="setupModal" tabindex="-1" role="dialog" aria-labelledby="setupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setupModalLabel">Setup Instructions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="setupModalBody">
                    <!-- Content will be inserted here by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const setupInstructions = {
        google: {
            title: 'Google API Setup Instructions',
            body: `
                <ol class="mb-0 pl-3">
                    <li>Visit <a href="https://console.cloud.google.com/apis/credentials" target="_blank" rel="noopener">Google Cloud Console</a>.</li>
                    <li>Create or select a project, then choose <strong>Create credentials &gt; OAuth client ID</strong>.</li>
                    <li>Select <strong>Web application</strong> as the application type.</li>
                    <li>Name the client and add the callback URI shown on this page to <strong>Authorized redirect URIs</strong>.</li>
                    <li>Save to get the <strong>Client ID</strong> and <strong>Client Secret</strong>, then paste them below.</li>
                    <li>Ensure the consent screen is configured and published.</li>
                </ol>
            `
        },
        facebook: {
            title: 'Facebook API Setup Instructions',
            body: `
                <ol class="mb-0 pl-3">
                    <li>Visit <a href="https://developers.facebook.com/" target="_blank" rel="noopener">Facebook Developers</a> and create or select an app.</li>
                    <li>Add the <strong>Facebook Login</strong> product and choose the <strong>Web</strong> platform.</li>
                    <li>Provide your site URL when prompted, then go to <strong>Settings &gt; Basic</strong> to copy the App ID and App Secret.</li>
                    <li>Under <strong>Facebook Login &gt; Settings</strong>, enable <strong>Client OAuth Login</strong>.</li>
                    <li>Add the callback URI shown on this page to <strong>Valid OAuth Redirect URIs</strong>.</li>
                    <li>Save the changes, then paste the App ID and App Secret below.</li>
                </ol>
            `
        }
    };

    // Show setup modal
    function showSetupModal(provider) {
        const instructions = setupInstructions[provider];
        if (!instructions) {
            return;
        }

        const modal = $('#setupModal');
        modal.find('#setupModalLabel').text(instructions.title);
        modal.find('#setupModalBody').html(instructions.body);
        modal.modal('show');
    }

    function showCopyToast(message, type = 'success') {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        }
    }

    const settingsContainer = document.getElementById('settingsContainer');
    const copyMessages = settingsContainer ? {
        copied: settingsContainer.dataset.copyCopied || 'Copied',
        success: settingsContainer.dataset.copySuccess || 'Callback URL copied to clipboard',
        failure: settingsContainer.dataset.copyFailure || 'Failed to copy. Please copy manually.'
    } : {
        copied: 'Copied',
        success: 'Callback URL copied to clipboard',
        failure: 'Failed to copy. Please copy manually.'
    };

    function animateCopyButton(button) {
        if (!button) {
            return;
        }

        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-1"></i>' + copyMessages.copied;
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');

        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
        }, 2000);
    }

    // Copy to clipboard
    function copyToClipboard(elementId, buttonElement) {
        const target = document.getElementById(elementId);
        if (!target) {
            return;
        }

        const text = target.textContent.trim();

        const handleSuccess = () => {
            animateCopyButton(buttonElement);
            showCopyToast(copyMessages.success);
        };

        const handleFailure = () => {
            showCopyToast(copyMessages.failure, 'error');
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(handleSuccess).catch(handleFailure);
        } else {
            // Fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'absolute';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            const selection = document.getSelection();
            const selectedRange = selection && selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
            textarea.select();

            try {
                const successful = document.execCommand('copy');
                document.body.removeChild(textarea);
                if (selectedRange && selection) {
                    selection.removeAllRanges();
                    selection.addRange(selectedRange);
                }
                successful ? handleSuccess() : handleFailure();
            } catch (err) {
                document.body.removeChild(textarea);
                handleFailure();
            }
        }
    }
</script>
@endpush
