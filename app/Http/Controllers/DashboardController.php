<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    
    public function getStatistics(DashboardService $dashboardService){
        return response()->json($dashboardService->getStatistics(),200);
    }
}
