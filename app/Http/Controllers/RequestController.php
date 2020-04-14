<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function store(Request $request){
        $dealer = $request->dealer_id;
        $denominations = $request->denominations;
        return response()->json([
            'dealer' => $dealer,
            'denominations' => $denominations
        ]);
    }
}
