<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserType;

class   AdminMiddleware
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
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === UserType::SUPER_ADMIN->value || $user->role === UserType::MODERATOR->value) {
                return $next($request);
            }
        }
        
        Toastr::error(translate('messages.you_do_not_have_permission_to_access_panel'));
        return redirect()->route('admin.auth.login');
    }
} 