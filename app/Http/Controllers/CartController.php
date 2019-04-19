<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Cart;
use App\Model\Goods;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
class CartController extends Controller
{
    //购物车添加
    public function cartAdd($goods_id=0){
        if(empty($goods_id)){
            echo '商品不存在';
        }
        $goodsInfo=Goods::where('id',$goods_id)->first();
        $cartInfo=[
            'goods_id'=>$goods_id,
            'goods_name'=>$goodsInfo->goods_name,
            'goods_price'=>$goodsInfo->goods_price,
            'buy_number'=>1,
            'uid'=>Auth::id(),
            'session_id'=>Session::getId(),
            'create_time'=>time(),
        ];
        $res=Cart::insertGetId($cartInfo);
        if($res){
            header('Refresh:2;url=/cartlist');
            echo '加入购物车成功';
        }else{
            echo '加入购物车失败';
        }

    }

    //购物车列表
    public function cartList(){
        $cartInfo=Cart::where('uid',Auth::id())->orderBy('id','desc')->get()->toArray();
        if($cartInfo){
            $total_price = 0;
            foreach($cartInfo as $k=>$v){
                $g = Goods::where(['id'=>$v['goods_id']])->first()->toArray();
                $total_price += $g['goods_price'];
                $goods_list[] = $g;
            }
            //展示购物车
            $data = [
                'goods_list' => $goods_list,
                'total'     => $total_price
            ];
            return view('cart.cartlist',$data);
        }else{
            echo '无购物车信息';
        }


    }
}
