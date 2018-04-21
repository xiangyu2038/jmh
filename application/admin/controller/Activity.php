<?php
namespace app\admin\controller;

use model\ActivityModel;
use model\AdminModel;
use model\UserModel;
use think\Controller;
use think\facade\Request;
use think\facade\Session;

class Activity extends BaseController
{
    public function index(){


        $datas = ActivityModel::orderBy('created_at','desc')->paginate(10);
      //dd($datas->toArray());
      $str='/admin/Activity/index';
      $datas->setPath($str);

        $page=$datas->render();
      $this->assign('datas',$datas);
      $this->assign('page',$page);
      return  $this->fetch();
    }
    public function edit(){
       if(Request::isPost()){
           $post=Request::post();
           $id=$post['id'];
           $array=[];
           $array['title']=$post['title'];
           $array['describe']=$post['describe'];
           $array['start_time']=$post['start_time'];
           $array['end_time']=$post['end_time'];
           $array['url']=$post['url'];
           $res=ActivityModel::find($id)->update($array);
           if(!$res){
               $this->error('保存失败');
           }

           $file = request()->file('file');
if($file){
    // 移动到框架应用根目录/uploads/ 目录下
    $info = $file->move('uploads/activity');
    if(!$info){
        $this->error('失败');
    }
    $filename = '\uploads\activity\\' . $info->getSaveName();
    $res=ActivityModel::find($id)->update(['imgurl'=>$filename]);
    if(!$res){
        $this->error('保存失败');
    }

}

           $this->success('成功');


           // 成功上传后 获取上传信息
           // 输出 jpg
           //echo $info->getExtension();
           // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
           //echo $info->getSaveName();
           // 输出 42a79759f284b767dfcb2a0197904287.jpg
           // $filename= $info->getFilename();

       }

        $id=Request::get('id');
       $data=ActivityModel::find($id);

       $this->assign('id',$id);
       $this->assign('data',$data);
        return  $this->fetch();
    }
    public function add(){
        if(Request::isPost()){
            $post=Request::post();
            $array=[];
            $array['title']=$post['title'];
            $array['describe']=$post['describe'];
            $array['start_time']=$post['start_time'];
            $array['end_time']=$post['end_time'];
            $array['url']=$post['url'];
           $res =    ActivityModel::create($array);
           if(!$res){
                   return json(['code'=>0,'msg'=>'添加失败']);
           }

            $file = request()->file('file');

            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->move('uploads/activity');

            if(!$info){
                return json(['code'=>0,'msg'=>'上传图片失败']);
            }

                // 成功上传后 获取上传信息
                // 输出 jpg
                //echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                // $filename= $info->getFilename();
                $filename = '\uploads\activity\\' . $info->getSaveName();
                      $res=ActivityModel::find($res->id)->update(['imgurl'=>$filename]);
                      if(!$res){
                          $this->error('保存失败');
                      }



            $this->success('成功');
        }
        return  $this->fetch();
    }
public function changeDisplay(){
        $id=Request::post('id');
        $display=Request::post('display');
        $res =ActivityModel::find($id)->update(['display'=>$display]);

        return ['code'=>1,'msg'=>'ok'];

}

public function changeStatus(){
    $id=Request::post('id');
    $status=Request::post('status');
    $res =ActivityModel::find($id)->update(['status'=>$status]);

    return ['code'=>1,'msg'=>'ok'];
}

}
