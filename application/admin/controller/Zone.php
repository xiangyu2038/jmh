<?php
namespace app\admin\controller;

use model\EvaluationModel;
use think\Controller;
use think\facade\Request;

class Zone extends Controller
{
public function index(){

    $datas = EvaluationModel::with('user')->paginate(config('pagesize'), $columns = ['*'], $pageName = 'page', Request::get('page'));

    $page=$datas->render();

    $this->assign('page',$page);
    $this->assign('datas',$datas);
    return $this->fetch();

}
}
