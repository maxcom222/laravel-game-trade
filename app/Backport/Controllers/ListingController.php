<?php

namespace App\Backport\Controllers;

use App\Models\Listing;
use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class ListingController extends Controller
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
            ->header('Listings')
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
        $grid = new Grid(new Listing);

        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableExport();

        $grid->status('Status')->display(function ($status) {
            switch ($status) {
                case 0:
                    return '<span class="badge badge-success">Active</span>';
                    break;
                case 1:
                    return '<span class="badge badge-primary">Sold</span>';
                    break;
                case 2:
                    return '<span class="badge badge-secondary">Complete</span>';
                    break;
            }
        });

        $grid->user_id('User')->display(function () {
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

        $grid->game_id('Game')->display(function () {
            return <<<EOT
<div class="image-text">
    <img src="{$this->game->image_square_tiny}" />
    <div class="content">
        <div class="top">
            <strong><a href="{$this->game->url_slug}" target="_blank">{$this->game->name}</a></strong>
        </div>
        <div class="bottom">
            <span class="badge badge-dark" style="background-color: {$this->game->platform->color}; margin-right: 10px;">{$this->game->platform->name}</span><i class="fa fa-calendar"></i> {$this->game->release_date->format('Y')}
        </div>
    </div>
</div>
EOT;
        });


        $grid->price('Price')->display(function () {
            if ($this->sell == '1') {
                return "<span class='badge badge-success'>{$this->price_formatted}</span>";
            } else {
                return "<span class='badge badge-danger'><i class='fa fa-shopping-basket'></i></span>";
            }
        });

        $grid->trade('Trade')->display(function ($trade) {
            if ($trade == '1') {
                return "<span class='badge badge-success'><i class='fa fa-exchange'></i></span>";
            } else {
                return "<span class='badge badge-danger'><i class='fa fa-exchange'></i></span>";
            }
        });


        $grid->created_at('Created')->display(function () {
            return '<strong>' . $this->created_at->format(config('settings.date_format')) . '</strong><br />' . $this->created_at->format('H:i:m');
        })->sortable();

        $grid->offers('Offers')->display(function ($offers) {
            $count = count($offers);
            if ($count == 0) {
                return "<span class='badge badge-secondary'>{$count}</span>";
            } else {
                return "<span class='badge badge-primary'>{$count}</span></strong>";
            }
        });

        $grid->clicks('Clicks')->sortable();

        $grid->filter(function($filter){

            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                // Add a column filter
                $filter->equal('status', 'Status')->select(function () {
                    return array(1 => 'Sold', 2 => 'Complete');
                });

                $filter->equal('user_id', 'User ID');
                $filter->equal('game_id', 'Game ID');

            });

            $filter->column(1/2, function ($filter) {

                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'sell':
                            $query->where('sell', 1);
                            break;
                        case 'trade':
                            $query->where('trade', 1);
                            break;
                        case 'both':
                            $query->where('sell', 1)->where('trade', 1);
                            break;
                    }
                }, 'Type', 'type')->radio([
                    'sell' => 'Sell',
                    'trade' => 'Trade',
                    'both' => 'Both',
                ]);

                $filter->between('created_at', 'Created')->date();

                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'yes':
                            $query->has('offers');
                            break;
                        case 'no':
                            $query->doesntHave('offers');
                            break;
                    }
                }, 'Offers', 'offers')->radio([
                    'yes' => 'Yes',
                    'no' => 'No',
                ]);




            });

        });


        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->prepend('<a class="badge badge-warning mr-1" target="_blank" href="' . url('listings/show-listing-' . $actions->getKey()) . '/edit"><i class="fa fa-edit"></i></a>');
            $actions->prepend('<a class="badge badge-primary mr-1" target="_blank" href="' . url('listings/show-listing-' . $actions->getKey()) . '"><i class="fa fa-eye"></i></a>');
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
        $show = new Show(Listing::findOrFail($id));

        $show->id('Id');
        $show->user_id('User id');
        $show->game_id('Game id');
        $show->name('Name');
        $show->picture('Picture');
        $show->description('Description');
        $show->price('Price');
        $show->condition('Condition');
        $show->digital('Digital');
        $show->limited_edition('Limited edition');
        $show->delivery('Delivery');
        $show->delivery_price('Delivery price');
        $show->pickup('Pickup');
        $show->sell('Sell');
        $show->sell_negotiate('Sell negotiate');
        $show->trade('Trade');
        $show->trade_negotiate('Trade negotiate');
        $show->trade_list('Trade list');
        $show->payment('Payment');
        $show->status('Status');
        $show->clicks('Clicks');
        $show->last_offer_at('Last offer at');
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
        $form = new Form(new Listing);

        $form->number('user_id', 'User id');
        $form->number('game_id', 'Game id');
        $form->text('name', 'Name');
        $form->image('picture', 'Picture');
        $form->textarea('description', 'Description');
        $form->number('price', 'Price');
        $form->number('condition', 'Condition');
        $form->number('digital', 'Digital');
        $form->text('limited_edition', 'Limited edition');
        $form->switch('delivery', 'Delivery');
        $form->number('delivery_price', 'Delivery price');
        $form->switch('pickup', 'Pickup');
        $form->switch('sell', 'Sell');
        $form->switch('sell_negotiate', 'Sell negotiate');
        $form->switch('trade', 'Trade');
        $form->switch('trade_negotiate', 'Trade negotiate');
        $form->textarea('trade_list', 'Trade list');
        $form->switch('payment', 'Payment');
        $form->number('status', 'Status');
        $form->number('clicks', 'Clicks');
        $form->datetime('last_offer_at', 'Last offer at')->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
