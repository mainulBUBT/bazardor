<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Services\AuthenticationService;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class LoginController extends Controller
{
    
    public function __construct(
        protected AuthenticationService $authService,
        protected RecaptchaService $recaptchaService
    ) {
        
    }
    
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Get reCAPTCHA settings from service (with caching)
        $recaptchaSettings = $this->recaptchaService->getSettingsForView();
        
        return view('admin.auth.login', $recaptchaSettings);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \App\Http\Requests\Admin\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request)
    {
        // Validate reCAPTCHA token
        if (!$this->recaptchaService->validate($request->input('recaptcha_token'))) {
            Toastr::error(translate('messages.reCAPTCHA verification failed. Please try again.'));
            return redirect()->back()->withInput($request->only('email', 'remember'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        $result = $this->authService->attemptLogin($credentials, $remember);
        
        if ($result['success']) {
            Toastr::success($result['message']);
            return redirect()->intended($result['redirect']);
        }
        
        Toastr::error($result['message']);
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $result = $this->authService->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        Toastr::success($result['message']);
        return redirect()->to($result['redirect']);
    }
} 