<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserType;

class AuthenticationService
{
    /**
     * Attempt to authenticate a user.
     *
     * @param array $credentials
     * @param bool $remember
     * @return array
     */
    public function attemptLogin(array $credentials, bool $remember = false): array
    {
        $guard = Auth::guard('admin');
        if ($guard->attempt($credentials, $remember)) {
            $user = $guard->user();

            if ($user instanceof Admin && $user->hasAnyRole([UserType::SUPER_ADMIN->value, UserType::MODERATOR->value])) {
                return [
                    'success' => true,
                    'user' => $user,
                    'message' => translate('messages.login_successful'),
                    'redirect' => route('admin.dashboard')
                ];
            }
            
            $guard->logout();
            return [
                'success' => false,
                'message' => translate('messages.you_do_not_have_permission_to_access_admin_panel'),
                'redirect' => route('admin.auth.login')
            ];
        }

        return [
            'success' => false,
            'message' => translate('messages.invalid_email_or_password'),
            'redirect' => route('admin.auth.login')
        ];
    }

    /**
     * Log the user out of the application.
     *
     * @return array
     */
    public function logout(): array
    {
        Auth::logout();
        
        return [
            'success' => true,
            'message' => translate('messages.logged_out_successfully'),
            'redirect' => route('admin.auth.login')
        ];
    }
    
    /**
     * Get the currently authenticated user.
     *
     * @return User|null
     */
    public function getAuthenticatedUser(): ?Admin
    {
        return Auth::guard('admin')->user();
    }
    
    /**
     * Check if the user is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return Auth::guard('admin')->check();
    }
    
    /**
     * Check if the authenticated user has admin access.
     *
     * @return bool
     */
    public function hasAdminAccess(): bool
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user instanceof Admin) {
            return false;
        }

        return $user->hasAnyRole([UserType::SUPER_ADMIN->value, UserType::MODERATOR->value]);
    }
} 