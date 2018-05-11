<?php
namespace app\admin\controller;

use Carbon\Carbon;
use model\ProjectModel;
use model\SuggestionModel;
use think\Controller;
use think\facade\Request;
use model\Export;


class Suggestion extends Controller
{
public function index(){
    return $this->fetch();
}

public function tousu(){

    $datas =SuggestionModel::with('user')->with('project')->where('type',2)->whereHas('project',function ($query){
        if(Request::has('keyword')){
            $keyword=trim(Request::get('keyword'));
            if($keyword!='请选择小区'){
                $query->where('project_name','like','%'.$keyword.'%');
            }

        }
    })->orderBy('created_at','desc')->paginate(config('pagesize'));
    if(Request::has('keyword')){
        $keyword=trim(Request::get('keyword'));
        $this->assign('keyword',$keyword);
        $str='/admin/Suggestion/tousu?keyword='.$keyword.'&';
        $export_str='/admin/Suggestion/export?type=2&keyword='.$keyword.'&';
    }else{
        $str='/admin/Suggestion/tousu';
        $export_str='/admin/Suggestion/export?type=2';
    }
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
public function praise(){
    $datas =SuggestionModel::where('type',1)->whereHas('project',function ($query){
        if(Request::has('keyword')){
            $keyword=trim(Request::get('keyword'));
            if($keyword!='请选择小区'){
                $query->where('project_name','like','%'.$keyword.'%');
            }

        }
    })->orderBy('created_at','desc')->paginate(config('pagesize'));

    if(Request::has('keyword')){
        $keyword=trim(Request::get('keyword'));
        $this->assign('keyword',$keyword);
        $str='/admin/User/index?keyword='.$keyword.'&';
        $export_str='/admin/Suggestion/export?type=1&keyword='.$keyword.'&';
    }else{
        $str='/admin/Suggestion/praise';
        $export_str='/admin/Suggestion/export?type=1';
    }

    $datas->setPath($str);
    $page=$datas->render();
    $project_list=ProjectModel::all();
    $project_list=$this->getProJectList($project_list);
    $this->assign('project_list',$project_list);
    $this->assign('page',$page);
    $this->assign('datas',$datas);
    $this->assign('export_str',$export_str);
    return $this->fetch();
    //return $this->fetch();
}
    public function getProJectList($data){
        $data=$data->groupBy('project_name');
        $array=[];
        foreach ($data as $key=> $v){
            $array[]=$key;
        }
        return $array;
    }

    public function delSuggestion(){
        $id=Request::get('id');
        $res = SuggestionModel::find($id)->delete();
        if(!$res){
            return ['msg'=>'删除失败','code'=>0];
        }
        return ['msg'=>'删除成功','code'=>1];
    }

    public function export(){

        $type=Request::get('type');

        $datas =SuggestionModel::with('user')->with('project')->where('type',$type)->whereHas('project',function ($query){
            if(Request::has('keyword')){
                $keyword=trim(Request::get('keyword'));
                if($keyword!='请选择小区'){
                    $query->where('project_name','like','%'.$keyword.'%');
                }

            }
        })->orderBy('created_at','desc')->get();
        $array=[];
        foreach ($datas as $key=> $v){
            $array[]=  $this->getExportDataOne($key,$v);
        }

        $headArr=['序号','姓名','电话','小区','表扬信息','时间'];
        $export=new Export();
        $export->exports($array,$headArr);

    }

    public function getExportDataOne($key,$data){

        $array=[];
        $array['key']=$key;
        $array['name']=$data['user']['name'];
        $array['phone']=$data['phone'];
        $array['project']=$data['project']['project_name'];
        $array['note']=$data['note'];
        $array['note']=$data['note'];
        $array['createdat']=$this->getTime($data['created_at']);

        return $array;
    }
    public function getTime($time){
$time=$time->timestamp;
return date('Y-m-d H:i:s',$time);


    }
}
