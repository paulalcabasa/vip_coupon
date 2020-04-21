<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DenominationService;

class DenominationController extends Controller
{
    private $denominationService;

    public function __construct(){
        $this->denominationService = new DenominationService;
    }

    public function show(Request $request){
        return response()->json($this->denominationService->getDenomination($request->couponId),200);
    }
}
