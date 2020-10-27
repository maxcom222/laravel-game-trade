<?php

namespace App\Backport\Controllers;

use App\Models\Offer;
use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class OfferController extends Controller
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
            ->header('Offers')
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
        $grid = new Grid(new Offer);

        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableExport();

        $grid->status('Status')->display(function ($status) {
            switch ($status) {
                case 0:
                    return '<span class="badge badge-warning">Wait</span>';
                case 1:
                    return '<span class="badge badge-primary">Accepted</span>';
                case 2:
                    return '<span class="badge badge-secondary">Complete</span>';
            }
        });


        $grid->user_id('From User')->display(function () {
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

        $grid->column('To User')->display(function () {
            if ($this->listing->user->isOnline()) {
                $status = '<i class="fa fa-circle text-success"></i> Online';
            } else {
                $status = '<i class="fa fa-circle text-danger"></i> Offline';
            }

            return <<<EOT
<div class="image-text">
    <img src="{$this->listing->user->avatar_square_tiny}" />
    <div class="content">
        <div class="top">
            <strong><a href="{$this->listing->user->url}" target="_blank">{$this->listing->user->name}</a></strong>
        </div>
        <div class="bottom">
            $status
        </div>
    </div>
</div>
EOT;
        });

        $grid->column('Game')->display(function () {
            return <<<EOT
<div class="image-text">
    <img src="{$this->listing->game->image_square_tiny}" />
    <div class="content">
        <div class="top">
            <strong><a href="{$this->listing->game->url_slug}" target="_blank">{$this->listing->game->name}</a></strong>
        </div>
        <div class="bottom">
            <span class="badge badge-dark" style="background-color: {$this->listing->game->platform->color}; margin-right: 10px;">{$this->listing->game->platform->name}</span><i class="fa fa-calendar"></i> {$this->listing->game->release_date->format('Y')}
        </div>
    </div>
</div>
EOT;
        });


        $grid->column('Offer')->display(function () {
            if ($this->game) {
                return <<<EOT
<div class="image-text">
    <img src="{$this->listing->game->image_square_tiny}" />
    <div class="content">
        <div class="top">
            <strong><a href="{$this->listing->game->url_slug}" target="_blank">{$this->listing->game->name}</a></strong>
        </div>
        <div class="bottom">
            <span class="badge badge-dark" style="background-color: {$this->listing->game->platform->color}; margin-right: 10px;">{$this->listing->game->platform->name}</span><i class="fa fa-calendar"></i> {$this->listing->game->release_date->format('Y')}
        </div>
    </div>
</div>
EOT;
            } else {
                return <<<EOT
<h5><span class="badge badge-success">{$this->getPriceOfferFormattedAttribute()}</span></h5>
EOT;
            }
        });


        $grid->created_at('Created')->display(function () {
            return '<strong>' . $this->created_at->format(config('settings.date_format')) . '</strong><br />' . $this->created_at->format('H:i:m');
        });

        $grid->filter(function($filter){

            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                // Add a column filter
                $filter->equal('status', 'Status')->select(function () {
                    return array(0 => 'Wait', 1 => 'Accepted', 2 => 'Complete');
                });

                $filter->equal('user_id', 'User ID');

            });

            $filter->column(1/2, function ($filter) {

                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'sell':
                            $query->where('price_offer', '!=', NULL);
                            break;
                        case 'trade':
                            $query->where('trade_game', '!=', NULL);
                            break;
                    }
                }, 'Type', 'type')->radio([
                    'sell' => 'Sell',
                    'trade' => 'Trade',
                ]);

                $filter->between('created_at', 'Created')->date();

            });

        });

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
            $actions->prepend('<a class="badge badge-primary mr-1" target="_blank" href="' . url('offer/' . $actions->getKey()) . '"><i class="fa fa-eye"></i></a>');
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
        $show = new Show(Offer::findOrFail($id));

        $show->id('Id');
        $show->status('Status');
        $show->user_id('User id');
        $show->listing_id('Listing id');
        $show->thread_id('Thread id');
        $show->note('Note');
        $show->price_offer('Price offer');
        $show->trade_game('Trade game');
        $show->additional_type('Additional type');
        $show->additional_charge('Additional charge');
        $show->delivery('Delivery');
        $show->trade_from_list('Trade from list');
        $show->declined('Declined');
        $show->decline_note('Decline note');
        $show->rating_id_offer('Rating id offer');
        $show->rating_id_listing('Rating id listing');
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
        $form = new Form(new Offer);

        $form->number('status', 'Status');
        $form->number('user_id', 'User id');
        $form->number('listing_id', 'Listing id');
        $form->number('thread_id', 'Thread id');
        $form->textarea('note', 'Note');
        $form->number('price_offer', 'Price offer');
        $form->number('trade_game', 'Trade game');
        $form->text('additional_type', 'Additional type');
        $form->number('additional_charge', 'Additional charge');
        $form->switch('delivery', 'Delivery')->default(1);
        $form->switch('trade_from_list', 'Trade from list');
        $form->switch('declined', 'Declined');
        $form->textarea('decline_note', 'Decline note');
        $form->number('rating_id_offer', 'Rating id offer');
        $form->number('rating_id_listing', 'Rating id listing');
        $form->datetime('closed_at', 'Closed at')->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
