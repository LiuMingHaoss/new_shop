<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('/goods', GoodsController::class);
    $router->resource('/order', OrderController::class);
    $router->resource('/wx_user', UserController::class);
    $router->resource('/msg', WxmediaController::class);
    $router->post('/msg/createdo', 'WxmediaController@createdo');   //添加素材
    $router->get('/user/allsend', 'UserController@allsend');   //消息群发
    $router->post('/user/allsenddo', 'UserController@allsenddo');   //消息群发







});
