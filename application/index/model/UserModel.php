<?php

namespace model;
use think\facade\Request;

class UserModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'user';
    protected $guarded = [];

}