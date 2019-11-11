<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Sku;

class SkuController extends Controller
{
//    获取库存信息
    public function get(Request $request)
    {
        if (empty($request->get('id'))) {
            return $this->failed('请传入库存id');
        }
        $id = $request->get('id');
        $model = new Sku();
        $model = $model->where('id', $id);
        $result = $model->get();
        return $this->success($result);
    }

    //添加库存
    public function create(Request $request)
    {
        $info = $request->all();
        if (empty($info['product_id'])) {
            return $this->failed('请传入商品id');
        }
        if (empty($info['price'])) {
            return $this->failed('请传入售价');
        }
        if (empty($info['attr1'])) {
            return $this->failed('至少需要一个属性');
        }

        $result = Sku::create($info);
        if (!empty($result)) {
            return $this->success($result);
        } else {
            return $this->failed('添加失败');
        }

        return $this->failed('缺少参数');

    }

    //更创库存
    public function update(Request $request)
    {
        $data = $request->all();
        if (empty($data['id'])) {
            return $this->failed('请传入库存id');
        }
        $model = Sku::find($data['id']);
        if (empty($model)) {
            return $this->failed('该库存不存在');
        }
        $result = $model->updata($data);
        if (empty($result)) {
            return $this->failed('更新失败');
        }
        return $this->success('更新成功');
    }

    //删除库存
    public function delete(Request $request)
    {
        if (empty($request->get('id'))) {
            return $this->failed('请传入库存id');
        }
        $id = $request->get('id');
        $model = Sku::find($id);
        if (empty($model)) {
            return $this->failed('该库存不存在');
        }
        $sku = Sku::where('product_id', '=', $model->product_id)->where('status', '!=', 0)->get();
        if (sizeof($sku) <= 1 && $sku[0]->id == $id) {
            return $this->failed('库存不能为空');
        }
        $model->status = 0;
        $result = $model->save();
        if ($result > 0) {
            return $this->success('删除成功');
        } else {
            return $this->failed('删除失败');
        }

    }
}
