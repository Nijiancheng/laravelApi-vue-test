<?php

namespace App\Http\Middleware;

use Closure;

class EnableCrossRequestMiddleware
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
        $response = $next($request);
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Headers', 'Origin,No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With, token');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
        $response->header('Access-Control-Allow-Credentials', 'true');




//        $response = $next($request);
//        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
//        $allow_origin = [
//            'http://localhost:8080',
//            'http://localhost:8081',
//            'http://127.0.0.1:8080',
//            'http://127.0.0.1:8081',
//        ];
//        if (in_array($origin, $allow_origin)) {
//            $response->header('Access-Control-Allow-Origin', '*');
//            $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie,x-requested-with, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
//            $response->header('Access-Control-Expose-Headers', 'Authorization, authenticated');
//            $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
//            $response->header('Access-Control-Allow-Credentials', 'false');
//        }
        return $response;
    }
}
