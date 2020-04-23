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
        $csNumHtml = "";
        foreach($csNumbers as $csNum){
          $csNumHtml .= "<span class='badge mr-1 badge-info'>".$csNum->cs_number."</span>";
        }
        $csNumbers = collect($csNumbers)->pluck('cs_number')->toArray();
        array_push($denomArr,[
            'amount'          => $row->amount,
            'quantity'        => $row->quantity,
            'cs_number'       => $csNumHtml,
            'csNumbers'       => $csNumbers,
            'csNumber'        => ''
        ]);
    }
    return $denomArr;
  }
}