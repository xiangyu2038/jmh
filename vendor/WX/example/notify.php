<?php
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);

require_once WX_PATH."/../lib/WxPay.Api.php";
require_once WX_PATH.'/../lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler(WX_PATH."/../logs/".date('Y-m-d').'.log');

$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数   ///这里处理回调数据内容
	public function NotifyProcess($data, &$msg)
	{

	    Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();

		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}


		//////本地业务处理
        $uu=\Model\Booking_pay::where('transaction_id',$data['transaction_id'])->first();

		if(!$uu){
            $array=[];///本地保存数组
            $array['order_sn']=$data['attach'];
            $array['appid']=$data['appid'];
            $array['bank_type']=$data['bank_type'];
            $array['cash_fee']=$this->get_price($data['cash_fee']);
            $array['fee_type']=$data['fee_type'];
            $array['mch_id']=$data['mch_id'];
            $array['nonce_str']=$data['nonce_str'];
            $array['openid']=$data['openid'];
            $array['out_trade_no']=$data['out_trade_no'];
            $array['sign']=$data['sign'];
            $array['time_end']=$this->get_time($data['time_end']);
            $array['total_fee']=$this->get_price($data['total_fee']);
            $array['trade_type']=$data['trade_type'];
            $array['transaction_id']=$data['transaction_id'];

            $uu=\Model\Booking_pay::create($array);
            if(!$uu){
                $msg = "保存订单失败";
                return false;
            }
        }

         $ress=$this->save_res_with_money_change($data,$uu->id);

		return true;
	}
	public function get_time($time){
	    $year=substr($time,0,4);
	    $m=substr($time,4,2);
        $d=substr($time,6,2);
	    $h=substr($time,8,2);
	    $f=substr($time,10,2);
	    $s=substr($time,12,2);
	    $time=$year.'-'.$m.'-'.$d.' '.$h.':'.$f.':'.':'.$s;
return $time;
    }
    public function get_price($price){
        $price =$this->ncPriceCalculate($this->ncPriceFormat($price),"/",100);
        return $price;
    }
    public function  ncPriceCalculate($n1,$symbol,$n2,$scale = '2'){
        $res = "";
        switch ($symbol){
            case "+"://加法
                $res = bcadd($n1,$n2,$scale);break;
            case "-"://减法
                $res = bcsub($n1,$n2,$scale);break;
            case "*"://乘法
                $res = bcmul($n1,$n2,$scale);break;
            case "/"://除法
                $res = bcdiv($n1,$n2,$scale);break;
            case "%"://求余、取模
                $res = bcmod($n1,$n2,$scale);break;
            default:
                $res = "";break;
        }
        return $res;
    }
    public function ncPriceFormat($price) {
        $price_format= number_format($price,2,'.','');
        return $price_format;
    }

    public function getOrderSn(){
        $order_sn = 'TOOL'.date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        return $order_sn;
    }
    public function save_res_with_money_change($data,$id=1){

        $res= \Model\SizeschoolMoneyChange::where('transaction_id',$data['transaction_id'])->first();

       if(!$res){
           //$order_sn=$this->getOrderSn();
           //$order=\Model\Booking_order::where('order_sn',$order_sn)->first();

           $created_data['type']=6;
           $created_data['x_id']=$id;
           $created_data['transaction_id']=$data['transaction_id'];
           $created_data['price']=$this->get_price($data['total_fee']);
           $created_data['real_price']=$this->get_price($data['total_fee']);
           $created_data['return_json']=json_encode($data);
           $created_data['state']=2;
           $created_data['pay_time']=$data['time_end'];

           \Model\SizeschoolMoneyChange::create($created_data);
       }

return true;

    }
}

Log::DEBUG("begin notify");

$notify = new PayNotifyCallBack();

$notify->Handle(false);
