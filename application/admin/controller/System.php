<?php
namespace app\admin\controller;

use think\Controller;

class System extends Controller
{

    public function index(){
        return $this->fetch();
    }

    public function add(){
        return $this->fetch();
    }
}
