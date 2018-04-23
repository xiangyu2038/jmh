<?php
namespace app\admin\controller;

use model\ProjectModel;
use model\SuggestionModel;
use think\Controller;
use think\facade\Request;

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
        $str='/admin/User/index?keyword='.$keyword.'&';
    }else{
        $str='/admin/Suggestion/tousu';
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
    }else{
        $str='/admin/Suggestion/praise';
    }

    $datas->setPath($str);
    $page=$datas->render();
    $project_list=ProjectModel::all();
    $project_list=$this->getProJectList($project_list);
    $this->assign('project_list',$project_list);
    $this->assign('page',$page);
    $this->assign('datas',$datas);
    return $this->fetch();
    return $this->fetch();
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
