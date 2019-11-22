<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


class ActionController extends Controller
{
    public function upload(Request $request)
    {
        $fileKey = Str::random(20);
        $imageFile = $request->file('imageFile');//得到原始文件
//        $fileName = $imageFile->getClientOriginalName();//获取文件名
        $extension = $imageFile->extension();//获取文件后缀
        $newName = Str::random(15) . '.' . $extension;//拼接新名字
        $newImg = $imageFile->move('uploads/tem_images', $newName);//移动文件
        $newPath = url('/') .'/uploads/tem_images/' . $newName;
        if (empty($newImg)) {
            return $this->failed('文件上传失败');
        }
        Cache::store('file')->put($fileKey, $newName, 600);
        $data = [
            "originalName" => $newName,
            "path" => $newPath,
            "fileKey" => $fileKey,
        ];
        return $this->success($data);
    }
}
