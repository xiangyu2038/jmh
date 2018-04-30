<?php

namespace app\index\controller;

use model\ActivityModel;
use model\EvaluationModel;
use model\ImgModel;
use model\Jssdk;
use model\ProjectModel;
use model\RepairModel;
use model\SuggestionModel;
use model\TestModel;
use model\UserModel;
use think\Cache;
use think\Controller;
use think\facade\Request;
use think\Session;

class Index extends Controller
{
    public function index()
    {

        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }

    public function hello()
    {
       //TODO  删除
       // $jssdk = new Jssdk('wxcab7c014367d6f9a', 'ad3150cc8c690605cfcd638d1a7c399a');

        //$signPackage = $jssdk->GetSignPackage();
        //$this->assign('signPackage', $signPackage);
        return $this->fetch();
    }

    public function login()
    {

        $openid=$this->getOpenId();
        $url=Request::get('url');
        if(!$url){
            $url='false';
        }

       // $openid='oUPo2wRgPOudk-bPLzwahZ1YkDkcll';
      $res =  UserModel::where('openid',$openid)->first();

      if($res){
          $is_login=1;
      }else{
          $is_login=0;
      }

        $this->assign('url',$url);
        $this->assign(['openid'=>$openid,'is_login'=>$is_login]);
        return $this->fetch();
    }

    public function doLogin()
    {


        $phone = Request::post('tel');
        $idcard = Request::post('idcard');
        $name = Request::post('name');
        $openid = Request::post('openid');
        $url=Request::post('url');
        $data = UserModel::where('phone', $phone)->first();
        if (!$data) {
            return json(['error_code' => 2, 'msg' => '认证失败','url'=>'false']);//认证失败
        }
        if ($idcard != $data->id_number || $name != $data->name) {
            return json(['error_code' => 3, 'msg' => '认证失败','url'=>'false']);//认证失败
        }

        $res = UserModel::where('phone', $phone)->update(['openid' => $openid]);

        if (!$res) {
            return json(['error_code' => 4, 'msg' => '认证失败','url'=>'false']);//认证失败
        }


            return json(['error_code' => 1, 'msg' => '成功','url'=>$url]);//认证成功

       // return json(['error_code' => 1, 'msg' => '成功']);//认证成功

    }


    public function prompt()
    {
       $repair_id=Request::get('repair_id');

       $this->assign(['repair_id'=>$repair_id]);
        return $this->fetch();
    }

