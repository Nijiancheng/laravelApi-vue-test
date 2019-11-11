<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Nav extends Model
{
    const STATUS_YES = 1;
    const STATUS_NO = 0;
//    const POSITION_FIRST = 0,


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
