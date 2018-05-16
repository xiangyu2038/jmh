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

    $datas = EvaluationModel::with('user')->with('repair.project')->whereHas('repair.project',function ($query){

        if(Request::has('pro')&&(Request::get('pro')!='请选择小区')){

            $keyword=trim(Request::get('pro'));

            if($keyword!='请选择小区'){
                $query->where('project_name',$keyword);
            }

        }
    })->orderBy('created_at','desc')->paginate(config('pagesize'));

    $par=[];
    if(Request::has('pro')&&(Request::get('pro')!='请选择小区')){
        $pro=trim(Request::get('pro'));
        $this->assign('pro',$pro);
        $par['pro']=$pro;
    }
    $str='/admin/Zone/index?';
    $export_str='/admin/Zone/export?';
    foreach ($par as $key=>$v){
        $str.=$key.'='.$v.'&';
        $export_str.=$key.'='.$v.'&';
    }
//dd($datas->toArray());
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


        $datas = EvaluationModel::with('user')->with('repair.project')->whereHas('repair.project',function ($query){

            if(Request::has('pro')&&(Request::get('pro')!='请选择小区')){

                $keyword=trim(Request::get('pro'));

                if($keyword!='请选择小区'){
                    $query->where('project_name',$keyword);
                }

            }
        })->orderBy('created_at','desc')->get();
        $array=[];
        foreach ($datas as $key=> $v){
            $array[]=  $this->getExportDataOne($key,$v);
        }

        $headArr=['序号','小区','姓名','电话','整体满意度','保修便利','现场清洁','方案清晰','服务态度','房修师工号','评价总分'];
        $export=new Export();
        $export->exports($array,$headArr);
    }
    public function getExportDataOne($key,$data){

        $array=[];
        $array['key']=$key;
        $array['project_name']=$data['repair']['project']['project_name'];
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
