<?php

namespace App\Backport\Controllers;

use App\Models\MenuItem;
use App\Models\Page;
use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;
use Wiledia\Backport\Tree;


class MenuItemController extends Controller
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
            ->header('Footer Links')
            ->body(MenuItem::tree(function (Tree $tree) {
                $tree->branch(function ($branch) {

                    $payload = "<strong>{$branch['name']}</strong>";

                    if (isset($branch['type'])) {
                        switch ($branch['type']) {
                            case 'page_link':
                                $page = Page::find($branch['page_id']);
                                if (isset($page)) {
                                    $payload .= "&nbsp;&nbsp;&nbsp;<a href=\"" . url('page/' . $page->slug) . "\" class=\"dd-nodrag\" target=\"_blank\"><i class=\"fas fa-file\"></i> page/" . $page->slug . "</a>";
                                }
                                break;
                            case 'internal_link':
                                $payload .= "&nbsp;&nbsp;&nbsp;<a href=\"" . url($branch['link']) . "\" class=\"dd-nodrag\" target=\"_blank\"><i class=\"fas fa-link\"></i> " . $branch['link'] . "</a>";
                                break;
                            case 'external_link':
                                $payload .= "&nbsp;&nbsp;&nbsp;<a href=\"" . $branch['link'] . "\" class=\"dd-nodrag\" target=\"_blank\"><i class=\"fas fa-link\"></i> " . $branch['link'] . "</a>";
                                break;
                        };
                    };

                    return $payload;
                });
            }));
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
        $grid = new Grid(new MenuItem);

        $grid->id('Id');
        $grid->name('Name');
        $grid->type('Type');
        $grid->link('Link');
        $grid->page_id('Page id');
        $grid->parent_id('Parent id');
        $grid->lft('Lft');
        $grid->rgt('Rgt');
        $grid->depth('Depth');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        $grid->deleted_at('Deleted at');

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
        $show = new Show(MenuItem::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->type('Type');
        $show->link('Link');
        $show->page_id('Page id');
        $show->parent_id('Parent id');
        $show->lft('Lft');
        $show->rgt('Rgt');
        $show->depth('Depth');
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
        $form = new Form(new MenuItem);

        $form->text('name', 'Name');
        $form->select('parent_id', trans('admin.parent_id'))->options(MenuItem::selectOptions());
        $form->hidden('type', 'Type');
        $form->hidden('page_id', 'Page ID');
        $form->pagelink('link', 'Link');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
        });

        return $form;
    }
}
