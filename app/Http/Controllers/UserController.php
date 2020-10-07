<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;

class UserController extends Controller
{
    //

    public function getUserDealer(){
        $users = User::where('user_type_id', 51)->select(DB::raw("
            user_id,
            email_address,
            account_name,
            first_name,
            last_name"))->get();
        return $users;
    }

    public function login(Request $request){
        $user = new User;

        $auth = $user->authenticate([
            'employee_no' => $request->email,
            'password' => $request->password
        ]);
        
        if(!empty($auth)) {
            return response()->json([
                'user_data' => $auth,
                'message' => 'Authenticated'
            ],200);
        }

        return response()->json([
            'message' => 'Unauthenticated.'
        ], 401);
        
        
    }
    
  
}
