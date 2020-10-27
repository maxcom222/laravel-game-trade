<?php

namespace App\Backport\Controllers;

use App\Models\Payment;
use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class PaymentController extends Controller
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
            ->header('Payments')
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
        $grid = new Grid(new Payment);

        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableActions();

        $grid->status('Status')->display(function ($status) {
            switch ($status) {
                case 0:
                    return '<span class="badge badge-warning">Refunded</span>';
                    break;
                case 1:
                    return '<span class="badge badge-success">Complete</span>';
                    break;
            }
        });

        $grid->total('Total')->display(function () {
            $status = '';
            switch ($this->status) {
                case 0:
                    $status = 'bp-font-danger';
                    break;
                case 1:
                    $status = 'bp-font-success';
                    break;
            }
            return <<<EOT
<span class="bp-font-lg bp-font-bolder {$status}">{$this->total} {$this->currency}</span><br />
<span>Transaction fee: <strong class="bp-font-danger">{$this->transaction_fee} {$this->currency}</strong></span>
EOT;
        })->sortable();

        $grid->transaction_id('Payment Details')->display(function () {
            $method = '';
            switch ($this->payment_method) {
                case 'paypal':
                    $method = '<i class="fab fa-paypal"></i> PayPal';
                    break;
                case 'stripe':
                    $method = '<i class="fab fa-cc-stripe"></i> Stripe';
                    break;
                case 'balance':
                    $method = '<i class="fas fa-money-bill"></i> Balance';
                    break;
            }
            return <<<EOT
<span>Transaction ID: <strong>{$this->transaction_id}</strong></span><br />
<span>{$method}</span>
EOT;
        });

        $grid->user_id('Payment User')->display(function () {
            if ($this->user->isOnline()) {
                $status = '<i class="fa fa-circle text-success"></i>';
            } else {
                $status = '<i class="fa fa-circle text-danger"></i>';
            }

            return <<<EOT
<div class="image-text" style=" ">
    <img src="{$this->user->avatar_square_tiny}" />
    <div class="content">
        <div class="top">
            <strong><a href="{$this->user->url}" target="_blank">{$this->user->name}</a></strong>
        </div>
        <div class="bottom">
            {$status} User ID: <strong>{$this->user->id}</strong>
        </div>
    </div>
</div>
EOT;
        })->sortable();

        $grid->column('Item')->display(function () {

            if ($this->item_type == 'App\Models\Offer' ) {
                if (isset($this->offer)) {
                    return <<<EOT
<div class="image-text">
    <img src="{$this->offer->listing->game->image_square_tiny}" />
    <div class="content">
        <div class="top">
            <strong><i class="fa fa-briefcase"></i> <a href="{$this->offer->url}" target="_blank">{$this->offer->listing->game->name}</a></strong>
        </div>
        <div class="bottom">
            <span class="badge badge-dark" style="background-color: {$this->offer->listing->game->platform->color}; margin-right: 10px;">{$this->offer->listing->game->platform->name}</span><i class="fa fa-calendar"></i> {$this->offer->listing->game->release_date->format('Y')}
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

        $grid->created_at('Date')->display(function () {
            return '<strong>' . $this->created_at->format(config('settings.date_format')) . '</strong><br />' . $this->created_at->format('H:i:m');
        })->sortable();

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('transaction_id', 'Transaction ID');
                $filter->like('user_id', 'User ID');

                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'complete':
                            $query->where('status', 1);
                            break;
                        case 'refunded':
                            $query->where('status', 0);
                            break;
                    }
                }, 'Status', 'status')->radio([
                    'complete' => 'Complete',
                    'refunded' => 'Refunded',
                ]);
            });

            $filter->column(1/2, function ($filter) {
                $filter->between('created_at', 'Date')->date();

                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'paypal':
                            $query->where('payment_method', 'paypal');
                            break;
                        case 'stripe':
                            $query->where('payment_method', 'stripe');
                            break;
                        case 'balance':
                            $query->where('payment_method', 'balance');
                            break;
                    }
                }, 'methode', 'Methode')->radio([
                    'paypal' => 'PayPal',
                    'stripe' => 'Stripe',
                    'balance' => 'Balance',
                ]);
            });

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
        $show = new Show(Payment::findOrFail($id));

        $show->id('Id');
        $show->item_id('Item id');
        $show->item_type('Item type');
        $show->user_id('User id');
        $show->transaction_id('Transaction id');
        $show->payment_method('Payment method');
        $show->payer_info('Payer info');
        $show->total('Total');
        $show->transaction_fee('Transaction fee');
        $show->currency('Currency');
        $show->status('Status');
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
        $form = new Form(new Payment);

        $form->number('item_id', 'Item id');
        $form->text('item_type', 'Item type');
        $form->number('user_id', 'User id');
        $form->text('transaction_id', 'Transaction id');
        $form->text('payment_method', 'Payment method');
        $form->textarea('payer_info', 'Payer info');
        $form->decimal('total', 'Total');
        $form->decimal('transaction_fee', 'Transaction fee');
        $form->text('currency', 'Currency');
        $form->number('status', 'Status')->default(1);

        return $form;
    }
}
