<?php

namespace App\Backport\Controllers;

use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;
use Wiledia\Backport\Controllers\HasResourceActions;
use Illuminate\Routing\Controller;

class RoleController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header(trans('admin.roles'))
            ->description(trans('admin.list'))
            ->body($this->grid()->render());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.roles'))
            ->description(trans('admin.detail'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.roles'))
            ->description(trans('admin.edit'))
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
            ->header(trans('admin.roles'))
            ->description(trans('admin.create'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $roleModel = config('backport.database.roles_model');

        $grid = new Grid(new $roleModel());

        $grid->disableRowSelector();

        $grid->name(trans('admin.name'))->editable();

        $grid->slug(trans('admin.slug'));

        $grid->permissions(trans('admin.permission'))->pluck('slug')->badge('secondary');

        $grid->actions(function ($actions) {
            $actions->disableView();
            if ($actions->row->slug == 'admin') {
                $actions->disableDelete();
            }
        });

        $grid->filter(function($filter){

            $filter->disableIdFilter();

            $filter->like('name', 'Name');
            $filter->like('slug', 'Slug');

        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $roleModel = config('backport.database.roles_model');

        $show = new Show($roleModel::findOrFail($id));
        $show->slug(trans('admin.slug'));
        $show->name(trans('admin.name'));
        $show->permissions(trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('slug');
        })->label();
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $permissionModel = config('backport.database.permissions_model');
        $roleModel = config('backport.database.roles_model');

        $form = new Form(new $roleModel());

        $form->text('slug', trans('admin.slug'))->rules('required')->required();
        $form->text('name', trans('admin.name'))->rules('required');
        $form->listbox('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('slug', 'id'));

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
        });

        return $form;
    }
}
