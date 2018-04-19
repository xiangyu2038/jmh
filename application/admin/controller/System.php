<?php
namespace app\admin\controller;

use model\AdminModel;
use think\Controller;
use think\facade\Request;

class System extends Controller
{

    public function index(){
        $datas =AdminModel::paginate(config('pagesize'), $columns = ['*'], $pageName = 'page', Request::get('page'));
        $page=$datas->render();

        $this->assign('page',$page);
        $this->assign('datas',$datas);
        return $this->fetch();
    }

    public function add(){
        return $this->fetch();
    }
}
