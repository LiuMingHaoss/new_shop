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
use App\Model\Wxscene;
use App\Model\Goods;
use App\Model\ShopGoods;
use Illuminate\Support\Facades\Redis;
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
        if($data->MsgType=='event'){
            if($event=='SCAN'){
                $info=[
                    'openid'=>$openid,
                    'scene_id'=>$data->EventKey,
                    'create_time'=>$data->CreateTime
                ];
                $res=Wxscene::insertGetId($info);
                if($res){
                    $v=DB::table('shop_goods')->where('goods_id',14)->first();
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
                              <Title><![CDATA['.$v->goods_name.'hellow,欢迎回来'.']]></Title>
                              <Description><![CDATA['.$v->goods_desc.']]></Description>
                              <PicUrl><![CDATA['.$img_url.']]></PicUrl>
                              <Url><![CDATA['.$desc_url.']]></Url>
                            </item>
                          </Articles>
                        </xml>';
                }
            }else if($event=='subscribe'){
                $v=DB::table('shop_goods')->where('goods_id',14)->first();
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
                              <Title><![CDATA['.$v->goods_name.'.hellow,欢迎加入'.']]></Title>
                              <Description><![CDATA['.$v->goods_desc.']]></Description>
                              <PicUrl><![CDATA['.$img_url.']]></PicUrl>
                              <Url><![CDATA['.$desc_url.']]></Url>
                            </item>
                          </Articles>
                        </xml>';
            }

        }else if($data->MsgType=='text'){       //用户发送文字消息
//            $info=[
//                'openid'=>$openid,
//                'create_time'  => time(),
//                'msg_type'  => 'text',
//                'text'=>$data->Content,
//            ];

//            $res=DB::table('wx_image')->insert($info);
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
            }else{

                //搜索商品

                $where[]=['goods_name','like',"%$Content%"];
                $goods=ShopGoods::where($where)->first();
                if($goods!=null){
                    $goods=$goods->toArray();
                    $img_url='http://1809liuminghao.comcto.com/goodsImg/'.$goods['goods_img'];
                    $desc_url='http://1809liuminghao.comcto.com/weixin/goods?goods_id='.$goods['goods_id'];
                    echo '
                        <xml>
                          <ToUserName><![CDATA['.$openid.']]></ToUserName>
                          <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                          <CreateTime>'.time().'</CreateTime>
                          <MsgType><![CDATA[news]]></MsgType>
                          <ArticleCount>1</ArticleCount>
                          <Articles>
                            <item>
                              <Title><![CDATA['.$goods['goods_name'].']]></Title>
                              <Description><![CDATA['.$goods['goods_desc'].']]></Description>
                              <PicUrl><![CDATA['.$img_url.']]></PicUrl>
                              <Url><![CDATA['.$desc_url.']]></Url>
                            </item>
                          </Articles>
                        </xml>';
                }else{
                    $rand_num=rand(9,36);
                    $goodss=ShopGoods::where('goods_id',$rand_num)->first()->toArray();
                    $img_url='http://1809liuminghao.comcto.com/goodsImg/'.$goodss['goods_img'];
                    $desc_url='http://1809liuminghao.comcto.com/weixin/goods?goods_id='.$goodss['goods_id'];
                    echo '
                        <xml>
                          <ToUserName><![CDATA['.$openid.']]></ToUserName>
                          <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                          <CreateTime>'.time().'</CreateTime>
                          <MsgType><![CDATA[news]]></MsgType>
                          <ArticleCount>1</ArticleCount>
                          <Articles>
                            <item>
                              <Title><![CDATA['.'无此商品，为您推荐：'.$goodss['goods_name'].']]></Title>
                              <Description><![CDATA['.$goodss['goods_desc'].']]></Description>
                              <PicUrl><![CDATA['.$img_url.']]></PicUrl>
                              <Url><![CDATA['.$desc_url.']]></Url>
                            </item>
                          </Articles>
                        </xml>';
                }

            }


        }else if($data->MsgType=='image'){
            $access_token=getWxAccessToken();
            var_dump($access_token);
            //请求地址
            $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.'&media_id='.$data->MediaId;

            //接口数据
            $client=new Client();
            $response = $client->get(new Uri($url));
            $headers = $response->getHeaders();     //获取 响应 头信息
            $file_info = $headers['Content-disposition'][0];            //获取文件名
            $file_name =  rtrim(substr($file_info,-20),'"');
            $new_file_name = 'weixin/' .substr(md5(time().mt_rand()),10,8).'_'.$file_name;
            //保存文件
            $rr = Storage::disk('local')->put($new_file_name,$response->getBody());       //保存文件
            var_dump($rr);
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
        $goods_id=$_GET['goods_id'];
        $goodsInfo=ShopGoods::where('goods_id',$goods_id)->first()->toArray();

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
        return view('weixin.goods',['jsconfig'=>$js_config,'goodsInfo'=>$goodsInfo]);
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
            header('Refresh:3;url=/goods_desc/3');
                echo '欢迎回来,'.$userInfo['nickname'].'  正在跳转福利页面';


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
            header('Refresh:3;url=/goods_desc/3');
                echo '欢迎你,'.$userInfo['nickname'].' 正在跳转福利页面';

        }

