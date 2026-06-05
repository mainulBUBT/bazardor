<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StatsPulseResource;
use App\Services\StatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function __construct(
        private StatsService $statsService
    ) {}

    /**
     * Get market pulse statistics for the current zone.
     */
    public function pulse(Request $request): JsonResponse
    {
        $zoneId = $request->attributes->get('zoneId');
        $window = $request->query('window', '24h');

        if (! in_array($window, ['24h', '7d', '30d'], true)) {
            $window = '24h';
        }

        $stats = $this->statsService->getPulse($zoneId, $window);

        return response()->json(
            formated_response(STATS_PULSE_200, new StatsPulseResource($stats)),
            200
        );
    }
}
