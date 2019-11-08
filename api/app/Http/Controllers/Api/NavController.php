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
    public function get(Request $request){
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

        $perPage = $request->get('perpage')?$request->get('perpage'):2;
        $columns = ['*'];
        $pageName = 'page';
        $page = $request->get('page')?$request->get('page'):1;

        $model=$model->where('status',"!=",0);
        $all = $model->paginate($perPage,$columns,$pageName,$page);
        $res = [
            'status'=>true,
            'msg'=>"成功",
            "data"=>$all,
        ];
        return $res;

    }
//    修改导航信息
    public function update(Request $request){
       if($request->isMethod('get')){
           $res = Nav::find($request->get('id'));
           $info=[
               'status'=>true,
               'msg'=>'成功',
               "data"=>$res,
           ];
           return $info;
       }elseif($request->isMethod('post')){
           $arr = $request->all();
           $model = Nav::find($request->get('id'));
           if(!empty($arr['picture'])){
               $path = $this->getPath($arr['picture']);
               if(!empty($path)){
                   $arr['picture']=$path;
               }else{
                   return $this->failed('图片已失效');
               }
           }
            $result = $model->update($arr);
            if($result>0){
                $res = [
                    'status'=>true,
                    'msg'=>'修改成功'
                ];
            }else{
                $res =[
                    'status'=>false,
                    'msg'=>'修改失败'
                ];
            }
            return $res;
       }

    }
//    添加导航
    public function create(Request $request){
        $arr = $request->all();
//        dump($arr);
        if(!empty($arr['picture'])){
            $path = $this->getPath($arr['picture']);
            if(!empty($path)){
                $arr['picture']=$path;
            }else{
                return $this->failed('图片已失效');
            }
        }
        $result = Nav::create($arr);
//        dump($result);
        if(!empty($result)){
            $res=[
                'status'=>true,
                'msg'=>'添加成功'
            ];
        }else{
            $res=[
                'status'=>false,
                'msg'=>'添加失败'
            ];
        }
        return $res;
    }
//    删除导航
    public function delete(Request $request){
        $model = Nav::find($request->get('id'));
        $model->status = 0;
        $result = $model->save();

        if($result){
            $res =[
                "status"=>true,
                "msg"=>'导航删除成功',
            ];
        }else{
            $res=[
                "status"=>false,
                "msg"=>'导航删除失败'
            ];
        }
        return $res;
    }
    public function getPath($key)
    {
        $name = Cache::store('file')->get($key);
//        $newImg = $imageFile->move('uploads/tem_images',$newName);//移动文件
        if(!empty($name)){
            $storage = Storage::disk('local');
            $storage->move('/tem_images/'.$name,'/images/'.$name);
            $path = 'http://127.0.0.1:8081/uploads/images/'.$name;
            return $path;
        }else{
            return false;
        }

    }

}
