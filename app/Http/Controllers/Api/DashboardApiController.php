<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {
    }

    public function dashboard(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->reportService->getDashboardSummary(),
        ]);
    }
}
