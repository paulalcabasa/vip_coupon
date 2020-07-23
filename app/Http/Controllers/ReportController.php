<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function getVoucherSummary(Request $request, ReportService $reportService){
        return response()->json($reportService->getVoucherSummary($request), 200);
    }
}
