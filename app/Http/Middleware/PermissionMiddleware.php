<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\Permission;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        if (!$user || !$user->hasPermission(Permission::from($permission))) {
            abort(403, 'Unauthorized.');
        }
        return $next($request);
    }
} 