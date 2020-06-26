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

    public function index(){
        $promoService = new PromoService;
        return response()->json($promoService->getPromos());
    }

    public function store(Request $request){
        $promoService = new PromoService;
        return response()->json($promoService->createPromo($request));
    }

    public function update(Request $request){
        $promoService = new PromoService;
        return response()->json($promoService->updatePromo($request));
    }
}
