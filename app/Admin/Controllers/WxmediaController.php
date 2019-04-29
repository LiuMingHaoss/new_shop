<?php

namespace App\Admin\Controllers;

use App\Model\Wximage;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Session\Storage;
class WxmediaController extends Controller
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
            ->body(view('admin.weixin.addimg'));
    }
    public function createdo(Request $request){

        $file=$request->file('media');
        $ext=$file->getClientOriginalExtension();
        $save_path='upload';
        //生成文件名
        $file_name=date('ymd').Str::random(5).'.'.$ext;
        $rs=$file->storeAs($save_path,$file_name);
        $access_token=getWxAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type=image';
        $client=new Client();

        $response = $client->request('post',$url,[
            'multipart' => [
                [
                    'name' => 'media',
                    'contents' => fopen($rs, 'r'),
                ]
            ]
        ]);

        $json =  $response->getBody();
        $media=json_decode($json,true);
        $info=[
            'type'=>$media['type'],
            'media_id'=>$media['media_id'],
            'create_time'=>$media['created_at']
        ];
        $res=DB::table('wx_media')->insert($info);
        if($res){
            return redirect('/admin/msg');
        }
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Wximage);

        $grid->id('Id');
        $grid->type('图片类型');
        $grid->media_id('Media id');
        $grid->create_time('Create time');

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
        $show = new Show(Wximage::findOrFail($id));

        $show->id('Id');
        $show->type('Type');
        $show->media_id('Media id');
        $show->create_time('Create time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Wximage);

        $form->text('type', 'Type');
        $form->text('media_id', 'Media id');
        $form->number('create_time', 'Create time');

        return $form;
    }
}
