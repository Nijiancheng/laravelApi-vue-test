<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
class test extends Controller
{
    public function getKey(Request $request)
    {
//        $redis = app('redis.connection');
        $key = Str::random(12);
//        $image[$key] = 'abcdefghijklmnopqrstuvwxyz';
//        $xxx = [
//            $key=>'1111111111'
//        ];
        Redis::set('img','1111');
        dump(Redis::get('img'));
//        return $key;
    }
    public function getPath(Request $request){
        $path = $request->file('imageFile')->store('uploads');
        $fileName = explode('/',$path)[1];
        $newPath = 'http://127.0.0.1:8081/'.$path;
//        $key = $request->get("key");
//        $xxx = session()->get('img');
//        $path = $xxx[$key];
        dump($path);
        dump($fileName);
//        return $xxx;
    }
}
