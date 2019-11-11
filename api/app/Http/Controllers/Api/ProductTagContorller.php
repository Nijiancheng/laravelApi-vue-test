<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\ProductTag;

class ProductTagContorller extends Controller
{
    public function get(Request $request)
    {
        $id = $request->get('id');
        $model = new ProductTag();
        $model = $model->where('id', $id);
        $result = $model->all();
        $res = [
            'status' => true,
            'msg' => '成功',
            'data' => $result,
        ];
        return $res;
    }

    //添加库存
    public function create(Request $request)
    {
        $info = $request->all();
        $model = ProductTag::find($info['id']);
        if (!empty($model)) {
            $result = $model->create($info);
            if (!empty($result)) {
                $res = [
                    'status' => true,
                    'msg' => '成功',
                ];
            } else {
                $res = [
                    'status' => false,
                    'msg' => '失败',
                ];
            }
        } else {
            $res = [
                'status' => false,
                'msg' => '失败',
            ];
        }
        return $res;
    }

    //更创库存
    public function update(Request $request)
    {
        $data = $request->all();
        $model = ProductTag::find($data['id']);
        if (!empty($model)) {
            $result = $model->updata($data);
            if (!empty($result)) {
                $res = [
                    'status' => true,
                    'msg' => '成功',
                ];
            } else {
                $res = [
                    'status' => false,
                    'msg' => '失败',
                ];
            }
        } else {
            $res = [
                'status' => false,
                'msg' => '失败',
            ];
        }
        return $res;
    }

    //删除库存
    public function del(Request $request)
    {
        $id = $request->get('id');
        $model = ProductTag::find($id);
        if (!empty($model)) {
            $model->status = 0;
            $result = $model->save();
            if ($result > 0) {
                $res = [
                    'status' => true,
                    'msg' => '成功',
                ];
            } else {
                $res = [
                    'status' => false,
                    'msg' => '失败',
                ];
            }
        } else {
            $res = [
                'status' => false,
                'msg' => '失败',
            ];
        }
        return $res;
    }
}
