<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Model\Sku;
use App\Model\ProductTag;
use App\Model\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use \Exception;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $order = $request->get('order') == 'ASC' ? $request->get('order') : 'DESC';
        $perPage = $request->get('perpage') ? $request->get('perpage') : 0;
        $columns = ['*'];
        $pageName = 'page';
        $page = $request->get('page') ? $request->get('page') : 1;
        $product = Product::where('status', '!=', '0')->orderBy('id', $order)->paginate($perPage, $columns, $pageName, $page);
        if (!empty($product)) {
            return $this->success($product);
        } else {
            return $this->failed('商品获取失败');
        }
    }

    public function get(Request $request)
    {
        $model = new Product();
        if (!empty($request->get('id'))) {
            $model = $model->where('id', '=', $request->get('id'));
        }
        $product = $model->where('status', '!=', '0')->with(['tag' => function ($query) {
            $query->where('status', '!=', '0');
        }, 'sku' => function ($query) {
            $query->where('status', '!=', '0');
        }])->orderBy('id', 'DESC')->get();
        $res = [
            'status' => true,
            'mas' => '成功',
            'data' => $product,
        ];
        return $res;
    }

    public function del(Request $request)
    {
        $model = Product::find($request->get('id'));
        $model->status = '0';
        $result = $model->save;
        if (!empty($result)) {
            $res = [
                status => true,
                data => '商品删除成功',
            ];
        } else {
            $res = [
                status => false,
                data => '商品删除失败'
            ];
        }
        return $res;
    }

    public function create(Request $request)
    {
        $product = json_decode($request->getContent(), true);
        if(empty($product['category_id'])){
            return $this->failed('分类id不能为空');
        }
        if(empty($product['name'])){
            return $this->failed('分类名称不能为空');
        }
        if(empty($product['sort'])){
            return $this->failed('分类id不能为空');
        }
        if(empty($product['sku'])){
            return $this->failed('库存不能为空');
        }
        //开启事务
        DB::beginTransaction();
        try {
            //新增商品
            $productModel = Product::create(
                [
                    'category_id' => $product['category_id'],
                    'name' => $product['name'],
                    'content' => !empty($product['content'])?$product['content']:'',
                    'sort' => !empty($product['sort'])?$product['sort']:1,
                ]
            );
            if (!$productModel) {
                throw new Exception("新增失败", false);
            }
            //新增库存
            foreach ($product['sku'] as $v) {
                if(empty($v['price'])){
                    throw new Exception("库存售价不能为空", false);
                }
                if(empty($v['price'])){
                    throw new Exception("库存售价不能为空", false);
                }
                if(empty($v['quantity']) || $v['quantity']<0){
                    throw new Exception("库存数不能小于0", false);
                }
                $sku = Sku::create(
                    [
                        'product_id' => $productModel->id,
                        'original_price' => !empty($v['original_price'])?$v['original_price']:'',
                        'price' => $v['price'],
                        'attr1' => !empty($v['attr1'])?$v['attr1']:'',
                        'attr2' => !empty($v['attr2'])?$v['attr2']:'',
                        'attr3' => !empty($v['attr3'])?$v['attr3']:'',
                        'quantity' => $v['quantity'],
                        'sort' => $v['sort']
                    ]
                );
                if (!$sku) {
                    throw new Exception("新增失败", false);
                }
            }
            //新增标签
            foreach ($product['tag'] as $val) {
                if(empty($v['type'])){
                    throw new Exception("标签类型不能为空", false);
                }
                if ($val['type'] == 3) {
                    $path = $this->getPath($val['fileKey']);
                    if($path){
                        $val['value'] = $path;
                    }else{
                        throw new Exception("图片地址已失效", false);
                    }
                }
                $tag = ProductTag::create(
                    [
                        'product_id' => $productModel->id,
                        'type' => $val['type'],
                        'value' => $val['value']
                    ]
                );
                if (!$tag) {
                    throw new Exception("新增失败", false);
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
                'status' => false,
                'msg' => $e->getMessage()
            ];
        }
    }

    public function update(Request $request)
    {
        $product = json_decode($request->getContent(), true);
        if(empty($product['id'])){
            return $this->failed('商品id不能为空');
        }
        //开启事务
        DB::beginTransaction();
        try {
            //修改商品
            $model1 = Product::find($product['id']);
            if(empty($model1)){
                return $this->failed('该商品id不存在');
            }
            $data =[];
            if(!empty($product['category_id'])){
                $data[ 'category_id'] = $product['category_id'];
            }
            if(!empty($product['name'])){
                $data[ 'name'] = $product['name'];
            }
            if(!empty($product['content'])||$product['content']==''){
                $data[ 'content'] = $product['content'];
            }
            if(!empty($product['sort'])){
                $data[ 'sort'] = $product['sort'];
            }
            if(!empty($product['status'])||$product['status']==0){
                $data[ 'status'] = $product['status'];
            }
            $productModel = $model1->update($data);
            if (!$productModel) {
                throw new Exception("新增失败", false);
            }
            //修改库存
            if (sizeof($product['sku']) < 1) {
                throw new Exception("库存不能空", false);
            }
            Sku::where('product_id', '=', $product['id'])->update(['status' => 0]);
            foreach ($product['sku'] as $v) {
                $skuData =[];
                if (!empty($v["id"])) {
                    $model2 = Sku::find($v["id"]);
                    if(empty($model2)){
                        throw new Exception("库存不存在", false);
                    }
                    if(!empty($v['category_id'])){
                        if ($v['category_id']>0){
                            $skuData[ 'category_id'] = $v['category_id'];
                        }else{
                            throw new Exception("分类id不存在", false);
                        }
                    }
                    if(!empty($v['price'])){
                        if ($v['price']>0){
                            $skuData[ 'price'] = $v['price'];
                        }else{
                            throw new Exception("售价不能小于0", false);
                        }
                    }
                    if(!empty($v['attr1'])){
                            $skuData[ 'attr1'] = $v['attr1'];
                    }
                    if(!empty($v['attr2'])){
                        $skuData[ 'attr2'] = $v['attr2'];
                    }
                    if(!empty($v['attr3'])){
                        $skuData[ 'attr3'] = $v['attr3'];
                    }
                    if(!empty($v['quantity'])){
                        if ($v['quantity']>0){
                            $skuData[ 'quantity'] = $v['quantity'];
                        }else{
                            throw new Exception("库存不能小于0", false);
                        }
                    }
                    if(!empty($v['sort'])){
                        if ($v['sort']>0){
                            $skuData[ 'sort'] = $v['sort'];
                        }else{
                            throw new Exception("排序值不能小于0", false);
                        }
                    }
                    $skuData[ 'status'] = 1;
                    $sku = $model2->update($skuData);
                } else {
                    if(empty($v['price'])){
                        throw new Exception("库存售价不能为空", false);
                    }
                    if(empty($v['price'])){
                        throw new Exception("库存售价不能为空", false);
                    }
                    if(empty($v['quantity']) || $v['quantity']<0){
                        throw new Exception("库存数不能小于0", false);
                    }
                    $sku = Sku::create(
                        [
                            'product_id' => $productModel->id,
                            'original_price' => !empty($v['original_price'])?$v['original_price']:'',
                            'price' => $v['price'],
                            'attr1' => !empty($v['attr1'])?$v['attr1']:'',
                            'attr2' => !empty($v['attr2'])?$v['attr2']:'',
                            'attr3' => !empty($v['attr3'])?$v['attr3']:'',
                            'quantity' => $v['quantity'],
                            'sort' => $v['sort']
                        ]
                    );
                    if (!$sku) {
                        throw new Exception("新增失败", false);
                    }
                }
                if (!$sku) {
                    throw new Exception("修改库存失败", false);
                }
            }
            //修改标签
            if (!empty($product['tag'])) {
                ProductTag::where('product_id', '=', $product['id'])->update(['status' => 0]);
                foreach ($product['tag'] as $val) {
                    if ($val['id'] < 0) {
                        if ($val['type'] == 3) {
                            $path = $this->getPath($val['fileKey']);
                            if($path){
                                $val['value'] = $path;
                            }else{
                                throw new Exception("图片地址已失效", false);
                            }
                        }
                        $tag = ProductTag::create(['product_id' => $val['product_id'], 'type' => $val['type'], 'value' => $val['value'], 'status' => 1]);
                    } else {
                        $model3 = ProductTag::find($val['id']);
                        if($val['type'] == 3){
                            $tag = $model3->update(['status' => 1,]
                            );
                        }else{
                            $tag = $model3->update(
                                [
                                    'value' => $val['value'],
                                    'status' => 1,
                                ]
                            );
                        }
                    }
                    if (!$tag) {
                        throw new Exception("新增失败", false);
                    }

                }
            }
            DB::commit();
            return $this->success('编辑成功');
        } catch (Exception $e) {
            //接收异常处理并回滚
            DB::rollBack();
//            $msg=$e->getMessage();
//            return $this->failed($e->getMessage());
            return [
                'status'=>false,
                'msg'=>$e->getMessage(),
            ];
        }
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
