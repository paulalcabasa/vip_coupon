<?php

namespace App\Services;
use DB;
use Carbon\Carbon;
use App\Models\Timeline;


class TimelineService {

  public function createTimeline($couponId){
    $timeline = new Timeline;
    $timelineData = $timeline->getByCoupon($couponId);
    $timelineArr = [];
    foreach($timelineData as $row){
        array_push($timelineArr,[
            'text' => '<strong>' . $row->created_by . '</strong> has <strong>' . strtolower($row->action) . '</strong> the coupon ' . Carbon::parse($row->created_at)->diffForHumans() . '.' 
        ]);
    }
    return $timelineArr;
  }

  
  
}