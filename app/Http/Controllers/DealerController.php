<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dealer;

class DealerController extends Controller
{
    public function get(){
        $dealers = Dealer::all();
        return response()->json($dealers);
    }
}
