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

    $datas= RepairModel::with('user')->with('project')->with('img')->whereHas('project',function ($query){
        if(Request::has('keyword')){
           $keyword=trim(Request::get('keyword'));
            $query->where('project_name','like','%'.$keyword.'%');
        }
    })->orderBy('created_at','desc')->paginate(10);
//dd($datas->toArray());
    if(Request::has('keyword')){
        $keyword=trim(Request::get('keyword'));
        $this->assign('keyword',$keyword);
        $str='/admin/User/index?keyword='.$keyword.'&';
    }else{
        $str='/admin/Repair/index';
    }

    $datas->setPath($str);

    $page=$datas->render();
    $project_list=ProjectModel::all();
    $project_list=$this->getProJectList($project_list);
    $this->assign('project_list',$project_list);
    $this->assign('page',$page);
    $this->assign('datas',$datas);

    return $this->fetch();
}
    public function export(){

        $datas= RepairModel::with('user')->with('img')->with('project')->get();
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
        $array['name']=$data['user']['name'];
        $array['phone']=$data['user']['phone'];
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
}
