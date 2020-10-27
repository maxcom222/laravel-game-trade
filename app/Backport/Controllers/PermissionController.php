<?php

namespace App\Backport\Controllers;

use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Show;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class PermissionController extends Controller
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
            ->header(trans('admin.permissions'))
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
            ->header(trans('admin.permissions'))
            ->description(trans('admin.detail'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.permissions'))
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
            ->header(trans('admin.permissions'))
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
        $permissionModel = config('backport.database.permissions_model');

        $grid = new Grid(new $permissionModel());

        $grid->disableRowSelector();

        $grid->slug(trans('admin.slug'));
        $grid->name(trans('admin.name'));

        $grid->http_path(trans('admin.route'))->display(function ($path) {
            return collect(explode("\r\n", $path))->map(function ($path) {
                $method = $this->http_method ?: ['ANY'];

                if (Str::contains($path, ':')) {
                    list($method, $path) = explode(':', $path);
                    $method = explode(',', $method);
                }

                $method = collect($method)->map(function ($name) {
                    return strtoupper($name);
                })->map(function ($name) {
                    return "<span class='badge badge-primary'>{$name}</span>";
                })->implode('&nbsp;');

                if (!empty(config('backport.route.prefix'))) {
                    $path = '/'.trim(config('backport.route.prefix'), '/').$path;
                }

                return "<div style='margin-bottom: 5px;'>$method<code>$path</code></div>";
            })->implode('');
        });

        $grid->roles(trans('admin.roles'))->pluck('slug')->badge('secondary');


        $grid->filter(function($filter){


            $filter->column(1/2, function ($filter) {
                // Add a column filter
                $filter->like('name', 'Name');

                $filter->equal('platform_id', 'Platform')->select(function () {
                    return array(5 => 1, 12 => 2, 3 => 1, 4 => 2, 15 => 1, 18 => 2 );
                });
            });

            $filter->column(1/2, function ($filter) {
                // Add a column filter
                $filter->like('publisher', 'Publisher');

                $filter->between('release_date', 'Release')->date();

                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'yes':
                            // custom complex query if the 'yes' option is selected
                            $query->has('somerelationship');
                            break;
                        case 'no':
                            $query->doesntHave('somerelationship');
                            break;
                    }
                }, 'Listings', 'listings')->radio([
                    'active' => 'Active',
                    'none' => 'None',
                ]);

            });


        });

        $grid->actions(function ($actions) {
            $actions->disableView();
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
        $permissionModel = config('backport.database.permissions_model');

        $show = new Show($permissionModel::findOrFail($id));

        $show->slug(trans('admin.slug'));
        $show->name(trans('admin.name'));

        $show->http_path(trans('admin.route'))->as(function ($path) {
            return collect(explode("\r\n", $path))->map(function ($path) {
                $method = $this->http_method ?: ['ANY'];

                if (Str::contains($path, ':')) {
                    list($method, $path) = explode(':', $path);
                    $method = explode(',', $method);
                }

                $method = collect($method)->map(function ($name) {
                    return strtoupper($name);
                })->map(function ($name) {
                    return "<span class='label label-primary'>{$name}</span>";
                })->implode('&nbsp;');

                if (!empty(config('backport.route.prefix'))) {
                    $path = '/'.trim(config('backport.route.prefix'), '/').$path;
                }

                return "<div style='margin-bottom: 5px;'>$method<code>$path</code></div>";
            })->implode('');
        });

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

        $form = new Form(new $permissionModel());

        $form->text('slug', trans('admin.slug'))->rules('required');
        $form->text('name', trans('admin.name'))->rules('required');

        $form->multipleSelect('http_method', trans('admin.http.method'))
            ->options($this->getHttpMethodsOptions())
            ->help(trans('admin.all_methods_if_empty'));
        $form->textarea('http_path', trans('admin.http.path'));

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
        });

        return $form;
    }

    /**
     * Get options of HTTP methods select field.
     *
     * @return array
     */
    protected function getHttpMethodsOptions()
    {
        $permissionModel = config('backport.database.permissions_model');

        return array_combine($permissionModel::$httpMethods, $permissionModel::$httpMethods);
    }
}
