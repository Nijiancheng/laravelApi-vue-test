<?php

namespace App\Http\Middleware;

use App\Model\Token;
use Closure;
use Illuminate\Support\Facades\Date;
class CheckToken
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
        if (!empty($request->get('token'))){
            $token = $request->get('token');
            $tokenInfo = Token::where('token','=',$token)->first();
            if(!empty($tokenInfo)){
                $time =strtotime($tokenInfo->validity);

//                $time =strtotime($tokenInfo->created_at).Token::TIME;

                if($time > time()){
                    return $next($request);
                }else{
                    $tokenInfo->delete();
                    $res = array('status'=>false,'code' =>402,'msg' => 'token已过期');
                    return response()->json($res);
                }
            }else{
                $res = array('status'=>false,'code' =>402,'msg' => '账号未登录');
                return response()->json($res);
            }
        }else{
            $res = array('status'=>false,'code' =>402,'msg' => '缺少token');
            return response()->json($res);
        }

    }
}
