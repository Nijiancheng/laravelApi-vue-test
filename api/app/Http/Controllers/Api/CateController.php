<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Category;

class CateController extends Controller
{
//    获取分类信息
    public function get(Request $request)
    {
        $model = new Category();
        $perPage = $request->get('perpage') ? $request->get('perpage') : 0;
        $columns = ['*'];
        $pageName = 'page';
        $page = $request->get('page') ? $request->get('page') : 1;
        $model = $model->where('status', '!=', '0');

        if (!empty($request->get('select'))) {
            $model->where('name', 'like', '%' + $request->get('select') + '%');
        }

        $result = $model->paginate($perPage, $columns, $pageName, $page);
        return $this->success($result);
    }

//    添加分类
    public function create(Request $request)
    {
        $arr = $request->all();
        $result = Category::create($arr);
        if (!empty($result)) {
            $res = [
                'status' => true,
                'msg' => '成功'
            ];
        } else {
            $res = [
                'status' => false,
                'msg' => '失败'
            ];
        }
        return $res;
    }

//    修改分类信息
    public function update(Request $request)
    {
        if ($request->isMethod('get')) {
            $res = Category::find($request->get('id'));
            return $res;
        } elseif ($request->isMethod('post')) {
            $model = Category::find($request->get('id'));
            if (empty($model)) {
                $info = [
                    'status' => false,
                    'info' => '没有该分类',
                ];
                return $info;
            }
            $arr = $request->all();
            $result = $model->update($arr);
            if ($result > 0) {
                $info = [
                    'status' => true,
                    'msg' => '修改分类成功',
                ];
            } else {
                $info = [
                    'status' => false,
                    'msg' => '分类没有更新',
                ];
            }
            return $info;
        }
    }

//    删除分类
    public function delete(Request $request)
    {
        $model = Category::find($request->get('id'));
        $id = $request->get('id');
        if (empty($model)) {
            return $this->failed('该分类不存在');
        }
        $result = Product::where('category_id', '=', $id)->get();
        if (!empty($result)) {
            return $this->failed('分类下还有商品,不可以删除');
        }
        $model->status = 0;
        $result = $model->save();
        if ($result > 0) {
            return $this->success('分类删除成功');
        } else {
            return $this->failed('分类删除失败');
        }
    }
}

