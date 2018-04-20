<?php
namespace app\admin\controller;

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
            $query->where('project_name','like','%'.$keyword.'%');
        }
    })->paginate(config('pagesize'));
    if(Request::has('keyword')){
        $keyword=trim(Request::get('keyword'));
        $this->assign('keyword',$keyword);
        $str='/admin/User/index?keyword='.$keyword.'&';
    }else{
        $str='/admin/Suggestion/tousu';
    }
    $datas->setPath($str);
    $page=$datas->render();

    $this->assign('page',$page);
    $this->assign('datas',$datas);
    return $this->fetch();
}
public function praise(){
    $datas =SuggestionModel::where('type',1)->whereHas('project',function ($query){
        if(Request::has('keyword')){
            $keyword=trim(Request::get('keyword'));
            $query->where('project_name','like','%'.$keyword.'%');
        }
    })->paginate(config('pagesize'));

    if(Request::has('keyword')){
        $keyword=trim(Request::get('keyword'));
        $this->assign('keyword',$keyword);
        $str='/admin/User/index?keyword='.$keyword.'&';
    }else{
        $str='/admin/Suggestion/praise';
    }
    $datas->setPath($str);
    $page=$datas->render();

    $this->assign('page',$page);
    $this->assign('datas',$datas);
    return $this->fetch();
    return $this->fetch();
}
}
