<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserType;
use App\Models\Admin;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('admin');

        if ($guard->check()) {
            $user = $guard->user();

            if ($user instanceof Admin && $user->hasAnyRole([
                UserType::SUPER_ADMIN->value,
                UserType::MODERATOR->value,
            ])) {
                return $next($request);
            }

            $guard->logout();
        }

        Toastr::error(translate('messages.you_do_not_have_permission_to_access_panel'));
        return redirect()->route('admin.auth.login');
    }
} 