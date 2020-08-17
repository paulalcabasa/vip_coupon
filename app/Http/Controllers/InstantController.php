<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InstantService;

class InstantController extends Controller
{
    public function instantProcess(Request $request, InstantService $instantService){
        return response()->json($instantService->instantProcess($request),200);
    }
}
