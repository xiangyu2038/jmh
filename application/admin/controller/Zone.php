<?php
namespace app\admin\controller;

use model\EvaluationModel;
use model\Export;
use model\ProjectModel;
use think\Controller;
use think\facade\Request;

class Zone extends Controller
{
public function index(){

    $datas = EvaluationModel::with('user')->whereHas('user',function ($query){
        if(Request::has('con')){
            $con=Request::get('con');
            $keyword=trim(Request::get('keyword'));
            if($con=='name'){

                $query->where('name','like','%'.$keyword.'%')->orderBy('created_at','desc');
            }elseif ($con=='phone'){
                $query->where('phone','like','%'.$keyword.'%')->orderBy('created_at','desc');
            }
        }
    })->orderBy('created_at','desc')->paginate(config('pagesize'));


    if(Request::has('keyword')){
        $keyword=trim(Request::get('keyword'));
        $con=Request::get('con');
        $this->assign('keyword',$keyword);
        $this->assign('con',$con);
        $str='/admin/Zone/index?keyword='.$keyword.'&';
        $export_str='/admin/Zone/export?keyword='.$keyword.'&con='.$con.'&';
    }else{
        $str='/admin/Zone/index';
        $export_str='/admin/Zone/export';
    }
    $page=$datas->render();
    $datas->setPath($str);
    $project_list=ProjectModel::all();
    $project_list=$this->getProJectList($project_list);
    $this->assign('project_list',$project_list);
    $this->assign('page',$page);
    $this->assign('datas',$datas);
    $this->assign('export_str',$export_str);
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
    public function getProJectList($data){
        $data=$data->groupBy('project_name');
        $array=[];
        foreach ($data as $key=> $v){
            $array[]=$key;
        }
        return $array;
    }

    public function export(){


        $datas = EvaluationModel::with('user')->whereHas('user',function ($query){
            if(Request::has('con')){
                $con=Request::get('con');
                $keyword=trim(Request::get('keyword'));
                if($con=='name'){

                    $query->where('name','like','%'.$keyword.'%')->orderBy('created_at','desc');
                }elseif ($con=='phone'){
                    $query->where('phone','like','%'.$keyword.'%')->orderBy('created_at','desc');
                }
            }
        })->orderBy('created_at','desc')->get();
        $array=[];
        foreach ($datas as $key=> $v){
            $array[]=  $this->getExportDataOne($key,$v);
        }

        $headArr=['序号','姓名','电话','整体满意度','保修便利','现场清洁','方案清晰','服务态度','房修师工号','评价总分'];
        $export=new Export();
        $export->exports($array,$headArr);
    }
    public function getExportDataOne($key,$data){

        $array=[];
        $array['key']=$key;
        $array['name']=$data['user']['name'];
        $array['phone']=$data['phone'];
        $array['satisfy']=$data['satisfy'];
        $array['convenient']=$data['convenient'];
        $array['clean']=$data['clean'];
        $array['plan']=$data['plan'];
        $array['serviece']=$data['serviece'];
        $array['id_number']=$data['user']['id_number'];
        $array['zongfen']=$this->getTotal($array);


        return $array;
    }
    public function getTotal($data){
        $num=0;
        $num=$num+$data['satisfy']+$data['convenient']+$data['clean']+$data['plan']+$data['serviece'];
        return $num;
    }
}
