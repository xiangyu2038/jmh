<?php

namespace model;
use think\facade\Request;

class SuggestionModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'suggestion';
    protected $guarded = [];
    public function user(){
        return $this->belongsTo('model\UserModel','openid','openid');
    }
    public function img(){

        return $this->hasMany('model\ImgModel','repair_id');
    }
}