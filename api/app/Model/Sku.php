<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    const STATUS_YES = 1;
    const STATUS_NO = 0;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'pre_sku';
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['original_price','status','product_id','price','quantity','sort','attr1','attr2','attr3'];
}
