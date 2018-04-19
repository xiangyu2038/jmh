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

    $datas =SuggestionModel::with('user')->where('type',2)->paginate(config('pagesize'), $columns = ['*'], $pageName = 'page', Request::get('page'));
    $page=$datas->render();

    $this->assign('page',$page);
    $this->assign('datas',$datas);
    return $this->fetch();
}
public function praise(){
    $datas =SuggestionModel::with('user')->where('type',1)->paginate(config('pagesize'), $columns = ['*'], $pageName = 'page', Request::get('page'));
    $page=$datas->render();
    //dd($datas->toArray());
    $this->assign('page',$page);
    $this->assign('datas',$datas);
    return $this->fetch();
    return $this->fetch();
}
}
