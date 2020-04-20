<?php

namespace App\Services;
use DB;
use Carbon\Carbon;
use App\Models\Denomination;
use App\Models\CSNumber;

class DenominationService {

  public function getDenomination($couponId){
    $denomination = new Denomination;
    $csNumber = new CSNumber;
    $denominationData = $denomination->getByCoupon($couponId);
    $denomArr = [];
    foreach($denominationData as $row){
        $csNumbers = $csNumber->getByDenomination($row->id);
        array_push($denomArr,[
            'denomination_id' => $row->denomination_id,
            'amount'   => $row->amount,
            'quantity' => $row->quantity,
            'cs_number' => $csNumbers
        ]);
    }
    return $denomArr;
  }
}