<?php
namespace app\admin\controller;

use model\Export;
use model\Pages;
use model\ProjectModel;
use model\UserModel;
use think\Controller;
use think\facade\Request;

class User extends BaseController
{
    public function index(){


        $datas = ProjectModel::with('user')->where(function ($query){
            if(Request::has('pro')){
                $pro=trim(Request::get('pro'));
                if($pro!='请选择小区'){
                    $query->where('project_name',$pro);
                }

            }else{
                if(Request::has('con')){
                    $con=Request::get('con');
                    $keyword=trim(Request::get('keyword'));

                    if($con=='project_name'){
                        $query->where('project_name','like','%'.$keyword.'%');
                    }else if($con=='city'){

                        $query->where('city','like','%'.$keyword.'%');
                    }
                }
            }

        })->whereHas('user',function ($query){
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
        if(Request::has('pro')){
            $pro=Request::get('pro');
            $str='/admin/User/index?pro='.$pro.'&';
            $export_str='/admin/User/export?pro='.$pro.'&';
            $this->assign('pro',$pro);
        }else{
            if(Request::has('con')){
                $con=Request::get('con');
                $keyword=trim(Request::get('keyword'));
                $this->assign('con',$con);
                $this->assign('keyword',$keyword);

                $str='/admin/User/index?con='.$con.'&keyword='.$keyword.'&';
                $export_str='/admin/User/export?con='.$con.'&keyword='.$keyword.'&';
            }else{
                $str='/admin/User/index';
                $export_str='/admin/User/index';
            }
        }



        $datas->setPath($str);

        $page=$datas->render();
         $project_list=ProjectModel::all();
         $project_list=$this->getProJectList($project_list);

          $this->assign('page',$page);
          $this->assign('datas',$datas);
          $this->assign('project_list',$project_list);
          $this->assign('export_str',$export_str);
        return $this->fetch();
}

    public function add(){
if(Request::isPost()){
$post=Request::post();
    $array=[];
    $array['name']=$post['name'];
    $array['phone']=$post['phone'];
    $array['id_number']=$post['id_number'];
   $res = UserModel::create($array);
   $p_array=[];
   $p_array['city']=$post['city'];
   $p_array['project_name']=$post['project_name'];
   $p_array['house_number']=$post['house_number'];
   $p_array['user_id']=$res->id;
   $res=ProjectModel::create($p_array);
if(!$res){
    return json(['code'=>0,'msg'=>'操作失败']);
}
    return json(['code'=>1,'msg'=>'操作成功']);
}

        return $this->fetch();
    }

    public function del(){
        $user_id=Request::get('user_id');
        $res=UserModel::find($user_id)->delete();
        if(!$res){
            return json(['code'=>0,'msg'=>'操作失败']);
        }
        return json(['code'=>1,'msg'=>'操作成功']);
    }
    public function edit(){
if(Request::isPost()){
    $post=Request::post();
    $res=$this->saveProjectInfo($post);
    if(!$res){
    return json(['code'=>0,'msg'=>'编辑失败']);
    }
    $res=$this->saveUserInfo($post);
    if(!$res){
        return json(['code'=>0,'msg'=>'编辑失败']);
    }
    return json(['code'=>1,'msg'=>'编辑成功']);
}


        $project_id=Request::get('project_id');
         $data=ProjectModel::with('user')->where('id',$project_id)->first();
         //dd($data->toArray());
         $this->assign('project',$data);


        return $this->fetch();
    }
    public function saveProjectInfo($data){
        $array=[];
        $array['city']=$data['city'];
        $array['project_name']=$data['project_name'];
        $array['house_number']=$data['house_number'];
       $res= ProjectModel::find($data['project_id'])->update($array);
       return $res;


}
public function saveUserInfo($data){
    $array=[];
    $array['name']=$data['name'];
    $array['phone']=$data['phone'];
    $array['id_number']=$data['id_number'];
    $res= UserModel::find($data['user_id'])->update($array);
    return $res;
}

public function addProject(){
    if(Request::isPost()){
        $post=Request::post();
        $res=ProjectModel::create($post);
        if(!$res){
            return json(['code'=>0,'msg'=>'操作失败']);
        }
        return json(['code'=>1,'msg'=>'操作成功']);
    }
    $user_id=Request::get('user_id');
    $user=UserModel::find($user_id);
    $this->assign('user_id',$user_id);
    $this->assign('user',$user);
    return $this->fetch();
}


    public function exports()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');

        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move('uploads');
        if ($info) {
            // 成功上传后 获取上传信息
            // 输出 jpg
            //echo $info->getExtension();
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            //echo $info->getSaveName();
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            // $filename= $info->getFilename();
            $filename = 'uploads/' . $info->getSaveName();

            $this->goods_import($filename);
        } else {
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    protected function goods_import($filename, $exts = 'xlsx')
    {
        require_once BASEPATH . 'vendor/PHPExcel/PHPExcel.class.php';
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入

        $PHPExcel = new \PHPExcel();


        //如果excel文件后缀名为.xls，导入这个类
        if ($exts == 'xls') {
            $PHPReader = new \PHPExcel_Reader_Excel5();
        } else if ($exts == 'xlsx') {

            $PHPReader = new \PHPExcel_Reader_Excel2007();
        }
        /*import("Org.Util.PHPExcel.Reader.CSV");
        $PHPReader = new \PHPExcel_Reader_CSV();*/

        //载入文件
        $PHPExcel = $PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet = $PHPExcel->getSheet(0);
        //获取总列数
        $allColumn = $currentSheet->getHighestColumn();

        //获取总行数
        $allRow = $currentSheet->getHighestRow();
        ++$allColumn;
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
            //从哪列开始，A表示第一列
            for ($currentColumn = 'A'; $currentColumn != $allColumn; $currentColumn++) {
                //数据坐标
                $address = $currentColumn . $currentRow;
                //读取到的数据，保存到数组$arr中
                $data[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
            }
        }


        $this->saveImport($data);
        $this->success('导入成功');

    }
    public function saveImport($data)
    {

        $project_array = [];
        foreach ($data as $key => $v) {

            if ($key == 1) {
                continue;
            }

            $res = UserModel::where('id_number', $v['G'])->first();

            if ($res) {
                ///已经保存用户
                $v['user_id'] = $res->id;
                $project_array[] = $this->getProjectArray($v);
            } else {
                ////去保存用户
                $user_info = $this->getUserInfo($v);
                $a = UserModel::create($user_info);
                $v['user_id'] = $a->id;
                $project_array[] = $this->getProjectArray($v);
            }


        }
        $res = ProjectModel::Insert($project_array);
        return json(['error_code' => 1, 'msg' => '成功']);
    }

    public function getProjectArray($data)
    {
        $array = [];
        $array['user_id'] = $data['user_id'];
        $array['project_name'] = $data['C'];
        $array['house_number'] = $data['D'];
        $array['city'] = $data['B'];
        $array['created_at'] = date('Y-m-d H:i:s');
        $array['updated_at'] = date('Y-m-d H:i:s');
        return $array;
    }

    public function getUserInfo($data)
    {
        $array = [];
        $array['name'] = $data['E'];
        $array['id_number'] = $data['G'];
        $array['phone'] = $this->formatePhone($data['F']);
        return $array;

    }

    public function formatePhone($data)
    {

        $data = explode('.', $data);

        return $data[0];
    }

    /**
     * 导出
     * @param 
     * @return mixed
     */
    public function export(){

        $data = ProjectModel::with('user')->where(function ($query){
            if(Request::has('pro')){
                $pro=trim(Request::get('pro'));
                if($pro!='请选择小区'){
                    $query->where('project_name',$pro);
                }

            }else{
                if(Request::has('con')){
                    $con=Request::get('con');
                    $keyword=trim(Request::get('keyword'));

                    if($con=='project_name'){
                        $query->where('project_name','like','%'.$keyword.'%');
                    }else if($con=='city'){
                        $query->where('city','like','%'.$keyword.'%');
                    }
                }
            }

        })->whereHas('user',function ($query){
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
;
     $this->getExportData($data);

    }
    public function getExportData($data){
        $data=$data->toarray();
        $array=[];
        foreach ($data as $key=> $v){
              $array[]=$this->getExportDataOne($key,$v);
          }

          $export=new Export();
        $headArr=['序号','城市','项目名','楼室号','客户姓名','联系电话','证件号'];
        $export->exports($array,$headArr);
    }
    public function getExportDataOne($key,$data){
       $array=[];
       $array['key']=$key;
       $array['city']=$data['city'];
       $array['project_name']=$data['project_name'];
       $array['house_number']=$data['house_number'];
       $array['name']=$data['user']['name'];
       $array['phone']=$data['user']['phone'];
       $array['id_number']=(string)$data['user']['id_number'];
       return $array;
    }
public function getProJectList($data){
 $data=$data->groupBy('project_name');
        $array=[];
    foreach ($data as $key=> $v){
            $array[]=$key;
        }
        return $array;
}

}
