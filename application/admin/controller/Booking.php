<?php
namespace app\admin\controller;

use model\Export;
use model\Pages;
use model\PeriodModel;
use model\ProjectModel;
use model\ReservationModel;
use model\UserBookingModel;
use model\UserModel;
use think\Controller;
use think\facade\Request;

class Booking extends BaseController
{
   public function index(){
   $data = ReservationModel::paginate(1);



   $str='/admin/Booking/index';
   $data->setPath($str);
   $page=$data->render();
   $this->assign('page',$page);
   $this->assign('data',$data);
       return  $this->fetch();
   }

   public function edit(){
     if(Request::isPost()){
         $post=Request::post();
          $array1=[];
          $array1['start_time']=$post['start_time'];
          $array1['end_time']=$post['end_time'];
          $array1['area']=$post['area'];
          $array1['object']=$post['object'];
          $array1['detail']=$post['detail'];
          $id=$post['id'];
          //dd($post);
        $res =  ReservationModel::find($id)->update($array1);
        if(!$res){
            $this->error();
        }
        PeriodModel::where('re_id',$id)->where('name','第一时间')->update(['start_time'=>$post['start_time1'],'end_time'=>$post['end_time']]);

         PeriodModel::where('re_id',$id)->where('name','第二时间')->update(['start_time'=>$post['start_time2'],'end_time'=>$post['end_time2']]);

         PeriodModel::where('re_id',$id)->where('name','第三时间')->update(['start_time'=>$post['start_time3'],'end_time'=>$post['end_time3']]);

         PeriodModel::where('re_id',$id)->where('name','第四时间')->update(['start_time'=>$post['start_time4'],'end_time'=>$post['end_time4']]);

         PeriodModel::where('re_id',$id)->where('name','第五时间')->update(['start_time'=>$post['start_time5'],'end_time'=>$post['end_time5']]);

         $this->success('成功');
     }
      $id=Request::get('id');
     // $id=1;
      $data=ReservationModel::with('period')->find($id);

      $this->assign('data',$data);
       return  $this->fetch();
   }

   public function User(){

      $data = UserBookingModel::with('project')->with('user')->with('peroid')->paginate(10);

//dd($data->toArray());
       $str='/admin/Booking/User';
       $data->setPath($str);
       $page=$data->render();
       $this->assign('page',$page);
       $this->assign('data',$data);
       return  $this->fetch();
   }


}
