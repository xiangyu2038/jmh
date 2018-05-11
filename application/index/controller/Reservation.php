<?php

namespace app\index\controller;

use model\ActivityModel;
use model\EvaluationModel;
use model\EverydayModel;
use model\ImgModel;
use model\Jssdk;
use model\PeriodModel;
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

        $openid='oUPo2wRf7e1SFpuyyLIc-5M46ACw';
        $data = UserModel::where('openid', $openid)->first();
        $user_id=$data->id;
        $datas = ProjectModel::where('user_id', $user_id)->get();

        $this->assign('datas',$datas);
       return $this->fetch();
    }

    public function reservationTime(){

        if(Request::isPost()){
            $user_id=Request::post('user_id');
            $project_id=Request::post('project_id');//房产id
            $re_peroid_time=Request::post('time_id');//预约时段id
            $re_time=Request::post('time');//预约那一天
            $re_id=Request::post('re_id');
            $visitors=Request::post('number');

            $array=[];
            $array['re_id']=$re_id;
            $array['re_time']=$re_time;
            $array['re_peroid_time']=$re_peroid_time;
            $array['project_id']=$project_id;
            $array['user_id']=$user_id;
            $res=   UserBookingModel ::create($array);

            ///增加一个预约人数
            $booking_people= EverydayModel::where('id',$re_peroid_time)->where('time',$re_time)->first()->booking_people;

            $booking_people=$booking_people+1;

            $booking_people= EverydayModel::where('id',$re_peroid_time)->where('time',$re_time)->update(['booking_people'=>$booking_people]);

           return json(['error_code'=>1,'msg'=>'成功']);


        }
//$openid='oUPo2wRgPOudk-bPLzwahZ1YkDkc';
        $openid=$this->getOpenId();

        $data = UserModel::where('openid', $openid)->first();

        if (!$data) {

            ///还没认证
            $this->redirect('index/index/login?url=/index/Reservation/reservationTime');
        }
        //$openid='oUPo2wRf7e1SFpuyyLIc-5M46ACw';
        $data = UserModel::where('openid', $openid)->first();
        $user_id=$data->id;
        $datas = ProjectModel::where('user_id', $user_id)->get();
        $yu_yue =  ReservationModel::with('period.everyday')->first();///预约

//dd($yu_yue->toArray());
        $this->assign('datas',$datas);
        $this->assign('yu_yue',$yu_yue);
        return $this->fetch();
    }

    public function getPeople(){
        ////获取每个预约时间的预约人数 和剩余人数

        $time=Request::get('time');
        $data = EverydayModel::where('time',$time)->with('period')->get();

       $data=$this->delaArray($data);
return json(['code'=>1,'msg'=>'成功','data'=>$data]);
    }

    public function delaArray($data){
       $array=[];
        foreach ($data as $v){
            $array[]=$this->delaArrayOne($v);
        }
        return $array;
    }
    public function delaArrayOne($data){
        $array=[];
        $array['start_time']=$data['period']['start_time'];
        $array['end_time']=$data['period']['end_time'];
        $array['people']=$data['people'];
        $array['booking_people']=$data['booking_people'];
        $array['id']=$data['id'];
        return $array;
    }


    public function dealPeriodTime($data){

        $array=[];
        foreach ($data as $v){
            foreach ($v['everyday'] as $vv){
                $array[]=$vv;
            }
        }
        dd($array);
    }

    public function getOpenId()
    {
        if(session('openid')){
            return session('openid');
        }else{
            define('WX_PATH', BASEPATH . '/vendor/WX/example/');

            require_once WX_PATH . 'WxPay.JsApiPay.php';
            $jsapi = new \JsApiPay();
            $openid = $jsapi->GetOpenid();
            session('openid', $openid);


            return $openid;
        }

        /* if(session('?name')){
             return 'oUPo2wRgPOudk-bPLzwahZ1YkDkc';
         }else{

         }*/

    }
}
