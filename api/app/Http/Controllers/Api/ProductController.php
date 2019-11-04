<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Model\Sku;
use App\Model\ProductTag;
use App\Model\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{
    public function index(Request $request){
        $perPage = $request->get('perpage')?$request->get('perpage'):0;
        $columns = ['*'];
        $pageName = 'page';
        $page = $request->get('page')?$request->get('page'):1;
        $product = Product::where('status','!=','0')->paginate($perPage,$columns,$pageName,$page);
        if(!empty($product)){
            $res =[
                'status'=>true,
                'msg'=>"成功",
                "data"=>$product
            ];
        }else{
            $res =[
                'status'=>false,
                'msg'=>"失败",
            ];
        }
        return $res;
    }
    public function get(Request $request)
    {
        $model = new Product();
        if(!empty($request->get('id'))){
            $model = $model->where('id','=',$request->get('id'));
        }
        $product = $model->where('status','!=','0')->with('tag','sku')->get();
        $res =[
            'status'=>true,
            'mas'=>'成功',
            'data'=>$product,
        ];
        return $res;
    }

    public function del(Request $request){
        $model = Product::find($request->get('id'));
        $model->status = '0';
        $result = $model->save;
        if(!empty($result)){
            $res =[
                status=>true,
                data=>'商品删除成功',
            ];
        }else{
            $res=[
                status=>false,
                data=>'商品删除失败'
            ];
        }
        return $res;
    }
    public function create (Request $request){

        $product= json_decode($request->getContent(),true);

        //开启事务
        DB::beginTransaction();
        try {
            //新增商品
            $productModel = Product::create(
                [
                    'category_id' => $product['category_id'],
                    'name' => $product['name'],
                    'content' => $product['content'],
                    'sort' => $product['sort']
                ]
            );
            if (!$productModel) {
                throw new \Exception("新增失败", false);
            }
            //新增库存
            foreach ($product['sku'] as $v) {
                $sku = Sku::create(
                    [
                        'product_id' => $productModel->id,
                        'original_price' => $v['original_price'],
                        'price' => $v['price'],
                        'attr1' => $v['attr1'],
                        'attr2' => $v['attr2'],
                        'attr3' => $v['attr3'],
                        'quantity' => $v['quantity'],
                        'sort' => $v['sort']
                    ]
                );
                if (!$sku) {
                    throw new \Exception("新增失败", false);
                }
            }
            //新增标签
            foreach ($product['tag'] as $val) {
                $tag = ProductTag::create(
                    [
                        'product_id' => $productModel->id,
                        'type' => $val['type'],
                        'value' => $val['value']
                    ]
                );
                if (!$tag) {
                    throw new \Exception("新增失败", false);
                }
            }
            DB::commit();
            return [
                'status' => true,
                'data' => 1
            ];
        } catch (Exception $e) {
            //接收异常处理并回滚
            DB::rollBack();
            return [
                'status' => $e->getCode(),
                'msg' => $e->getMessage()
            ];
        }
    }
     public function update(Request $request){
         $product= json_decode($request->getContent(),true);
//         return $product['id'];
         //开启事务
         DB::beginTransaction();
         try {
             //修改商品
             $model1 = Product::find($product['id']);
             $productModel = $model1->update(
                 [
                     'category_id' => $product['category_id'],
                     'name' => $product['name'],
                     'content' => $product['content'],
                     'sort' => $product['sort'],
                     'status'=>$product['status'],
                 ]
             );
             if (!$productModel) {
                 throw new \Exception("新增失败", false);
             }
             //修改库存
             foreach ($product['sku'] as $v) {
                 $model2 = Sku::find($v["id"]);
                 $sku = $model2->update(
                     [
                         'original_price' => $v['original_price'],
                         'price' => $v['price'],
                         'attr1' => $v['attr1'],
                         'attr2' => $v['attr2'],
                         'attr3' => $v['attr3'],
                         'quantity' => $v['quantity'],
                         'sort' => $v['sort']
                     ]
                 );
                 if (!$sku) {
                     throw new \Exception("修改库存失败", false);
                 }
             }
             //修改标签
             if(!empty($product['tag'])){
                 foreach ($product['tag'] as $val) {
                     $model3 = ProductTag::find($val['id']);
                     $tag = $model3->update(
                         [
                             'type' => $val['type'],
                             'value' => $val['value']
                         ]
                     );
                     if (!$tag) {
                         throw new \Exception("新增失败", false);
                     }
                 }
             }
             DB::commit();
             return [
                 'status' => true,
                 'msg' => '成功'
             ];
         } catch (Exception $e) {
             //接收异常处理并回滚
             DB::rollBack();
             return [
                 'status' => $e->getCode(),
                 'msg' => $e->getMessage()
             ];
         }
     }
}
