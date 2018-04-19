<?php
/**
 * 引入wx接口文件
 * @param 
 * @return mixed
 */

dd('da');
//require_once "WxPay.JsApiPay.php";
class import_wx{
    public function import(){
        ini_set('date.timezone','Asia/Shanghai');
        require_once "../lib/WxPay.Api.php";
        dd('das');
        require_once "WxPay.JsApiPay.php";
        dd('da');
        require_once 'log.php';
        dd('da');
    }
}

?>