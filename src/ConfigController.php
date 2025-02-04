<?php

namespace Encore\Admin\Config;

use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\App;

class ConfigController
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Config')
            ->description('list')
            ->body($this->grid());
    }

    /**
     * Edit interface.
     *
     * @param int     $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Config')
            ->description('edit')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Config')
            ->description('create')
            ->body($this->form());
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('Config')
            ->description('detail')
            ->body(Admin::show(ConfigModel::findOrFail($id), function (Show $show) {
                $show->id();
                $show->name();
                $show->value();
                $show->description();
                $show->created_at();
                $show->updated_at();
            }));
    }

    public function grid()
    {
        $grid = new Grid(new ConfigModel());

        $grid->id('ID')->sortable();
        $grid->name()->display(function ($name) {
            return "<a tabindex=\"0\" class=\"btn btn-xs btn-twitter\" role=\"button\" data-toggle=\"popover\" data-html=true title=\"Usage\" data-content=\"<code>config('$name');</code>\">$name</a>";
        });
        $grid->value();
        $grid->description();

        $grid->created_at();
        $grid->updated_at();

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('name');
            $filter->like('value');
        });

        return $grid;
    }

    public function form()
    {
           $form = new Form(new ConfigModel());

     
        $form->display('id', 'ID');
         $form->select('dil', 'İçerik Dili')->options(config('admin.extensions.config.lang', [
            'tr-TR' => 'Türkçe',
            'en' => 'İngilizce',
            'ar' => 'Arapça',

        ]))->rules('required')->value(App::currentLocale());
        $form->text('name')->rules('required');
        if (config('admin.extensions.config.valueEmptyStringAllowed', false)) {
            if(config('admin.extensions.config.editor', false))
               switch (config('admin.extensions.config.editor', false)):
                   case 'tinymce' :  $form->tinymce('value');  break;
                  // case 'tinymce' :  $form->tinymce('value');  break;
                //   case 'tinymce' :  $form->tinymce('value');  break;
                   default :  $form->textarea('value'); break;
                   endswitch;
            else $form->textarea('value');
        } else {

            if(config('admin.extensions.config.editor', false))
                switch (config('admin.extensions.config.editor', false)):
                    case 'tinymce' :  $form->tinymce('value')->rules('required');  break;
                    // case 'tinymce' :  $form->tinymce('value');  break;
                    //   case 'tinymce' :  $form->tinymce('value');  break;
                    default :  $form->textarea('value')->rules('required');; break;
                endswitch;
            else $form->textarea('value');


        }
         $form->textarea('description');
        
            //    $form->display('created_at');
       // $form->display('updated_at');

        return $form;
    }
}
