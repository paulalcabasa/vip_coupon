<?php

namespace App\Services;

use DB;
use App\Models\Promo;
use Carbon\Carbon;

class PromoService {

    public function getActive(){
        $promo = new Promo();
        $promos = $promo->getActive();
        return $promos;
    }
}