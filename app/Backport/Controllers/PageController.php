<?php

namespace App\Backport\Controllers;

use App\Models\Page;
use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class PageController extends Controller
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
            ->header('Pages')
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
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Page);

        $grid->name('Name')->editable()->sortable();
        $grid->slug('Slug')->editable()->sortable();
        $grid->column('URL')->display(function () {
            return '<a href="' . url('page/' . $this->slug ) . '" target="_blank">' . url('page') . '/<strong>' . $this->slug . '</strong></a>';
        });
        $grid->template('Template')->display(function ($template) {
            return "<span class='badge badge-secondary'>{$template}</span>";
        });
        $grid->created_at('Created')->display(function () {
            return '<strong>' . $this->created_at->format(config('settings.date_format')) . '</strong><br />' . $this->created_at->format('H:i:m');
        })->sortable();

        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        $grid->filter(function($filter){

            $filter->disableIdFilter();

            $filter->equal('slug', 'Slug');
            $filter->equal('name', 'Name');


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
        $show = new Show(Page::findOrFail($id));

        $show->id('Id');
        $show->template('Template');
        $show->name('Name');
        $show->title('Title');
        $show->slug('Slug');
        $show->content('Content');
        $show->extras('Extras');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->deleted_at('Deleted at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Page);

        $form->select('template', 'Template')->options(['with_subheader' => 'With subheader', 'without_subheader' => 'Without subheader', 'contact_form' => 'Contact form'])->default('with_subheader');
        $form->text('name', 'Page Name')->help('Only seen by admins');
        $form->text('title', 'Page Title');
        $form->text('slug', 'Page Slug (URL)')->rules(function ($form) {
            if (!$id = $form->model()->id) {
                return 'required|unique:pages,slug';
            } else {
                return 'required|unique:pages,slug,' . $id;
            }
        })->required();
        $form->editor('content', 'Content');

        $form->embeds('extras', 'Subheader', function ($form) {

            $form->text('subheader_title','Subheader Title');
            $form->text('subheader_icon', 'Subheader Icon');


        });

        $form->embeds('extras', 'Metas', function ($form) {

            $form->text('meta_title','Meta Title');
            $form->text('meta_description', 'Meta Description');

        });

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
        });

        return $form;
    }
}
