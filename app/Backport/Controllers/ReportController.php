<?php

namespace App\Backport\Controllers;

use App\Models\Report;
use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class ReportController extends Controller
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
            ->header('Reports')
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
        $grid = new Grid(new Report);

        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableExport();

        $grid->status('Status')->display(function ($status) {
            switch ($status) {
                case 0:
                    return '<span class="badge badge-danger">Open</span>';
                case 1:
                    return '<span class="badge badge-success">Closed</span>';
            }
        });


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


        $grid->reason('Reason');

        $grid->created_at('Opened')->display(function () {
            return '<strong>' . $this->created_at->format(config('settings.date_format')) . '</strong><br />' . $this->created_at->format('H:i:m');
        });

        $grid->filter(function($filter){

            $filter->disableIdFilter();


            // Add a column filter
            $filter->equal('status', 'Status')->select(function () {
                return array(0 => 'Open', 1 => 'Closed');
            });

            $filter->equal('user_id', 'User ID');


            $filter->between('created_at', 'Opened')->date();

        });

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
            $actions->prepend('<a class="badge badge-primary mr-1" target="_blank" href="' . url('offer/' . $actions->row['offer_id']) . '"><i class="fa fa-eye"></i></a>');
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
        $show = new Show(Report::findOrFail($id));

        $show->id('Id');
        $show->status('Status');
        $show->offer_id('Offer id');
        $show->listing_id('Listing id');
        $show->user_id('User id');
        $show->user_is('User is');
        $show->reason('Reason');
        $show->user_staff('User staff');
        $show->closed_at('Closed at');
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
        $form = new Form(new Report);

        $form->number('status', 'Status');
        $form->number('offer_id', 'Offer id');
        $form->number('listing_id', 'Listing id');
        $form->number('user_id', 'User id');
        $form->text('user_is', 'User is');
        $form->textarea('reason', 'Reason');
        $form->number('user_staff', 'User staff');
        $form->datetime('closed_at', 'Closed at')->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
