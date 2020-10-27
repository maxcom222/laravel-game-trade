<?php

namespace App\Backport\Controllers;

use App\Models\Comment;
use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class CommentController extends Controller
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
            ->header('Comments')
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
        $grid = new Grid(new Comment);

        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableActions();

        $grid->column('User')->display(function () {
            if ($this->user->isOnline()) {
                $status = '<i class="fa fa-circle text-success"></i> Online';
            } else {
                $status = '<i class="fa fa-circle text-danger"></i> Offline';
            }

            return <<<EOT
<div class="image-text">
    <img src="{$this->user->avatar_square_tiny}" />
    <div class="content">
        <div class="top">
            <strong><a href="{$this->user->url}" target="_blank">{$this->user->name}</a></strong>
        </div>
        <div class="bottom">
            $status
        </div>
    </div>
</div>
EOT;
        });

        $grid->content('Content');

        $grid->column('Item')->display(function () {

            if ($this->type == 'game' ) {
                if (isset($this->game)) {
                    return <<<EOT
<div class="image-text">
    <img src="{$this->game->image_square_tiny}" />
    <div class="content">
        <div class="top">
            <strong><i class="fa fa-gamepad"></i> <a href="{$this->game->url_slug}#!comments" target="_blank">{$this->game->name}</a></strong>
        </div>
        <div class="bottom">
            <span class="badge badge-dark" style="background-color: {$this->game->platform->color}; margin-right: 10px;">{$this->game->platform->name}</span><i class="fa fa-calendar"></i> {$this->game->release_date->format('Y')}
        </div>
    </div>
</div>
EOT;
                } else {
                    return '<span class="badge badge-danger">
                        <i class="fa fa-ban"></i> Removed
                    </span>';
                }
            }
            if ($this->type == 'listing' ) {
                if (isset($this->listing)) {
                    return <<<EOT
<div class="image-text">
    <img src="{$this->listing->game->image_square_tiny}" />
    <div class="content">
        <div class="top">
            <strong><i class="fa fa-tag"></i> <a href="{$this->listing->url_slug}#!comments" target="_blank">{$this->listing->game->name}</a></strong>
        </div>
        <div class="bottom">
            <span class="badge badge-dark" style="background-color: {$this->listing->game->platform->color}; margin-right: 10px;">{$this->listing->game->platform->name}</span><i class="fa fa-calendar"></i> {$this->listing->game->release_date->format('Y')}
        </div>
    </div>
</div>
EOT;
                } else {
                    return '<span class="badge badge-danger">
                        <i class="fa fa-ban"></i> Removed
                    </span>';
                }
            }
            if ($this->type == 'article' ) {
                if (isset($this->article)) {
                    return <<<EOT
<div class="image-text">
    <img src="{$this->article->image_square_tiny}" />
    <div class="content">
        <div class="top">
            <strong><i class="fa fa-newspaper"></i> <a href="{$this->article->url_slug}#!comments" target="_blank">{$this->article->title}</a></strong>
        </div>
        <div class="bottom">
            <i class="fa fa-calendar"></i> {$this->article->created_at->format(Config::get('settings.date_format'))}
        </div>
    </div>
</div>
EOT;
                } else {
                    return '<span class="badge badge-danger">
                        <i class="fa fa-ban"></i> Removed
                    </span>';
                }
            }


        });



        $grid->created_at('Created')->display(function () {
            return '<strong>' . $this->created_at->format(config('settings.date_format')) . '</strong><br />' . $this->created_at->format('H:i:m');
        });


        $grid->filter(function($filter){

            $filter->disableIdFilter();


            $filter->equal('user_id', 'User ID');


            $filter->where(function ($query) {
                switch ($this->input) {
                    case 'game':
                        $query->where('commentable_type', 'App\Models\Game');
                        break;
                    case 'listing':
                        $query->where('commentable_type', 'App\Models\Listing');
                        break;
                    case 'article':
                        $query->where('commentable_type', 'App\Models\Article');
                        break;
                }
            }, 'commentable_type', 'Item')->radio([
                'game' => 'Game',
                'listing' => 'Listing',
                'article' => 'Article',
            ]);

            $filter->between('created_at', 'Created')->date();

        });

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
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
        $show = new Show(Comment::findOrFail($id));

        $show->id('Id');
        $show->commentable_id('Commentable id');
        $show->commentable_type('Commentable type');
        $show->user_id('User id');
        $show->content('Content');
        $show->likes('Likes');
        $show->status('Status');
        $show->has_children('Has children');
        $show->root_id('Root id');
        $show->last_reply_at('Last reply at');
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
        $form = new Form(new Comment);

        $form->number('commentable_id', 'Commentable id');
        $form->text('commentable_type', 'Commentable type');
        $form->number('user_id', 'User id');
        $form->textarea('content', 'Content');
        $form->number('likes', 'Likes');
        $form->number('status', 'Status');
        $form->switch('has_children', 'Has children');
        $form->number('root_id', 'Root id');
        $form->datetime('last_reply_at', 'Last reply at')->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
