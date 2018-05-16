<?php
namespace app\admin\controller;

use model\Export;
use model\ProjectModel;
use model\RepairModel;
use think\Controller;
use think\facade\Request;

class Repair extends Controller
{
public function index(){
   // $keyword=trim(Request::get('pro'));

    $datas= RepairModel::with('user')->with('project')->with('img')->where(function ($query){
        if(Request::has('con')){
            $con=Request::get('con');
            $keyword=trim(Request::get('keyword'));
            if($con=='name'){
                $query->where('contacts_name','like','%'.$keyword.'%')->orderBy('created_at','desc');
            }elseif ($con=='phone'){
                $query->where('contacts_phone','like','%'.$keyword.'%')->orderBy('created_at','desc');
            }
        }
    })->whereHas('project',function ($query){

        if(Request::has('pro')&&(Request::get('pro')!='请选择小区')){

            $keyword=trim(Request::get('pro'));

            if($keyword!='请选择小区'){
    $query->where('project_name',$keyword);
}

        }
    })->orderBy('created_at','desc')->paginate(config('pagesize'));
    $par=[];
    if(Request::has('pro')&&(Request::get('pro')!='请选择小区')){
        $pro=trim(Request::get('pro'));
        $this->assign('pro',$pro);
        $par['pro']=$pro;
    }
    if(Request::has('con')){
        $con=Request::get('con');
        $keyword=trim(Request::get('keyword'));
        $this->assign('con',$con);
        $this->assign('keyword',$keyword);

        $par['con']=$con;
        $par['keyword']=$keyword;

    }
    $str='/admin/Repair/index?';
    $export_str='/admin/Repair/export?';
    foreach ($par as $key=>$v){
        $str.=$key.'='.$v.'&';
        $export_str.=$key.'='.$v.'&';
    }

//dd($datas->toArray());
    $datas->setPath($str);

    $page=$datas->render();
    $project_list=ProjectModel::all();
    $project_list=$this->getProJectList($project_list);
    $this->assign('project_list',$project_list);
    $this->assign('page',$page);
    $this->assign('datas',$datas);
    $this->assign('export_str',$export_str);

    return $this->fetch();
}
    public function export(){

        $datas= RepairModel::with('user')->with('project')->with('img')->where(function ($query){
            if(Request::has('con')){
                $con=Request::get('con');
                $keyword=trim(Request::get('keyword'));
                if($con=='name'){
                    $query->where('contacts_name','like','%'.$keyword.'%')->orderBy('created_at','desc');
                }elseif ($con=='phone'){
                    $query->where('contacts_phone','like','%'.$keyword.'%')->orderBy('created_at','desc');
                }
            }
        })->whereHas('project',function ($query){

            if(Request::has('pro')&&(Request::get('pro')!='请选择小区')){

                $keyword=trim(Request::get('pro'));

                if($keyword!='请选择小区'){
                    $query->where('project_name',$keyword);
                }

            }
        })->orderBy('created_at','desc')->get();
       $array=[];
        foreach ($datas as $key=> $v){
            $array[]=  $this->getExportDataOne($key,$v);
       }

        $headArr=['序号','姓名','电话','所属小区','房产地址','说明','预约上门时间','状态'];
        $export=new Export();
        $export->exports($array,$headArr);

    }

    public function getExportDataOne($key,$data){

        $array=[];
        $array['key']=$key;
        $array['name']=$data['contacts_name'];
        $array['phone']=(string)$data['contacts_phone'];
        $array['project_name']=$data['project']['project_name'];
        $array['house_number']=$data['project']['house_number'];
        $array['note']=$data['note'];
        $array['visit_time']=$data['visit_time'];
        $array['status']=$this->getStatus($data['status']);
        return $array;
    }
    public function getStatus($status){
        if($status==2){
            return '已评价';
        }else{
            return '未评价';
        }
    }
    public function getProJectList($data){
        $data=$data->groupBy('project_name');
        $array=[];
        foreach ($data as $key=> $v){
            $array[]=$key;
        }
        return $array;
    }

    public function delRepair(){
        $id=Request::get('id');

       $res = RepairModel::find($id)->delete();
       if(!$res){
           return ['msg'=>'删除失败','code'=>0];
       }
        return ['msg'=>'删除成功','code'=>1];
    }
}
