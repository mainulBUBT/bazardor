<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ translate('messages.Admin login for Bazar-dor') }}">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ translate('messages.Bazar-dor Admin - Login') }}</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('public/assets/admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('public/assets/admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <!-- Custom styles for login page -->
    <link href="{{ asset('public/assets/admin/css/custom.css') }}" rel="stylesheet">
    <!-- Toastr CSS -->
    <link href="{{ asset('public/assets/admin/vendor/toastr/toastr.css') }}" rel="stylesheet"/>
    
    <!-- Google reCAPTCHA v3 (only if configured) -->
    @if(isset($recaptchaSiteKey) && $recaptchaSiteKey && ($recaptchaEnabled ?? false))
    <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
    @endif

</head>

<body class="modern-login-body">
    <!-- Animated Background -->
    <div class="login-background">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
    </div>

    <div class="login-container">
        <div class="login-wrapper">
            <!-- Left Side - Branding -->
            <div class="login-brand-section">
                <div class="brand-content">
                    <div class="brand-logo-wrapper">
                        <i class="fas fa-shopping-basket brand-icon"></i>
                    </div>
                    <h1 class="brand-title">Bazar-dor</h1>
                    <p class="brand-subtitle">{{ translate('messages.Admin Dashboard') }}</p>
                    <div class="brand-features">
                        <div class="feature-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>{{ translate('messages.Secure Access') }}</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-chart-line"></i>
                            <span>{{ translate('messages.Analytics') }}</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-users"></i>
                            <span>{{ translate('messages.User Management') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="login-form-section">
                <div class="login-card">
                    <div class="login-header">
                        <h2 class="login-title">{{ translate('messages.Welcome Back') }}</h2>
                        <p class="login-subtitle">{{ translate('messages.Please login to your account') }}</p>
                    </div>

                    <form class="modern-login-form" method="POST" action="{{ route('admin.auth.login.submit') }}" id="loginForm">
                        @csrf
                        
                        <!-- Email Field -->
                        <div class="form-group-modern">
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input 
                                    type="email" 
                                    class="form-control-modern @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="email"
                                    autofocus
                                >
                                <label for="email" class="floating-label">{{ translate('messages.Email Address') }}</label>
                            </div>
                            @error('email')
                                <span class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="form-group-modern">
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input 
                                    type="password" 
                                    class="form-control-modern @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                >
                                <label for="password" class="floating-label">{{ translate('messages.Password') }}</label>
                                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                            </div>
                            @error('password')
                                <span class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="form-options">
                            <label class="custom-checkbox">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                                <span class="checkbox-label">{{ translate('messages.Remember me') }}</span>
                            </label>
                            <a href="#" class="forgot-link">{{ translate('messages.Forgot Password?') }}</a>
                        </div>

                        <!-- reCAPTCHA Token -->
                        <input type="hidden" name="recaptcha_token" id="recaptcha_token">

                        <!-- Submit Button -->
                        <button type="submit" class="btn-modern-primary" id="loginBtn">
                            <span class="btn-text">{{ translate('messages.Sign In') }}</span>
                            <i class="fas fa-arrow-right btn-icon"></i>
                        </button>

                        <!-- reCAPTCHA Badge Info -->
                        <div class="recaptcha-info">
                            <i class="fas fa-shield-alt"></i>
                            <span>{{ translate('messages.Protected by reCAPTCHA') }}</span>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="login-footer">
                    <p>&copy; {{ date('Y') }} Bazar-dor. {{ translate('messages.All rights reserved.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('public/assets/admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('public/assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('public/assets/admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('public/assets/admin/js/sb-admin-2.min.js') }}"></script>
    
    <!-- Toastr JS -->
    <script src="{{ asset('public/assets/admin/vendor/toastr/toastr.js') }}"></script>
    {!! Toastr::message() !!}

    <!-- Custom Login Scripts -->
    <script>
        $(document).ready(function() {
            // Password toggle functionality
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            // Floating label animation
            $('.form-control-modern').on('focus', function() {
                $(this).parent().addClass('focused');
            });

            $('.form-control-modern').on('blur', function() {
                if ($(this).val() === '') {
                    $(this).parent().removeClass('focused');
                }
            });

            // Check if inputs have values on page load
            $('.form-control-modern').each(function() {
                if ($(this).val() !== '') {
                    $(this).parent().addClass('focused');
                }
            });

            // reCAPTCHA v3 integration (optional)
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                const submitBtn = $('#loginBtn');
                const recaptchaSiteKey = '{{ $recaptchaSiteKey ?? '' }}';
                
                // Disable button and show loading state
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> {{ translate('messages.Signing In...') }}');

                // Check if reCAPTCHA is configured
                if (recaptchaSiteKey && recaptchaSiteKey.trim() !== '' && typeof grecaptcha !== 'undefined') {
                    // reCAPTCHA is configured, use it
                    grecaptcha.ready(function() {
                        grecaptcha.execute(recaptchaSiteKey, {action: 'login'})
                            .then(function(token) {
                                $('#recaptcha_token').val(token);
                                form.submit();
                            })
                            .catch(function(error) {
                                console.error('reCAPTCHA error:', error);
                                submitBtn.prop('disabled', false);
                                submitBtn.html('<span class="btn-text">{{ translate('messages.Sign In') }}</span><i class="fas fa-arrow-right btn-icon"></i>');
                                if (typeof toastr !== 'undefined') {
                                    toastr.error('{{ translate('messages.reCAPTCHA verification failed. Please try again.') }}');
                                } else {
                                    alert('{{ translate('messages.reCAPTCHA verification failed. Please try again.') }}');
                                }
                            });
                    });
                } else {
                    // reCAPTCHA not configured, submit form directly
                    console.log('reCAPTCHA not configured, submitting form without it');
                    form.submit();
                }
            });

            // Add ripple effect to button
            $('.btn-modern-primary').on('click', function(e) {
                const ripple = $('<span class="ripple"></span>');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.css({
                    width: size + 'px',
                    height: size + 'px',
                    left: x + 'px',
                    top: y + 'px'
                });
                
                $(this).append(ripple);
                
                setTimeout(function() { ripple.remove(); }, 600);
            });
        });
    </script>

</body>

</html>