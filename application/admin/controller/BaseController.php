<?php
namespace app\admin\controller;

use model\AdminModel;
use model\UserModel;
use think\Controller;
use think\facade\Request;
use think\facade\Session;

class BaseController extends Controller
{
    public function __construct($need_check_token = true)
    {
        parent::__construct();

        if(!session('userInfo')){
            $this->redirect('admin/login/index');
        }
    }

}
