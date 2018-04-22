<?php
namespace app\admin\controller;

use model\EverydayModel;
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
   $data = ReservationModel::with('period.everyday')->paginate(1);
       $str='/admin/Booking/index';
   $data->setPath($str);

   $page=$data->render();
  // dd($data->toArray());
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

        $res =  ReservationModel::find($id)->update($array1);
        if(!$res){
            $this->error();
        }
        PeriodModel::where('re_id',$id)->where('name','第一时间')->update(['start_time'=>$post['start_time1'],'end_time'=>$post['end_time']]);

         PeriodModel::where('re_id',$id)->where('name','第二时间')->update(['start_time'=>$post['start_time2'],'end_time'=>$post['end_time2']]);

         PeriodModel::where('re_id',$id)->where('name','第三时间')->update(['start_time'=>$post['start_time3'],'end_time'=>$post['end_time3']]);

         PeriodModel::where('re_id',$id)->where('name','第四时间')->update(['start_time'=>$post['start_time4'],'end_time'=>$post['end_time4']]);

         PeriodModel::where('re_id',$id)->where('name','第五时间')->update(['start_time'=>$post['start_time5'],'end_time'=>$post['end_time5']]);

          $this->getNum($post['num']);
         //EverydayModel::where('period_id',)
         $this->success('成功');
     }
      $id=Request::get('id');
     // $id=1;
      $data=ReservationModel::with('period.everyday')->find($id);
//dd($data->toArray());
      $this->assign('data',$data);
       return  $this->fetch();
   }

   public function User(){

      $data = UserBookingModel::with('project')->with('user')->with('peroid')->where(function ($query){
          if(Request::has('keyword')){

              $query->whereHas('project',function ($query){
                  $keyword=trim(Request::get('keyword'));
                  if($keyword!='请选择小区'){
                      $query->where('project_name','like','%'.$keyword.'%');
                  }

              });
          }

      })->paginate(10);

       if(Request::has('keyword')){
           $keyword=trim(Request::get('keyword'));
           $this->assign('keyword',$keyword);
           $str='/admin/Booking/User?keyword='.$keyword.'&';
           $export_str='/admin/Booking/export?keyword='.$keyword.'&';
       }else{
           $str='/admin/Booking/User';
           $export_str='/admin/Booking/export';
       }

       $project_list=ProjectModel::all();
       $project_list=$this->getProJectList($project_list);
       $this->assign('project_list',$project_list);


       $data->setPath($str);
       $page=$data->render();
       $this->assign('export_str',$export_str);
       $this->assign('page',$page);
       $this->assign('data',$data);
       return  $this->fetch();
   }

    public function getProJectList($data){
        $data=$data->groupBy('project_name');
        $array=[];
        foreach ($data as $key=> $v){
            $array[]=$key;
        }
        return $array;
    }

    public function export(){
        $data = UserBookingModel::with('project')->with('user')->with('peroid')->where(function ($query){
            if(Request::has('keyword')){

                $query->whereHas('project',function ($query){
                    $keyword=trim(Request::get('keyword'));
                    if($keyword!='请选择小区'){
                        $query->where('project_name','like','%'.$keyword.'%');
                    }

                });
            }

        })->get();


        $export=new Export();
        $headArr=['姓名','预约小区','预约房产','预约时间','预约时段'];
        $array=$this->getArray($data);
        $export->exports($array,$headArr);

    }
public function getArray($data){
        $array=[];
        //dd($data->toArray());
       foreach ($data as $v){
           $array[]=$this->getArrayOne($v);
       }
       return $array;
}
public function getArrayOne($data){
    $array=[];
    $array['a']=$data['user']['name'];
    $array['b']=$data['project']['project_name'];
    $array['c']=$data['project']['project_name'];
    $array['d']=$data['project']['house_number'];
    $array['e']=$data['re_time'];
    $array['f']=$data['peroid']['start_time'].'至'.$data['peroid']['end_time'];
   return $array;
}
public function getNum($data){
    foreach ($data as $key=> $v){
        $this->saveOne($key,$v);
    }

}
public function saveOne($key,$data){
$id=explode(',',$key);
$id=$id['1'];
EverydayModel::where('period_id',$id)->update(['people'=>$data]);
}

}
