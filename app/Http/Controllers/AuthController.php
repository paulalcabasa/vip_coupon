<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['login', 'refresh']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $user = User::where([
            ['user_name', $request->employee_no],
            ['user_pass' , $request->password]
        ])
        ->select(
            'user_id',
            'first_name',
            'middle_name',
            'last_name',
            'email_address',
            'user_type_id',
            'user_type_name',
            'user_source_id',
            'dealer_id')
        ->first();
        
        if(empty($user)){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
  
        $token = JWTAuth::fromUser($user);
        
        return response()->json([
            'access_token' => $token,
            'user' => $user
        ]); 
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json([
            'user' => auth()->user()
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
       
        $token = auth()->refresh();
        $user = JWTAuth::setToken($token)->toUser();
/* 
        $refreshed = JWTAuth::refresh(JWTAuth::getToken());
        $user = JWTAuth::setToken($refreshed)->toUser();
 */
        return response()->json([
            'access_token' => $token,
            'user' => $user
        ]);
    }

    

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }

   public function payload()
   {
        return auth()->payload();
   }
}
