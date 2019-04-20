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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//购物车添加
Route::get('/addcart/{goods_id?}', 'CartController@cartAdd');
//购物车列表
Route::get('/cartlist', 'CartController@cartList');
//订单添加
Route::get('/orderadd', 'OrderController@orderAdd');
Route::get('/orderlist', 'OrderController@orderList');

//商品列表
Route::get('/goods', 'CartController@goodsList');

//微信支付
Route::get('/weixin_pay/{oid}', 'Weixin\WxPayController@pay');
Route::post('/weixin/pay/notify', 'Weixin\WxPayController@notify');

