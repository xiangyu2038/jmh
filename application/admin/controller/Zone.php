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
    public function delZone(){
        $id=Request::get('id');
        $res = EvaluationModel::find($id)->delete();
        if(!$res){
            return ['msg'=>'删除失败','code'=>0];
        }
        return ['msg'=>'删除成功','code'=>1];
    }
}
