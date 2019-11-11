<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    const STATUS_YES = 1;
    const STATUS_NO = 0;
    const TYPE_FIRST = 1;
    const TYPE_SECOND =2;
    const TYPE_THIRD =3;
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
