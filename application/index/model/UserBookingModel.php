<?php

namespace model;
use think\facade\Request;

class UserBookingModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'user_booking';
    protected $guarded = [];

    public function project(){

        return $this->belongsTo('model\ProjectModel','project_id');
    }
    public function user(){

        return $this->belongsTo('model\UserModel','user_id');
    }
    public function peroid(){

        return $this->belongsTo('model\PeriodModel','re_peroid_time');
    }
}