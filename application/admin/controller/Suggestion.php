<?php
namespace app\admin\controller;

use think\Controller;

class Suggestion extends Controller
{
public function index(){
    return $this->fetch();
}

public function tousu(){
    return $this->fetch();
}
public function praise(){
    return $this->fetch();
}
}