    public function upload()
    {

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move('../uploads');
        if ($info) {
            // 成功上传后 获取上传信息
            // 输出 jpg
            echo $info->getExtension();
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            echo $info->getSaveName();
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            echo $info->getFilename();
        } else {
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    public function repair()
    {
        $openid=$this->getOpenId();
        //$openid='oUPo2wRgPOudk-bPLzwahZ1YkDkc';
        $data = UserModel::where('openid', $openid)->first();

        if (!$data) {

            ///还没认证
            $this->redirect('index/index/login?url=/index/index/repair');
        }
        $user_id = $data->id;
        /////去寻找还有没有未评价保修
        $repair = RepairModel::where('user_id',$user_id)->where('status',1)->get();
        if($repair){
           $repair=$this->getRepairId($repair);

           if($repair){
                $this->redirect(url('index/index/prompt',['repair_id'=>$repair['id']]));
            }

        }
        $datas = ProjectModel::where('user_id', $user_id)->get();

        $jssdk = new Jssdk('wxcab7c014367d6f9a', 'ad3150cc8c690605cfcd638d1a7c399a');

        $signPackage = $jssdk->GetSignPackage();

        $this->assign(['datas' => $datas, 'user_id' => $user_id,'signPackage'=>$signPackage]);

        return $this->fetch();
    }

    public function doRepair()
    {


       // $data=Request::post();
$data=cache('aa');
        $user_id = $data['user_id'];

        $contacts_name = $data['name'];
        $contacts_phone = $data['phone'];
        $visit_time = $data['time'];
        $project_id = $data['project_id'];
        $note =$data['note'];



        $created_data = [];
        $created_data['user_id'] = $user_id;
        $created_data['contacts_name'] = $contacts_name;
        $created_data['contacts_phone'] = $contacts_phone;
        $created_data['visit_time'] = $visit_time;
        $created_data['project_id'] = $project_id;
        $created_data['note'] = $note;
        //$created_data=json_encode($created_data);


        $res = RepairModel::create($created_data);
        if (!$res) {
            return json(['error_code' => 2, 'msg' => '失败','url'=>'false']);
        }
       // return json(['error_code' => 1, 'msg' => '成功']);//认证成功
        //cache('aa',$server_id);
        $repair_id = $res->id;
        if(Request::has('serverId')){
        $server_id = $data['serverId'];
            $img_data = $this->uploads($server_id);



            $created_img = $this->getImg($img_data, $repair_id, $type = 1);
            $res = ImgModel::Insert($created_img);
            if (!$res) {
                return json(['error_code' => 3, 'msg' => '失败','url'=>'false']);//认证成功
            }
        }

        return json(['error_code' => 1, 'msg' => '成功','url'=>'false','data'=>['repair_id'=>$repair_id]]);//认证成功

    }

    public function evaluation()
    {
       $openid=$this->getOpenId();
        //$openid='oUPo2we4EByx0XGxP5_1pU1nP5ZI';
       $repair_id=Request::get('repair_id');
        //$repair_id=2;
      $this->assign(['openid'=>$openid,'repair_id'=>$repair_id]);
        return $this->fetch();
    }

    public function doEvaluation()
    {
        $post=Request::post();

        $satisfy = $post['satisfied'];
        $convenient =$post['convenient'];
        $clean = $post['clean'];
        $plan = $post['clear'];
        $serviece =$post['attitude'];
        $openid=$post['openid'];
        $repair_id = $post['repair_id'];
        $repair_man_id = $post['name'];



        $created_data = [];
        $created_data['satisfy'] = $satisfy;
        $created_data['convenient'] = $convenient;
        $created_data['clean'] = $clean;
        $created_data['plan'] = $plan;
        $created_data['serviece'] = $serviece;
        $created_data['repair_id'] = $repair_id;
        $created_data['repair_man_id'] = $repair_man_id;
        $created_data['openid'] = $openid;


        $res = EvaluationModel::create($created_data);

        if (!$res) {
            return json(['error_code' => 3, 'msg' => '提交失败','url'=>'false']);
        }
        RepairModel::find($repair_id)->update(['status'=>2]);///修改为已评价
        return json(['error_code' => 1, 'msg' => '提交成功','url'=>'false']);

    }

    public function getOpenId()
    {
       if(session('openid')){
           return session('openid');
       }else{
           define('WX_PATH', BASEPATH . '/vendor/WX/example/');

           require_once WX_PATH . 'WxPay.JsApiPay.php';
           $jsapi = new \JsApiPay();
           $openid = $jsapi->GetOpenid();
           session('openid', $openid);


           return $openid;
       }

       /* if(session('?name')){
            return 'oUPo2wRgPOudk-bPLzwahZ1YkDkc';
        }else{

        }*/

    }

    public function getImg($data, $repair_id, $type)
    {

        $array = [];
        foreach ($data as $v) {

            $array[] = $this->getImgOne($v, $repair_id, $type);
        }

        return $array;
    }

    public function getImgOne($data, $repair_id, $type)
    {
        $array = [];
        $array['img_link'] = $data;
        $array['repair_id'] = $repair_id;
        $array['type'] = $type;
        $array['created_at'] = date('y-m-d h:i:s', time());
        $array['updated_at'] = date('y-m-d h:i:s', time());
        return $array;
    }

    public function getImgData()
    {
        return ['dasd', 'das'];
    }

    public function suggestion()
    {

        $openid=$this->getOpenId();
        $data = UserModel::where('openid', $openid)->first();

        if (!$data) {

            ///还没认证
            $this->redirect('index/index/login?url=/index/index/suggestion');
        }

        $this->assign(['openid'=>$openid]);
        return $this->fetch();
    }

    public function doSuggestion()
    {


        $post=Request::post();

//cache('dada',$post);
     //   $post=cache('dada');
      $note = $post['note'];
        $name = $post['name'];
        $phone = $post['phone'];
        $openid=$post['openid'];
        $type = $post['type'];//1为表扬
        $project_id = $post['project_id'];//1为表扬
        $server_id = $post['serverId'];

        $created_data = [];
        $created_data['note'] = $note;
        $created_data['name'] = $name;
        $created_data['phone'] = $phone;
        $created_data['openid'] = $openid;
        $created_data['type'] = $type;
        $created_data['project_id'] = $project_id;

        $res = SuggestionModel::create($created_data);

        if (!$res) {
            return json(['error_code' => 3, 'msg' => '保存表扬信息失败','url'=>'false']);
        }

        if($server_id){
            $img_data = $this->uploads($server_id);


            $repair_id = $res->id;
            $created_img = $this->getImg($img_data, $repair_id, $type = 1);
            $res = ImgModel::Insert($created_img);
            if (!$res) {
                return json(['error_code' => 3, 'msg' => '保存表扬信息失败','url'=>'false']);
            }
        }



        return json(['error_code' => 1, 'msg' => '成功','url'=>'false']);//认证成功

    }

    public function praise()
    {
        $openid=Request::get('openid');
        $user_id=UserModel::where('openid',$openid)->first()->id;
        $datas = ProjectModel::where('user_id', $user_id)->get();

        $jssdk = new Jssdk('wxcab7c014367d6f9a', 'ad3150cc8c690605cfcd638d1a7c399a');

        $signPackage = $jssdk->GetSignPackage();

        $this->assign(['signPackage'=>$signPackage,'openid'=>$openid,'datas'=>$datas]);

        return $this->fetch();
    }

    public function complaints()
    {
        $openid=Request::get('openid');
       // $openid='oUPo2wRgPOudk-bPLzwahZ1YkDkc';
        $user_id=UserModel::where('openid',$openid)->first()->id;
        $datas = ProjectModel::where('user_id', $user_id)->get();

        $jssdk = new Jssdk('wxcab7c014367d6f9a', 'ad3150cc8c690605cfcd638d1a7c399a');

        $signPackage = $jssdk->GetSignPackage();

        $this->assign(['signPackage'=>$signPackage,'openid'=>$openid,'datas'=>$datas]);

        return $this->fetch();
    }



    public function uploads($server_id)
    {

        $jssdk = new Jssdk('wxcab7c014367d6f9a', 'ad3150cc8c690605cfcd638d1a7c399a');

        $accessToken =$jssdk->getAccessToken();
        $array=[];
        foreach ($server_id as $v){
            $array[]= $this->uploadsOne($accessToken,$v);
        }
        return $array;

// 要存在你服务器哪个位置？

    }



    public function uploadsOne($accessToken,$data){
        $targetNames = '/uploads/' . date('YmdHis').rand(0,1000) .'.jpg';
        $targetName='./public'.$targetNames;
        $ch = curl_init("http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$accessToken}&media_id={$data}");
        $fp = fopen(BASEPATH . $targetName, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $targetNames;

    }

    public function getRepairId($data){
        foreach ($data as $v){
            $res=$this->judgeStatus($v);
            if($res){
                return $v;
            }
        }
        return [];
    }
    public function judgeStatus($data){
        $before_time=$date = strtotime('-7 days');///前7天时间

        $repair_time=strtotime($data['created_at']);////提交维修时间
        if($repair_time<$before_time){
            return false;
        }
        return true;

    }
    public function actitity(){


        ////正在活动
        $data =  ActivityModel::where('display',1)->get();
        $activity=$this->getActivity($data);
        $over=$this->getOver($data);

        $this->assign('activity',$activity);
        $this->assign('over',$over);
        return $this->fetch();
    }
    public function getActivity($data){
        $array=[];
        foreach ($data as $v){

$start_time=strtotime($v['start_time']);
$end_time=strtotime($v['end_time']);
if($start_time<=time()&&time()<$end_time){

if($v['status']!=2){
    $array[]=$v;
}

                }

        }

        return $array;
    }
public function getOver($data){
        $array=[];
        foreach ($data as $v){
            $end_time=strtotime($v['end_time']);
            if($end_time>time()){
                $array[]=$v;
            }
        }
        return $array;
}


}
