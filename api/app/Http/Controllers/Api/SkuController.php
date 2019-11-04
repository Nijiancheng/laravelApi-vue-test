<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Sku;

class SkuController extends Controller
{
//    获取库存信息
    public function get(Request $request){
        $id = $request->get('id');
        $model = new Sku();
        $model = $model->where('id',$id);
        $result = $model->get();
        $res = [
            'status'=>true,
            'msg'=>'成功',
            'data'=>$result,
        ];
        return $res;
    }
    //添加库存
    public function create(Request $request){
        $info = $request->all();
        if (!empty($info)){
            $result=Sku::create($info);
            if (!empty($result)){
                $res = [
                    'status'=>true,
                    'msg'=>'成功',
                    'data'=>$result,
                ];
            }else{
                $res = [
                    'status'=>false,
                    'msg'=>'失败',
                ];
            }
        }else{
            $res = [
                'status'=>false,
                'msg'=>'失败',
            ];
        }
        return $res;
    }
    //更创库存
    public function update(Request $request){
        $data = $request->all();
        $model=Sku::find($data['id']);
        if (!empty($model)){
            $result=$model->updata($data);
            if (!empty($result)){
                $res = [
                    'status'=>true,
                    'msg'=>'成功',
                ];
            }else{
                $res = [
                    'status'=>false,
                    'msg'=>'失败',
                ];
            }
        }else{
            $res = [
                'status'=>false,
                'msg'=>'失败',
            ];
        }
        return $res;
    }
    //删除库存
    public function del(Request $request){
        $id = $request->get('id');
        $model = Sku::find($id);
        if (!empty($model)){
            $model->status=0;
            $result = $model->save();
            if ($result>0){
                $res = [
                    'status'=>true,
                    'msg'=>'成功',
                ];
            }else{
                $res = [
                    'status'=>false,
                    'msg'=>'失败',
                ];
            }
        }else{
            $res = [
                'status'=>false,
                'msg'=>'失败',
            ];
        }
        return $res;
    }
}
