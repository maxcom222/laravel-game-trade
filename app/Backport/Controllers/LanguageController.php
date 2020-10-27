<?php

namespace App\Backport\Controllers;

use App\Models\Language;
use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class LanguageController extends Controller
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
            ->header('Languages')
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
        $grid = new Grid(new Language);

        $grid->disableRowSelector();
        $grid->disableExport();

        $grid->name('Language Name')->editable()->sortable();
        $grid->active('Active')->using(['1' => 'Yes', '0' => 'No']);

        $grid->default('Default')->using(['1' => 'Yes', '0' => 'No']);

        $grid->filter(function($filter){
            // Remove the default id filter
            $filter->disableIdFilter();
            // Add a column filter
            $filter->like('name', 'Name');
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
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Language::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->description('Description');
        $show->deleted_at('Deleted at');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Language);

        $form->text('name', 'Language name')->rules('required')->required();
        $form->text('native', 'Native name');

        $form->text('abbr', 'Code (ISO 639-1)')->rules(function ($form) {
            if (!$id = $form->model()->id) {
                return 'required|unique:languages,abbr';
            } else {
                return 'required|unique:languages,abbr,' . $id;
            }
        })->required();

        $form->switch('active', 'Active');
        $form->switch('default', 'Default');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
        });

        return $form;
    }
}
