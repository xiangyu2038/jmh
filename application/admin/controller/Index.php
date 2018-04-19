<?php
namespace app\admin\controller;

use think\Controller;

class Index extends Controller
{
    public function login(){
        return $this->fetch();
    }


    public function main(){
    return $this->fetch();
}
}
