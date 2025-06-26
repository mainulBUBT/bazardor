<?php

namespace App\Services;

use App\Models\User;
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
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            if ($user->role === UserType::SUPER_ADMIN->value || $user->role === UserType::MODERATOR->value) {
                return [
                    'success' => true,
                    'user' => $user,
                    'message' => translate('messages.login_successful'),
                    'redirect' => route('admin.dashboard')
                ];
            }
            
            Auth::logout();
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
    public function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }
    
    /**
     * Check if the user is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }
    
    /**
     * Check if the authenticated user has admin access.
     *
     * @return bool
     */
    public function hasAdminAccess(): bool
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return false;
        }
        
        return $user->role === UserType::SUPER_ADMIN->value || $user->role === UserType::MODERATOR->value;
    }
} 