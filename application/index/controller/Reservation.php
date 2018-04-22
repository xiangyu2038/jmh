<?php

namespace app\index\controller;

use model\ActivityModel;
use model\EvaluationModel;
use model\ImgModel;
use model\Jssdk;
use model\ProjectModel;
use model\RepairModel;
use model\ReservationModel;
use model\SuggestionModel;
use model\TestModel;
use model\UserBookingModel;
use model\UserModel;
use think\Cache;
use think\Controller;
use think\facade\Request;
use think\Session;

class Reservation extends Controller
{
    public function index(){

        //$openid=$this->getOpenId();
         $openid='oUPo2wRgPOudk-bPLzwahZ1YkDkc';
        $data = UserModel::where('openid', $openid)->first();
        $user_id=$data->id;
        $datas = ProjectModel::where('user_id', $user_id)->get();

        $this->assign('datas',$datas);
       return $this->fetch();
    }

    public function reservationTime(){

        if(Request::isPost()){
            $user_id=1;
            $project_id=1;//房产id
            $re_peroid_time=1;//预约时段id
            $re_time='2018-04-22';//预约时间
            $re_id=1;

            $array=[];
            $array['re_id']=$re_id;
            $array['re_time']=$re_time;
            $array['re_peroid_time']=$re_peroid_time;
            $array['project_id']=$project_id;
            $res=   UserBookingModel    ::create($array);
            dd('ok');


        }

        //$openid=$this->getOpenId();
        $openid='oUPo2wRgPOudk-bPLzwahZ1YkDkc';
        $data = UserModel::where('openid', $openid)->first();
        $user_id=$data->id;
        $datas = ProjectModel::where('user_id', $user_id)->get();
        $yu_yue =  ReservationModel::with('period')->first();///预约
//dd($yu_yue->toArray());
        $this->assign('datas',$datas);
        $this->assign('yu_yue',$yu_yue);
        return $this->fetch();
    }

}
