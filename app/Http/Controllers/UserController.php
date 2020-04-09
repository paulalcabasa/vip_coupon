<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\User;
class AuthController extends Controller
{
    //

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
        
        //return 'testapi call';
    }
}
