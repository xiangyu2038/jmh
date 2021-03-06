<?php

namespace model;
use think\facade\Request;

class EvaluationModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'evaluation';
    protected $guarded = [];
public function user(){
    return $this->belongsTo('model\UserModel','openid','openid');
}
public function repair(){

    return $this->belongsTo('model\RepairModel','repair_id');
}
}