<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    const TIME = 604800;
    protected $table = 'pre_token';
    protected $fillable =['token','user_id','validity'];
}
