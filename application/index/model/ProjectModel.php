<?php

namespace model;
use think\facade\Request;

class ProjectModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'project';
    protected $guarded = [];

    public function user(){

        return $this->belongsTo('model\UserModel','user_id');
    }
}