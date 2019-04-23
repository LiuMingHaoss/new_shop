<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
class JssdkController extends Controller
{
    //
    public function jsTest(){

        //计算签名
        $nonceStr=Str::random(10);
        $ticket = getTicket();
        $timestamp=time();
        $current_url=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//        echo 'nonceStr:'.$nonceStr;echo '</br>';
//        echo 'ticket:'.$ticket;echo '</br>';
//        echo 'timestamp:'.$timestamp;echo '</br>';
//        echo 'current_url:'.$current_url;echo '</br>';

        $string = "jsapi_ticket=$ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$current_url";
        $sign=sha1($string);

            $js_config=[
                'appId'=>env('WX_APPID'),   //公众号appid
                'timestamp'=>$timestamp,            //时间戳
                'nonceStr'=>$nonceStr,    //随机字符串
                'signature'=>$sign,                //签名

            ];


        return view('weixin.jssdk',['jsconfig'=>$js_config]);
    }

    //获取照片
    public function getImg(){
        echo '<pre>';print_r($_GET);echo '</pre>';
    }

    //下载照片
    public function upload(){
        $media_id=$_GET['media_id'];
    }
}
