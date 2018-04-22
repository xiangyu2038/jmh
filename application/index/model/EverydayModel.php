<?php

namespace model;
use think\facade\Request;

class EverydayModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'every_day';
    protected $guarded = [];
public function period(){

    return $this->belongsTo('model\PeriodModel','period_id');
}
}