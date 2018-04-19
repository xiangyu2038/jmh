<?php
namespace app\admin\controller;

use model\AdminModel;
use model\UserModel;
use think\Controller;
use think\facade\Request;
use think\facade\Session;

class Index extends BaseController
{
    public function index(){

        $this->view->engine->layout('layouthome');
         $user=Session::get('userInfo');
         $this->assign('userinfo',$user);
        return $this->fetch();
    }


}