//        return view('weixin.user',$data);
    }

    //生成带参数的二维码
    public function scene(){
        $access_token=getWxAccessToken();
        $url1='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        $info=[
            'expire_seconds'=>604800,
            'action_name'=>'QR_SCENE',
            'action_info'=>[
                'scene'=>[
                    'scene_id'=>4610
                ]
            ]
        ];
        $json_arr=json_encode($info,JSON_UNESCAPED_UNICODE);

        $client=new Client();
        $response=$client->request('POST',$url1,[
           'body'=>$json_arr
        ]);
        $ticket=json_decode($response->getBody(),true);
        $url2='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket['ticket'];
        echo $url2;

    }

    //自定义菜单
    public function menu(){
        $access_token=getWxAccessToken();
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
        $client = new Client();
        $arr=[
          "button"=>[
              [
                  "type"=>"view",
                  "name"=>"最新福利",
                  "url"=>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxfb5d95e795f0a9d3&redirect_uri=http%3A%2F%2F1809liuminghao.comcto.com%2Fweixin%2Fwxweb&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect"
              ],
              [
              "type"=>"view",
              "name"=>"签到",
              "url"=>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxfb5d95e795f0a9d3&redirect_uri=http%3A%2F%2F1809liuminghao.comcto.com%2Fsign&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect"
          ]
          ]
        ];
        $json_arr=json_encode($arr,JSON_UNESCAPED_UNICODE);
        $response = $client->request('POST',$url,[
           'body'=>$json_arr
        ]);
        echo $response->getBody();
    }

    //考试
    public function sign(){
        $code=$_GET['code'];
        $url1='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET').'&code='.$code.'&grant_type=authorization_code';
        $arr=json_decode(file_get_contents($url1),true);
        $access_token=$arr['access_token'];
        $url2='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$arr['openid'].'&lang=zh_CN';
        $userInfo=json_decode(file_get_contents($url2),true);
        print_r($userInfo);
    }

    //考试
    public function tag(){
        $access_token=getWxAccessToken();
        $url='https://api.weixin.qq.com/cgi-bin/tags/create?access_token='.$access_token;
        $client=new Client;
        $response=$client->request('POST',$url,[
            'body'=>'{   "tag" : {     "name" : "1809b"//php   } }'
        ]);
        echo $response->getBody();
    }
    //标签
    public function tagdo(){
        $access_token=getWxAccessToken();
        $url='https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token='.$access_token;
        $client=new Client;
        $response=$client->request('POST',$url,[
            'body'=>'{   
                        "openid_list" : [//粉丝列表    
                        "og1Jd1HtDM0K9NdrNBTJB1cbGT5s",    
                        "og1Jd1KlcDOxObfJuCnzCe5-CZ68"   ],   
                        "tagid" : 100
                    }'
        ]);
        echo $response->getBody();
    }
}
