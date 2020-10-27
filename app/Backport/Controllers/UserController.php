<?php

namespace App\Backport\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;
use ClickNow\Money\Money;
use Config;

class UserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Users')
            ->description(trans('admin.list'))
            ->body($this->grid()->render());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.administrator'))
            ->description(trans('admin.detail'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.administrator'))
            ->description(trans('admin.edit'))
            ->body($this->form($id)->edit($id));
    }

    public function update($id, Request $request)
    {

        $data = $request->all();

        if ($request->has('avatar')) {
            $avatar = $request->file('avatar');

            $filename = time().'-'.$id.'.jpg';
            $disk = "local";
            $destination_path = "public/users";

            Storage::disk($disk)->put($destination_path.'/'.$filename,  File::get($avatar));

            $user = \App\Models\User::findOrFail($id);

            // Delete old image
            if (!is_null($user->avatar)) {
                \Storage::disk($disk)->delete('/public/users/' . $user->avatar);
            }

            // Save to database
            $user->avatar = $filename;
            $user->save();

            unset($data['avatar']);
        }

        return $this->form($id)->update($id, $data);
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.administrator'))
            ->description(trans('admin.create'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $userModel = config('backport.database.users_model');

        $grid = new Grid(new $userModel());

        $grid->disableRowSelector();
        $grid->disableCreateButton();

        $grid->id('ID')->sortable();

        $grid->name(trans('admin.name'))->display(function () {
            if ($this->isOnline()) {
                $status = '<i class="fa fa-circle text-success"></i> Online';
            } else {
                $status = '<i class="fa fa-circle text-danger"></i> Offline';
            }

            return <<<EOT
<div class="image-text">
    <img src="{$this->avatar_square_tiny}" />
    <div class="content">
        <div class="top">
            <strong><a href="{$this->url}" target="_blank">{$this->name}</a></strong>
        </div>
        <div class="bottom">
            {$status}
        </div>
    </div>
</div>
EOT;
        })->sortable();

        $grid->email()->editable();

        $confirmed_states = [
            'on'  => ['value' => 1, 'text' => 'Yes', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => 'No', 'color' => 'default'],
        ];
        $grid->confirmed('Confirmed')->switch($confirmed_states);

        $grid->roles(trans('admin.roles'))->pluck('name')->badge('secondary');

        $active_states = [
            'on'  => ['value' => 1, 'text' => 'Active', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => 'Banned', 'color' => 'danger'],
        ];
        $grid->status('Status')->switch($active_states);
        $grid->balance()->display(function ($balance) {
            return money($balance, Config::get('settings.currency'))->format(true, Config::get('settings.decimal_place'));;
        })->sortable();

        $grid->created_at('Created')->display(function () {
            return '<strong>' . $this->created_at->format(config('settings.date_format')) . '</strong><br />' . $this->created_at->format('H:i:m');
        })->sortable();

        $grid->filter(function($filter){

            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {

                $filter->equal('name', 'Name');

                $filter->equal('id', 'User ID');

                $filter->equal('email', 'Email');

            });

            $filter->column(1/2, function ($filter) {

                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'yes':
                            $query->where('confirmed', 1);
                            break;
                        case 'no':
                            $query->where('confirmed', 0);
                            break;
                    }
                }, 'Confirmed', 'confirmed')->radio([
                    'yes' => 'Yes',
                    'no' => 'No',
                ]);

                $filter->between('created_at', 'Created')->date();

                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'active':
                            $query->where('status', 1);
                            break;
                        case 'banned':
                            $query->where('status', 0);
                            break;
                    }
                }, 'Status', 'status')->radio([
                    'active' => 'Active',
                    'banned' => 'Banned',
                ]);




            });

        });

        $grid->actions(function ($actions) {
            $actions->disableView();
            if ($actions->getKey() == \Auth::user()->id) {
                $actions->disableDelete();
            }
            $actions->disableDelete();
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $userModel = config('backport.database.users_model');

        $show = new Show($userModel::findOrFail($id));

        $show->id('ID');
        $show->username(trans('admin.username'));
        $show->name(trans('admin.name'));
        $show->roles(trans('admin.roles'))->as(function ($roles) {
            return $roles->pluck('name');
        })->label();
        $show->permissions(trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form($id = null)
    {
        $userModel = config('backport.database.users_model');
        $permissionModel = config('backport.database.permissions_model');
        $roleModel = config('backport.database.roles_model');

        $form = new Form(new $userModel());

        $form->display('id', 'ID');

        $form->text('name', trans('admin.name'))->required()->rules('required|unique:users,name,' . $id);

        $form->email('email', 'Email')->required()->rules('required|unique:users,email,' . $id);
        $form->avatar('avatar', trans('admin.avatar'));
        $form->password('password', trans('admin.password'))->rules('sometimes|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('sometimes');

        $form->ignore(['password_confirmation']);

        $form->switch('confirmed', 'Email confirmed');
        $form->switch('status', 'Active');

        $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'));
        $form->multipleSelect('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id'));

        $form->display('created_at', trans('admin.created_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }

            if ($form->password == '' || $form->password == '0') {
                $form->password = $form->model()->password;
            }
        });

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
            $footer->disableCreatingCheck();
        });

        return $form;
    }
}
