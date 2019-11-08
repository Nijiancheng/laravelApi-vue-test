<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Token;
use App\Model\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function getInfo(Request $request)
    {
        if (!empty($request->get('token'))) {
            $token = $request->get('token');
            $tokenInfo = Token::where('token', '=', $token)->first();
            if (!empty($tokenInfo)) {
                $time = strtotime($tokenInfo->validity);
                if ($time > time()) {
                    return $request;
                } else {

//                    $tokenInfo->delete();
                    dump($time,time(),$tokenInfo->validity);
                    return ['status' => false, 'msg' => 'token已过期', 'code' => 402];
                }
            } else {
                return ['status' => false, 'msg' => '账号未登录', 'code' => 402];
            }
        } else {
            return ['status' => false, 'msg' => '缺少token', 'code' => 402];
        }
    }
}
