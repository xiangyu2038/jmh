<?php
namespace app\admin\controller;

use model\EvaluationModel;
use think\Controller;
use think\facade\Request;

class Zone extends Controller
{
public function index(){

    $datas = EvaluationModel::with('user')->orderBy('created_at','desc')->paginate(config('pagesize'));

    $page=$datas->render();

    $this->assign('page',$page);
    $this->assign('datas',$datas);
    return $this->fetch();

}
}
