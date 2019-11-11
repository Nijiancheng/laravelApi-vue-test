<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const STATUS_YES = 1;
    const STATUS_NO = 0;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'pre_product';

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['category_id', 'name', 'content', 'sort', 'status'];

    public function tag()
    {
        return $this->hasMany('App\Model\ProductTag');
    }

    public function sku()
    {
        return $this->hasMany('App\Model\Sku');
    }
}
