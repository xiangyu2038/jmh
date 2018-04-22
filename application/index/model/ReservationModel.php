<?php

namespace model;
use think\facade\Request;

class ReservationModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'reservation';
    protected $guarded = [];

    public function period(){
        return $this->hasMany('model\PeriodModel','re_id');
    }

}