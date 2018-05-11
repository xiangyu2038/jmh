<?php
namespace app\admin\controller;

use model\Export;
use model\ProjectModel;
use model\RepairModel;
use think\Controller;
use think\facade\Request;

class Maike extends Controller
{
public function index(){
    return $this->fetch();
}
}
