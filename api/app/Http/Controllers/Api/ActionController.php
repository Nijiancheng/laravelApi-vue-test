<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class ActionController extends Controller
{
    public function upload(Request $request)
    {
        $fileKey = Str::random(20);
        $imageFile = $request->file('imageFile');//得到原始文件
//        $fileName = $imageFile->getClientOriginalName();//获取文件名
        $extension = $imageFile->extension();//获取文件后缀
        $newName = md5(date('YmdHis')).'.'.$extension;//拼接新名字
        $newImg = $imageFile->move('uploads/tem_images',$newName);//移动文件
        $newPath = 'http://127.0.0.1:8081/uploads/tem_images/'.$newName;

//        $path = $request->file('imageFile')->store('uploads');
//        $newName = explode('/',$path)[1];
//        $newPath = 'http://127.0.0.1:8081/'.$path;
        if(!empty($newImg)){
            $info = [
                "status"=> true,
                "msg"=>"成功",
                "data"=>[
                    "originalName"=>$newName,
                    "path"=>$newPath,
                    "fileKey"=>$fileKey,
                ],
            ];
        }else{
            $info = [
                "status"=> false,
                "msg"=>"失败",
                ];
        }

        return $info;//返回文件信息
    }

    public function getToken(Request $request)
    {

    }
}
