<?php

namespace App\Backport\Controllers;

use App\Models\Withdrawal;
use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class WithdrawalController extends Controller
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
            ->header('Withdrawals')
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
        $grid = new Grid(new Withdrawal);

        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableActions();

        $grid->column('')->display(function () {
            $withdrawal_status = '';
            switch ($this->status) {
                case 0:
                    $withdrawal_status = '<span class="badge badge-danger bp-font-lg"><i class="fas fa-times-circle"></i></span>';
                    break;
                case 1:
                    $withdrawal_status = '<span class="badge badge-warning bp-font-lg"><i class="fas fa-clock"></i></span>';
                    break;
                case 2:
                    $withdrawal_status = '<span class="badge badge-success bp-font-lg"><i class="fas fa-check-circle"></i></span>';
                    break;
            }
            return $withdrawal_status;
        });

        $grid->status()->select([
            0 => 'Declined',
            1 => 'Pending',
            2 => 'Complete',
        ]);

        $grid->total('Total')->display(function () {
            return <<<EOT
<span class="bp-font-lg bp-font-bolder">{$this->total} {$this->currency}</span><br />
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

        $grid->payment_details('Payment Details')->display(function() {
            $withdrawal_method = ucfirst($this->payment_method);
            $details = '';
            if ($this->payment_method == 'paypal') {
                $details = 'Details: <strong>' . $this->payment_details . '</strong>';
            } elseif($this->payment_method == 'bank') {
                $bank = json_decode($this->payment_details);
                $details = 'Account holder: <strong>' .  $bank->holder_name . '</strong><br />';
                $details .= 'IBAN number: <strong>' .  $bank->iban . '</strong><br />';
                $details .= 'Swift (BIC) code: <strong>' .  $bank->bic . '</strong><br />';
                $details .= 'Bank Name: <strong>' .  $bank->bank_name . '</strong><br />';
            }
            $withdrawal_details = $this->payment_details;
            return <<<EOT
<span>Payment method: <strong>{$withdrawal_method}</strong></span><br />
<span>{$details}</span>
EOT;
        });
        $grid->created_at('Date')->display(function () {
            return '<strong>' . $this->created_at->format(config('settings.date_format')) . '</strong><br />' . $this->created_at->format('H:i:m');
        })->sortable();

        $grid->filter(function($filter){

            $filter->disableIdFilter();


            $filter->equal('user_id', 'User ID');


            $filter->where(function ($query) {
                switch ($this->input) {
                    case 'declined':
                        $query->where('status', 0);
                        break;
                    case 'pending':
                        $query->where('status', 1);
                        break;
                    case 'complete':
                        $query->where('status', 2);
                        break;
                }
            }, 'Status', 'status')->radio([
                'declined' => 'Declined',
                'pending' => 'Pending',
                'complete' => 'Complete',
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
        $show = new Show(Withdrawal::findOrFail($id));

        $show->id('Id');
        $show->user_id('User id');
        $show->total('Total');
        $show->currency('Currency');
        $show->payment_method('Payment method');
        $show->payment_details('Payment details');
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
        $form = new Form(new Withdrawal);

        $form->number('user_id', 'User id');
        $form->decimal('total', 'Total');
        $form->text('currency', 'Currency');
        $form->text('payment_method', 'Payment method');
        $form->textarea('payment_details', 'Payment details');
        $form->number('status', 'Status')->default(1);

        return $form;
    }
}
