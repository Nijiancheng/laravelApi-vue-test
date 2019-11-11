<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Nav;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class NavController extends Controller
{
//    获取导航信息
    public function get(Request $request)
    {
        $model = new Nav();
////        按状态查询
//        if(!empty($request->get('status')||$request->get('status')==0)){
//            dump($request->get('status'));
//            $model=$model->where('status',"=",$request->get('status'));
//        }
////        按链接类型查询
//        dump($request->get('link_type'));
//        if(!empty($request->get('link_type'))){
//            $model=$model->where('link_type',"=",$request->get('link_type'));
//        }
////        按位置查询
//        if (!empty($request->get('position_id'))){
//            $model=$model->where('position_id',"=",$request->get('position_id'));
//        }

        $perPage = $request->get('perpage') ? $request->get('perpage') : 2;//默认查询2条
        $columns = ['*'];
        $pageName = 'page';
        $page = $request->get('page') ? $request->get('page') : 1;//查询页数 默认第1页

        $model = $model->where('status', "!=", Nav::STATUS_NO);
        $all = $model->paginate($perPage, $columns, $pageName, $page);
        if (!empty($arr)) {
            return $this->success($all);
        }
    }

//    修改导航信息
    public function update(Request $request)
    {
        if ($request->isMethod('get')) {
            $res = Nav::find($request->get('id'));
            return $this->success($res);
        } elseif ($request->isMethod('post')) {
            $arr = $request->all();
            $model = Nav::find($request->get('id'));
            if (!empty($arr['picture'])) {
                $path = $this->getPath($arr['picture']);
                if (!empty($path)) {
                    $arr['picture'] = $path;
                } else {
                    return $this->failed('图片已失效');
                }
            }
            $result = $model->update($arr);
            if ($result > 0) {
                return $this->success('修改成功');
            } else {
                return $this->failed('修改失败');
            }
        }

    }

//    添加导航
    public function create(Request $request)
    {
        $arr = $request->all();
        if (empty($arr['position_id'])) {
            return $this->failed('导航位置不能为空');
        }
        if (empty($arr['title']) && empty($arr['picture'])) {
            return $this->failed('导航名或标题至少有一个');
        }
        if (empty($arr['link_type'])) {
            return $this->failed('连接类型不能为空');
        }
        if (empty($arr['link_target'])) {
            return $this->failed('链接目标不能为空');
        }
        if (!empty($arr['picture'])) {
            $path = $this->getPath($arr['picture']);
            if (!empty($path)) {
                $arr['picture'] = $path;
            } else {
                return $this->failed('图片已失效');
            }
        }
        $result = Nav::create($arr);
        if (!empty($result)) {
            return $this->success('添加成功');
        } else {
            return $this->failed('导航添加失败');
        }
    }

//    删除导航
    public function delete(Request $request)
    {
        if (!empty($request->get('id'))) {
            return $this->failed('id不能为空');
        }
        $model = Nav::find($request->get('id'));
        if (empty($model)) {
            return $this->failed('导航不存在');
        }
        $model->status = 0;
        $result = $model->save();
        if ($result) {
            return $this->success('导航删除成功');
        } else {
            return $this->failed('导航删除失败');
        }
    }

    public function getPath($key)
    {
        $name = Cache::store('file')->get($key);
        if (!empty($name)) {
            $storage = Storage::disk('local');
            $storage->move('/tem_images/' . $name, '/images/' . $name);
            $path = url('/') + 'uploads/images/' . $name;
            return $path;
        } else {
            return false;
        }
    }

}
