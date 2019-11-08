<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\User;
class CheckLoginInfo
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
        if(empty($request->get('name'))||empty($request->get('password'))){
            return ['status'=>false,'msg'=>'缺少参数'];
        }else{
            $name = User::where('name','=',$request->get('name'))->first();
            if(empty($name)){
                return ['status'=>false,'msg'=>'用户不存在'];
            }
        }
        return $next($request);
    }
}
