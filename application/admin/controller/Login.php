<?php
namespace app\admin\controller;

use model\AdminModel;
use model\UserModel;
use think\Controller;
use think\facade\Request;
use think\facade\Session;

class Login extends Controller
{

    public function index(){
        if(Request::isPost()){
            $user_name=Request::post('username');
            $password=Request::post('password');

            $user=AdminModel::where('username',$user_name)->first();
            if(!$user){
                $this->error('用户不存在');
            }
            if($user['password'] !== $this->encryptPassword($password)){
                $this->error('密码错误');
            }

            Session::set('userInfo', $user->toArray());

            $this->success('登录成功', url('admin/index/index'));
        }
        return $this->fetch();

    }


    protected function encryptPassword($password, $salt='', $encrypt='md5')
    {
        return $encrypt($password.$salt);
    }

    /* 退出登录 */
    public function logout() {

        //dd(__LINE__);
        Session::delete('userInfo');
        // Session::clear();
        // Session::destroy();
        $this->success('退出成功！', url('admin/index/login'));
    }

}
