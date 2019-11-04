<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'pre_product_tag';
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['product_id','type','value','status'];
}
