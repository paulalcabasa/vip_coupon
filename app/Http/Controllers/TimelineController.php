<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TimelineService;

class TimelineController extends Controller
{
    
    private $timelineService;

    public function __construct(){
        $this->timelineService = new TimelineService;
    }

    public function show(Request $request){
        return response()->json($this->timelineService->createTimeline($request->couponId),200);
    }
}
