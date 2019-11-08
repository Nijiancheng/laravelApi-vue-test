<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'pre_token';
    protected $fillable =['token','user_id','validity'];
}
