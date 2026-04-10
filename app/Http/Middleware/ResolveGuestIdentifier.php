<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResolveGuestIdentifier
{
    /**
     * Optionally authenticate via API token (if present) and extract device_id.
     *
     * - If a valid Bearer token is provided, resolves the authenticated user.
     * - If no token (or invalid), the request proceeds as guest.
     * - device_id is extracted from X-Device-ID header or request body for guest tracking.
     */
    public function handle(Request $request, Closure $next)
    {
        // Try to authenticate via the api (sanctum) guard — non-blocking
        if ($user = Auth::guard('api')->user()) {
            Auth::setUser($user);
        }

        $deviceId = $request->header('X-Device-ID')
                    ?? $request->input('device_id');

        if ($deviceId) {
            $deviceId = substr(trim($deviceId), 0, 255);
        }

        $request->attributes->set('device_id', $deviceId);

        return $next($request);
    }
}
