<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {  

      
        $domains = [
            'http://localhost:8080',
            'http://localhost:8000',
            'http://localhost/vip_coupon',
            'http://idh.isuzuphil.com/vip_coupon',
            'http://ecommerce4/vip_coupon'
        ];

        if(isset($request->server()['HTTP_ORIGIN'])) {
            $origin = $request->server()['HTTP_ORIGIN'];
            if(in_array($origin,$domains)){
                header('Access-Control-Allow-Origin: ' . $origin);
            }
            header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');
            header('Access-Control-Allow-Methods: *');
         //   header('Access-Control-Allow-Headers: GET, POST, PUT, DELETE, OPTIONS');
        }
  /*        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS'); */
        return $next($request);
    }
}
