<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Nav extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'pre_nav';
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['position_id','title','picture','link_type','link_target','status'];
}
