<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Goods;
use Illuminate\Support\Facades\Redis;
class GoodsController extends Controller
{
    //商品列表
    public function goodsList(){
        $goodsInfo=Goods::all()->toArray();
        return view('goods/goodslist',['data'=>$goodsInfo]);
    }

    //商品详情
    public function goodsDesc($goods_id){
        if(!$goods_id){
            die('商品不存在');
        }
        $goodsInfo=Goods::where('id',$goods_id)->first()->toArray();

        //浏览历史
        Goods::where('id',$goods_id)->update(['last_time'=>time()]);
        $last_look=Goods::orderBy('last_time','desc')->get()->toArray();

        //数据库改变浏览次数
//        $view=$goodsInfo['view']+1;
//        Goods::where('id',$goods_id)->update(['view'=>$view]);

        //redis改变浏览次数
        $key='view:goods_id:'.$goods_id;
//        Redis::set($key,$goodsInfo['view']);
        $view=Redis::incr($key);

        //浏览次数排行
        $goods_info=$this->getStore($view,$goods_id);
        $url='http://1809liuminghao.comcto.com/goods_desc/'.$goods_id;
        return view('goods.goodsdesc',['data'=>$goodsInfo,'view'=>$view,'goodsInfo'=>$goods_info,'last_look'=>$last_look,'url'=>$url]);
    }

    //根据浏览排行商品数据
    public function getStore($view,$goods_id){
        //商品浏览排行
        $k='ss:goods:view';
        Redis::zadd($k,$view,$goods_id);
        $list=Redis::ZREVRANGE($k,0,1000,true);     //倒序
        $goods_info=[];
        foreach($list as $k=>$v){
            $goodsDesc=Goods::where(['id'=>$k])->first()->toArray();
            $goodsDesc['view']=$list[$k];
            $goods_info[]=$goodsDesc;
        }
        return $goods_info;
    }
}
