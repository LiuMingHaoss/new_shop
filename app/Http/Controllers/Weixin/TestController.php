<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Model\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Psr7\Uri;
class TestController extends Controller
{
    //微信推送
    public function valid(){
        echo $_GET['echostr'];
    }
    public function wxEvent(){
        $content=file_get_contents('php://input');

        //记录日志
        $time =date('Y-m-d H:i:s');
        $str =$time . $content . "\n";
        is_dir('logs')or mkdir('logs',0777,true);
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);

        //解析XML 将xml字符串转化为对象
        $data=simplexml_load_string($content);

        $wx_id =$data->ToUserName;      //公众号id
        $event=$data->Event;            //事件类型
        $openid=$data->FromUserName;    //用户openid
        $Content=$data->Content;
        if($data->MsgType=='text'){
            $info=[
                'openid'=>$openid,
                'create_time'  => time(),
                'msg_type'  => 'text',
                'text'=>$data->Content,
            ];
            $res=DB::table('wx_image')->insert($info);
            if($Content=='最新商品'){
                $goodsInfo=DB::table('shop_goods')->orderBy('create_time','desc')->limit(5)->get()->toArray();
                foreach($goodsInfo as $k=>$v){
                    $img_url='http://1809liuminghao.comcto.com/goodsImg/'.$v->goods_img;
                    $desc_url='http://1809liuminghao.comcto.com/weixin/goods?goods_id='.$v->goods_id;
                    echo '
                        <xml>
                          <ToUserName><![CDATA['.$openid.']]></ToUserName>
                          <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                          <CreateTime>'.time().'</CreateTime>
                          <MsgType><![CDATA[news]]></MsgType>
                          <ArticleCount>1</ArticleCount>
                          <Articles>
                            <item>
                              <Title><![CDATA['.$v->goods_name.']]></Title>
                              <Description><![CDATA['.$v->goods_desc.']]></Description>
                              <PicUrl><![CDATA['.$img_url.']]></PicUrl>
                              <Url><![CDATA['.$desc_url.']]></Url>
                            </item>
                          </Articles>
                        </xml>';
                }
            }


        }else if($data->MsgType=='image'){
            $access_token=getWxAccessToken();
            var_dump($access_token);die;
            //请求地址
            $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.'&media_id='.$data->MediaId;
            var_dump($url);die;
            //接口数据
            $client=new Client();
            $response = $client->get(new Uri($url));
            $headers = $response->getHeaders();     //获取 响应 头信息
            var_dump($headers);die;
            $file_info = $headers['Content-disposition'][0];            //获取文件名
            $file_name =  rtrim(substr($file_info,-20),'"');
            $new_file_name = 'weixin/' .substr(md5(time().mt_rand()),10,8).'_'.$file_name;
            //保存文件
            $rs = Storage::put($new_file_name, $response->getBody());       //保存文件

            $info=[
                'openid'=>$openid,
                'create_time'  => time(),
                'msg_type'  => 'image',
                'image_path'=>$data->PicUrl,
            ];
            $res=DB::table('wx_image')->insert($info);
            if($res){
                echo '图片信息入库成功';
            }else{
                echo '图片信息入库失败';
            }
            echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wx_id.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.'图片不错 '.']]></Content></xml>';

        }
        echo "SUCCESS";
    }
    //商品详情
    public function goodsdesc(){
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
        return view('weixin.goods',['jsconfig'=>$js_config]);
    }

    //微信授权回调
    public function wxweb(){
        $code=$_GET['code'];
        $url1='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET').'&code='.$code.'&grant_type=authorization_code';
        $arr=json_decode(file_get_contents($url1),true);
        $url2='https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN';
        $userInfo=json_decode(file_get_contents($url2),true);


        $users=User::where('openid',$userInfo['openid'])->first();
        if($users){
            $data=[
                'data'=>'欢迎回来,'.$userInfo['nickname'],
            ];
        }else{
            $user_info=[
                'openid'=>$userInfo['openid'],
                'nickname'=>$userInfo['nickname'],
                'country'=>$userInfo['country'],
                'province'=>$userInfo['province'],
                'city'=>$userInfo['city'],
                'headimgurl'=>$userInfo['headimgurl'],
                'create_time'=>time(),
            ];
            $res=User::insertGetId($user_info);
            $data=[
                'data'=>'欢迎你,'.$userInfo['nickname'],
            ];
        }

        return view('weixin.user',$data);
    }
}
