<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PromoService;


class PromoController extends Controller
{
    public function getActive(){
        $promoService = new PromoService;
        return response()->json($promoService->getActive());
    }
}
