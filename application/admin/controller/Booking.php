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

         $day=$this->getDay($array1);

          $this->getNum($post['num'],$day);
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

      })->paginate(config('pagesize'));

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
public function getNum($data,$day){

    foreach ($data as $key=> $v){

        $this->saveOne($key,$v,$day);
    }

}
public function saveOne($key,$data,$day){
$id=explode(',',$key);
$id=$id['1'];
////首先增加日期
$a = EverydayModel::all();
$a=$a->groupBy('time');
$re_day=[];////预先的日期
foreach ($a as $key=> $v){
    $re_day[]=$key;
}

///删除不在日期范围内的数据
foreach ($re_day as $vv){
    if(in_array($vv,$day)){
        ////如果所选的日期已经存在表中
        continue;

    }else{
        ///不在删除
        EverydayModel::where('time',$vv)->delete();
    }
}

///增加额外的日期输
foreach ($day as $vvv){
    if(in_array($vvv,$re_day)){
        ////如果所选的日期已经存在表中
        continue;

    }else{
        ///不在增加
        $b=[];
        $b[]=['period_id'=>1,'people'=>$data,'booking_people'=>0,'time'=>$vvv,'created_at'=>date('Y-m-d',time())];
        $b[]=['period_id'=>2,'people'=>$data,'booking_people'=>0,'time'=>$vvv,'created_at'=>date('Y-m-d',time())];
        $b[]=['period_id'=>3,'people'=>$data,'booking_people'=>0,'time'=>$vvv,'created_at'=>date('Y-m-d',time())];
        $b[]=['period_id'=>4,'people'=>$data,'booking_people'=>0,'time'=>$vvv,'created_at'=>date('Y-m-d',time())];
        $b[]=['period_id'=>5,'people'=>$data,'booking_people'=>0,'time'=>$vvv,'created_at'=>date('Y-m-d',time())];
        EverydayModel::Insert($b);

    }
}


EverydayModel::where('period_id',$id)->update(['people'=>$data]);
}
public function getDay($data){
    $str_start_time=$data['start_time'];
    $str_end_time=$data['end_time'];
    $start_time=  date('Y-m-d',strtotime($str_start_time));
    $end_time=  date('Y-m-d',strtotime($str_end_time));

    $day=$this->diffBetweenTwoDays($start_time,$end_time);

    $array=[];


    for ($x=0; $x<=$day; $x++) {
        $array[]=date("Y-m-d",strtotime("$str_start_time   +$x   day"));
    }
return $array;

}
   public function diffBetweenTwoDays ($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }

}
