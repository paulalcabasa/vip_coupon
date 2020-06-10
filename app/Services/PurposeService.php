<?php

namespace App\Services;

use DB;
use App\Models\Purpose;
use Carbon\Carbon;

class PurposeService {

    public function getActive(){
        $purpose = new Purpose;
        return $purpose->getActive();
    }
}