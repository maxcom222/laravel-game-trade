<?php

namespace App\Backport\Controllers;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class TransactionController extends Controller
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
            ->header('Transactions')
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
        $grid = new Grid(new Transaction);

        $grid->disableActions();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableCreateButton();

        $grid->type('Type')->display(function ($type) {
            switch ($type) {
                case 'sale':
                    return '<span class="badge badge-success">Sale</span>';
                    break;
                case 'withdrawal':
                    return '<span class="badge badge-warning">Withdrawal</span>';
                    break;
                case 'purchase':
                    return '<span class="badge badge-primary">Purchase</span>';
                    break;
                case 'refund':
                    return '<span class="badge badge-info">Refund</span>';
                    break;
                case 'fee':
                    return '<span class="badge badge-danger">Fee</span>';
                    break;
            }
        });

        $grid->total('Total')->display(function () {
            $type = '';
            switch ($this->type) {
                case 'sale':
                    $type = 'bp-font-success';
                    break;
                case 'withdrawal':
                    $type = 'bp-font-warning';
                    break;
                case 'purchase':
                    $type = 'bp-font-primary';
                    break;
                case 'refund':
                    $type = 'bp-font-info';
                    break;
                case 'fee':
                    $type = 'bp-font-danger';
                    break;
            }
            return <<<EOT
<span class="bp-font-lg bp-font-bolder {$type}">{$this->total} {$this->currency}</span>
EOT;
        })->sortable();

        $grid->user_id('User')->display(function () {
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

        $grid->column('Details')->display(function () {

            if (($this->type == 'sale' || $this->type == 'fee' || $this->type == 'refund' || $this->type == 'purchase')) {
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
            } elseif ($this->type == 'withdrawal') {
                if (isset($this->withdrawal)) {
                    $withdrawal_status = '';
                    switch ($this->withdrawal->status) {
                        case 0:
                            $withdrawal_status = '<span class="badge badge-danger">Declined</span>';
                            break;
                        case 1:
                            $withdrawal_status = '<span class="badge badge-warning">Pending</span>';
                            break;
                        case 2:
                            $withdrawal_status = '<span class="badge badge-success">Complete</span>';
                            break;
                    }

                    $details = '';
                    if ($this->withdrawal->payment_method == 'paypal') {
                        $details = 'Details: <strong>' . $this->withdrawal->payment_details . '</strong>';
                    } elseif($this->withdrawal->payment_method == 'bank') {
                        $bank = json_decode($this->withdrawal->payment_details);
                        $details = 'Account holder: <strong>' .  $bank->holder_name . '</strong><br />';
                        $details .= 'IBAN number: <strong>' .  $bank->iban . '</strong><br />';
                        $details .= 'Swift (BIC) code: <strong>' .  $bank->bic . '</strong><br />';
                        $details .= 'Bank Name: <strong>' .  $bank->bank_name . '</strong>';
                    }

                    $withdrawal_method = ucfirst($this->withdrawal->payment_method);

                    return <<<EOT
<span>Payment method: <strong>{$withdrawal_method}</strong></span><br />
<span>{$details}</span><br />
{$withdrawal_status}
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


            $filter->equal('user_id', 'User ID');


            $filter->where(function ($query) {
                switch ($this->input) {
                    case 'sale':
                        $query->where('type', 'sale');
                        break;
                    case 'withdrawal':
                        $query->where('type', 'withdrawal');
                        break;
                    case 'purchase':
                        $query->where('type', 'purchase');
                        break;
                    case 'refund':
                        $query->where('type', 'refund');
                        break;
                    case 'fee':
                        $query->where('type', 'fee');
                        break;
                }
            }, 'Type', 'type')->radio([
                'sale' => 'Sale',
                'withdrawal' => 'Withdrawal',
                'purchase' => 'Purchase',
                'refund' => 'Refund',
                'fee' => 'Fee',
            ]);

            $filter->between('created_at', 'Created')->date();


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
        $show = new Show(Transaction::findOrFail($id));

        $show->id('Id');
        $show->type('Type');
        $show->item_id('Item id');
        $show->item_type('Item type');
        $show->user_id('User id');
        $show->payment_id('Payment id');
        $show->payer_id('Payer id');
        $show->total('Total');
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
        $form = new Form(new Transaction);

        $form->text('type', 'Type');
        $form->number('item_id', 'Item id');
        $form->text('item_type', 'Item type');
        $form->number('user_id', 'User id');
        $form->number('payment_id', 'Payment id');
        $form->number('payer_id', 'Payer id');
        $form->decimal('total', 'Total');
        $form->text('currency', 'Currency');
        $form->number('status', 'Status')->default(1);

        return $form;
    }
}
