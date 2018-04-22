<?php

namespace model;
use think\facade\Request;

class PeriodModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'period';
    protected $guarded = [];
public function everyday(){
    return $this->hasMany('model\EverydayModel','period_id');
}

}