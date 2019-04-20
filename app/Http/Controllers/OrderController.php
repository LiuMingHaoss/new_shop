<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Cart;
use App\Model\Goods;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Model\Order;
use App\Model\OrderDetail;
class OrderController extends Controller
{
    //订单添加
    public function orderAdd(){
        $CartInfo=Cart::where('uid',Auth::id())->get()->toArray();

        //添加订单表
        $allprice=0;
        foreach($CartInfo as $k=>$v){
            $allprice+=$v['goods_price']*$v['buy_number'];
        }
        $order_no=Order::order_sn();
        $info=[
          'order_no'=>$order_no,
            'uid'=>Auth::id(),
            'order_amount'=>$allprice,
            'create_time'=>time()
        ];
        $oid=Order::insertGetId($info);

        //添加订单详情
        foreach($CartInfo as $k=>$v){
            $order_detail=[
              'oid'=>$oid,
                'goods_id'=>$v['goods_id'],
                'goods_name'=>$v['goods_name'],
                'goods_price'=>$v['goods_price'],
                'uid'=>Auth::id(),
            ];
            $res=OrderDetail::insertGetId($order_detail);
        }
        header('Refresh:3;url=/orderlist');
        echo "添加订单成功";
    }
    //订单列表
    public function orderList(){
        $orderInfo=Order::where(['uid'=>Auth::id()])->OrderBy('id','desc')->get()->toArray();
        return view('order/orderlist',['data'=>$orderInfo]);
    }
}
