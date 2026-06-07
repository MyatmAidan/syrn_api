<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function index(Request $request): JsonResponse
    {
        $days = (int) $request->query('days', 14);

        return response()->json([
            'success' => true,
            'data' => $this->dashboardService->getOverview($days),
        ]);
    }
}
