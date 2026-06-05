<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureZoneId
{
    /**
     * Ensure the zoneId header is present on the request.
     * Sets the zoneId on request attributes for downstream use.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->hasHeader('zoneId')) {
            return response()->json(
                formated_response(constant: ZONE_ID_REQUIRED_403),
                403
            );
        }

        $zoneId = $request->header('zoneId');
        $request->attributes->set('zoneId', $zoneId);

        return $next($request);
    }
}
