<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use Illuminate\Http\Request;
use App\Model\User;
use App\Model\Token;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public  function login(UserRequest $request){
        $user = $request->all();
        if (empty($user['name'])){
            return $this->failed('用户名不能为空');
        }
        if(empty($user['password'])){
            return $this->failed('用户密码不能为空');
        }
        $info = User::where('name','=',$user['name'])->first();
        //判断密码是否正确
        if(decrypt($info->password) != $user['password']){
            return $this->failed('账户密码不匹配');
        }
        $tokenInfo = Token::where('user_id','=',$info->id)->first();
        if(!empty($tokenInfo)){
            $time =strtotime($tokenInfo->validity);
            if($time>time()){
                $res = [
                    'token'=>$tokenInfo->token,
                    'id'=>$info->id,
                    'name'=>$info->name,
                ];
                return $this->success($res);
            }else{
                $tokenInfo->delete();
                $token = Str::random(32);
                $validity = date('Y-m-d H-i-s',strtotime("+1 week"));
                $result = Token::create(['token'=>$token,'user_id'=>$info->id,'validity'=>$validity]);
                if(empty($result)){
                    return $this->failed('登录失败');
                }
                $res = [
                    'token'=>$token,
                    'id'=>$info->id,
                    'name'=>$info->name,
                ];
                return $this->success($res);
            }
        } else{
            $token = Str::random(32);
            $validity = date('Y-m-d H-i-s',strtotime("+1 week"));
            $result = Token::create(['token'=>$token,'user_id'=>$info->id,'validity'=>$validity]);
            if(empty($result)){
                return $this->failed('登录失败');
            }
            $res = [
                'token'=>$token,
                'id'=>$info->id,
                'name'=>$info->name,
            ];
            return $this->success($res);
        }
    }

//    public function register(UserRequest $request){
//        $user = $request->all();
//        $password = encrypt($user['password']);
//        $name = $user['name'];
//        $result = User::create(['name'=>$name,'password'=>$password,'email'=>$user['email']]);
//        if(!empty($result)){
//            $id = $result->id;
//            $token = Str::random(32);
//            $validity = date('Y-m-d H-i-s',strtotime("+1 week"));
//            $result = Token::create(['token'=>$token,'user_id'=>$result->id,'validity'=>$validity]);
//           if($result){
//               $res =['token'=>$token,'id'=>$id];
//               return $this->success($res);
//           }else{
//               return $this->failed('token有误,请重新登录');
//           }
//        }else{
//            return $this->failed('注册失败');
//        }
//    }
//    public function logout(Request $request){
//
//    }

}
