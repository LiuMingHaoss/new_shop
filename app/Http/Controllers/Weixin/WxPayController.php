<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Weixin\WXBizDataCryptController;
use Illuminate\Support\Str;
class WxPayController extends Controller
{
    public $weixin_unifiedorder_url='https://api.mch.weixin.qq.com/pay/unifiedorder';   //统一下单接口
    public $notify_url='http://1809liuminghao.comcto.com/weixin/pay/notify';    //支付回调

    /**
     *  微信支付测试
     */
    public function pay($oid){
        echo $oid;die;
        $total_fee = 1;     //用户需要支付的总金额
        $order_id=time().'liuminghao'.mt_rand(10000,99999);  //随机生成测试订单号
        $order_info = [
            'appid'         => env('WEIXIN_APPID_0'),     //微信支付绑定的服务号的APPID
            'mch_id'        => env('WEIXIN_MCH_ID'),     //商户id
            'nonce_str'     => Str::random(16),     //随机字符串
            'sign_type'     => 'MD5',
            'body'          =>'测试订单-'.mt_rand(1111,9999).Str::random(6),
            'out_trade_no'  => $order_id,       //本地订单号
            'total_fee'     => $total_fee,
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],  //客户端IP
            'notify_url'    =>$this->notify_url,    //通知回调地址
            'trade_type'    =>'NATIVE'  //交易类型
        ];
        $this->values=[];
        $this->values=$order_info;
        $this->SetSign();
        $xml = $this->ToXml();  //将数组转换为XML
        $rs = $this->postXmlCurl($xml,$this->weixin_unifiedorder_url,$useCert=false,$second=30);
        $data=simplexml_load_string($rs);
//        echo 'return_code: '.$data->return_code;echo '<br>';
//		echo 'return_msg: '.$data->return_msg;echo '<br>';
//		echo 'appid: '.$data->appid;echo '<br>';
//		echo 'mch_id: '.$data->mch_id;echo '<br>';
//		echo 'nonce_str: '.$data->nonce_str;echo '<br>';
//		echo 'sign: '.$data->sign;echo '<br>';
//		echo 'result_code: '.$data->result_code;echo '<br>';
//		echo 'prepay_id: '.$data->prepay_id;echo '<br>';
//		echo 'trade_type: '.$data->trade_type;echo '<br>';
//        echo 'code_url: '.$data->code_url;echo '<br>';




        $data=[
            'code_url'=>$data->code_url
        ];


        return view('weixin.test',$data);
    }
}
