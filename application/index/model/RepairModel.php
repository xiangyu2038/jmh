<?php

namespace model;
use think\facade\Request;

class RepairModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'repaiir';
    protected $guarded = [];
public function user(){
    return $this->belongsTo('model\UserModel','user_id');
}
public function img(){

    return $this->hasMany('model\ImgModel','repair_id');
}
public function project(){
    return $this->belongsTo('model\ProjectModel','project_id');
}
}