<?php

namespace App\Admin\Controllers;

use App\Model\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use GuzzleHttp\Client;
class UserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->id('Id');
        $grid->openid('Openid');
        $grid->nickname('名称');
        $grid->headimgurl('头像')->display(function($img){
            return '<img src="'.$img.'" width="30">';
        });
        $grid->country('国家');
        $grid->province('省份');
        $grid->city('城市');
        //$grid->create_time('Create time');
        //$grid->status('Status');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->id('Id');
        $show->openid('Openid');
        $show->nickname('Nickname');
        $show->headimgurl('Headimgurl');
        $show->country('Country');
        $show->province('Province');
        $show->city('City');
        $show->create_time('Create time');
        $show->status('Status');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->text('openid', 'Openid');
        $form->text('nickname', 'Nickname');
        $form->text('headimgurl', 'Headimgurl');
        $form->text('country', 'Country');
        $form->text('province', 'Province');
        $form->text('city', 'City');
        $form->number('create_time', 'Create time');
        $form->text('status', 'Status')->default('1');

        return $form;
    }

    //消息群发
    public function allsend(Content $content){
        $userInfo=User::all()->toArray();
        return $content
            ->header('微信用户')
            ->description('群发')
            ->body(view('admin.weixin.allsend',['data'=>$userInfo]));
    }

    public function allsenddo(){
        $arr=request()->input();
        $text=$arr['text'];
        $openid=explode(',',$arr['openid']);
        $access_token=getWxAccessToken();
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$access_token;
        $client=new Client();
        $send_arr=[
            'touser'=>$openid  ,
            'msgtype'=>'text',
            'text'=>[
                'content'=>$text
            ]
        ];
        $msg_json=json_encode($send_arr,JSON_UNESCAPED_UNICODE);
        $response=$client->request('POST',$url,[
            'body'=>$msg_json,
        ]);
        $res=json_decode($response->getBody(),true);
//        if($res['errcode']==0){
            echo 1;
//        }
    }
}
