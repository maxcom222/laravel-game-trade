<?php

namespace App\Backport\Controllers;

use App\Models\User_Rating;
use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class UserRatingController extends Controller
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
            ->header('Ratings')
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
        $grid = new Grid(new User_Rating);

        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableExport();



        /*$grid->column('')->display(function () {
            $rating_status = '';
            switch ($this->active) {
                case 0:
                    $rating_status = '<span class="badge badge-warning bp-font-lg"><i class="fas fa-clock"></i></span>';
                    break;
                case 1:
                    $rating_status = '<span class="badge badge-success bp-font-lg"><i class="fas fa-check-circle"></i></span>';
                    break;
            }
            return $rating_status;
        });

        $grid->active('Status')->select([
            0 => 'Pending',
            1 => 'Active',
        ]);*/

        $active_states = [
            'off' => ['value' => 0, 'text' => 'Pending', 'color' => 'warning'],
            'on'  => ['value' => 1, 'text' => 'Active', 'color' => 'success']
        ];
        $grid->active('Status')->switch($active_states);


        $grid->user_id_from('From User')->display(function () {
            if ($this->user_from->isOnline()) {
                $status = '<i class="fa fa-circle text-success"></i> Online';
            } else {
                $status = '<i class="fa fa-circle text-danger"></i> Offline';
            }

            return <<<EOT
<div class="d-flex justify-content-between align-items-center">
    <div class="image-text">
        <img src="{$this->user_from->avatar_square_tiny}" />
        <div class="content">
            <div class="top">
                <strong><a href="{$this->user_from->url}" target="_blank">{$this->user_from->name}</a></strong>
            </div>
            <div class="bottom">
                $status
            </div>
        </div>
    </div>
    <span class="font-weight-bolder bp-font-lg"><i class="fas fa-chevron-double-right"></i></span>
</div>
EOT;
        });

        $grid->user_id_to('To User')->display(function () {
            if ($this->user_to->isOnline()) {
                $status = '<i class="fa fa-circle text-success"></i> Online';
            } else {
                $status = '<i class="fa fa-circle text-danger"></i> Offline';
            }

            return <<<EOT
<div class="image-text">
    <img src="{$this->user_to->avatar_square_tiny}" />
    <div class="content">
        <div class="top">
            <strong><a href="{$this->user_to->url}" target="_blank">{$this->user_to->name}</a></strong>
        </div>
        <div class="bottom">
            $status
        </div>
    </div>
</div>
EOT;
        });

        $grid->rating('Rating')->display(function ($rating) {
            switch ($rating) {
                case 0:
                    return '<h3><span class="badge badge-danger"><i class="fa fa-thumbs-down"></i></span></h3>';
                case 1:
                    return '<h3><span class="badge badge-secondary"><i class="fa fa-minus"></i></span></h3>';
                case 2:
                    return '<h3><span class="badge badge-success"><i class="fa fa-thumbs-up"></i></span></h3>';
            }
        });


        $grid->notice('Notice');
        $grid->created_at('Created')->display(function () {
            return '<strong>' . $this->created_at->format(config('settings.date_format')) . '</strong><br />' . $this->created_at->format('H:i:m');
        });


        $grid->filter(function($filter){

            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                // Add a column filter
                $filter->equal('active', 'Status')->select(function () {
                    return array(0 => 'Pending', 1 => 'Active');
                });

                $filter->equal('user_id_from', 'From User ID');

                $filter->equal('user_id_to', 'To User ID');


            });

            $filter->column(1/2, function ($filter) {

                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'positive':
                            $query->where('rating', 2);
                            break;
                        case 'neutral':
                            $query->where('rating', 1);
                            break;
                        case 'negative':
                            $query->where('rating', 0);
                            break;
                    }
                }, 'Rating', 'rating')->radio([
                    'positive' => 'Positive',
                    'neutral' => 'Neutral',
                    'negative' => 'Negative',
                ]);

                $filter->between('created_at', 'Created')->date();

            });

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
        $show = new Show(User_Rating::findOrFail($id));

        $show->id('Id');
        $show->user_id_from('User id from');
        $show->user_id_to('User id to');
        $show->is_seller('Is seller');
        $show->offer_id('Offer id');
        $show->listing_id('Listing id');
        $show->rating('Rating');
        $show->notice('Notice');
        $show->active('Active');
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
        $form = new Form(new User_Rating);

        $form->number('user_id_from', 'User id from');
        $form->number('user_id_to', 'User id to');
        $form->switch('is_seller', 'Is seller');
        $form->number('offer_id', 'Offer id');
        $form->number('listing_id', 'Listing id');
        $form->number('rating', 'Rating');
        $form->textarea('notice', 'Notice');
        $form->switch('active', 'Active');

        return $form;
    }
}
