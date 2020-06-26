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

    public function get(){
        $purpose = new Purpose;
        return $purpose->get();
    }

    public function createPurpose($request){
        $purpose = new Purpose;
        $user = auth()->user();
        $purpose->purpose = $request->purpose['purpose'];
        $purpose->status = $request->purpose['status'];
        $purpose->require_cs_no_flag = $request->purpose['require_cs_no_flag'];
        $purpose->created_by = $user->user_id;
        $purpose->create_user_source = $user->user_source_id;
        $purpose->created_at = Carbon::now();
        $purpose->save();
        return [    
            'message' => $request->purpose['purpose'] . ' has been created.',
            'purposes' => $purpose->get()
        ];
    }

    public function updatePurpose($request){
        $purpose = Purpose::find($request->purpose['id']);
        $user = auth()->user();
        $purpose->purpose = $request->purpose['purpose'];
        $purpose->status = $request->purpose['status'];
        $purpose->require_cs_no_flag = $request->purpose['require_cs_no_flag'];
        $purpose->created_by = $user->user_id;
        $purpose->create_user_source = $user->user_source_id;
        $purpose->created_at = Carbon::now();
        $purpose->save();
     
        return [
            'message' => $request->purpose['purpose'] . ' has been updated.',
            'purposes' => $purpose->get()
        ];
    }
}