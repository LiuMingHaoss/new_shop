<?php

namespace App\Admin\Controllers;

use App\Model\Wximage;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WximageController extends Controller
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
        $grid = new Grid(new Wximage);

        $grid->id('Id');
        $grid->openid('Openid');
        $grid->msg_type('素材类型');
        $grid->image_path('Image path')->display(function($img){
            return '<img src="'.$img.'" width="30">';
        });
        $grid->create_time('Create time');
        $grid->text('text');
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
        $show->openid('Openid');
        $show->msg_type('Msg type');
        $show->image_path('Image path');
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

        $form->text('openid', 'Openid');
        $form->text('msg_type', 'Msg type');
        $form->text('image_path', 'Image path');
        $form->number('create_time', 'Create time');

        return $form;
    }
}
