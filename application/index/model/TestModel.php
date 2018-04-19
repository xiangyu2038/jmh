<?php

namespace model;
use think\facade\Request;

class TestModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'test';
    protected $guarded = [];

}