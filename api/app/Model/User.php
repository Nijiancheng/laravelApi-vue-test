<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'pre_user';
    protected $fillable =['name','password','email','phone'];
}
