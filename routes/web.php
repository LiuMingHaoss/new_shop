<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/a', function () {
    echo urlencode("http://1809liuminghao.comcto.com/sign");
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//购物车添加
Route::get('/addcart/{goods_id?}', 'CartController@cartAdd');
//购物车列表
Route::get('/cartlist', 'CartController@cartList');
//订单添加
Route::get('/orderadd', 'OrderController@orderAdd');
Route::get('/orderlist', 'OrderController@orderList');
Route::get('/delOrder', 'OrderController@delOrder');


//商品列表
Route::get('/goods', 'GoodsController@goodsList');
//商品详情
Route::get('/goods_desc/{goods_id}', 'GoodsController@goodsDesc');


//微信支付
Route::get('/weixin_pay/{oid}', 'Weixin\WxPayController@pay');
Route::post('/weixin/pay/notify', 'Weixin\WxPayController@notify');

//提示支付成功
Route::get('/order/paystatus', 'OrderController@payStatus');
Route::get('/pay/success','Weixin\WxPayController@paySuccess');

//weixin jssdk
Route::get('/weixin/test','Weixin\JssdkController@jsTest');
Route::get('/weixin/getImg','Weixin\JssdkController@getImg'); //获取jssdk上传的照片
Route::get('/weixin/upload','Weixin\JssdkController@upload'); //下载的照片

//微信推送消息
Route::get('/index','Weixin\TestController@valid');
Route::post('/index','Weixin\TestController@wxEvent');
Route::get('/weixin/goods','Weixin\TestController@goodsdesc');

Route::get('/weixin/wxweb','Weixin\TestController@wxweb');  //授权回调地址

//生成带参数的二维码
Route::get('/scene','Weixin\TestController@scene');

//自定义菜单
Route::get('/menu','Weixin\TestController@menu');
//签到
Route::get('/sign','Weixin\TestController@sign');

//用户标签
Route::get('/tag','Weixin\TestController@tag');
Route::get('/tagdo','Weixin\TestController@tagdo');







