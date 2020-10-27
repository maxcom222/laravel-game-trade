<?php

namespace App\Backport\Controllers;

use App\Models\Platform;
use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class PlatformController extends Controller
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
            ->header('Platforms')
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
        $grid = new Grid(new Platform);

        $grid->name('Name')->editable()->sortable();
        $grid->acronym('Acronym');
        $grid->color('Color')->display(function ($color) {
            return "<span class='badge badge-dark' style='background-color:{$color} !important;'>{$color}</span>";
        });

        $grid->games('Games')->display(function ($games) {
            $count = count($games);
            if ($count == 0) {
                return "<span class='badge badge-secondary'>{$count}</span>";
            } else {
                return "<span class='badge badge-primary'>{$count}</span>";
            }
        });

        $grid->digitals('Digital Distributors')->display(function ($digitals) {

            $digitals = array_map(function ($digital) {
                return "<span class='badge badge-dark'>{$digital['name']}</span>";
            }, $digitals);

            return join('&nbsp;', $digitals);
        });

        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();

            // Add a column filter
            $filter->like('name', 'Name');

            $filter->where(function ($query) {
                switch ($this->input) {
                    case 'yes':
                        $query->has('games');
                        break;
                    case 'no':
                        $query->doesntHave('games');
                        break;
                }
            }, 'Games', 'games')->radio([
                'yes' => 'Yes',
                'no' => 'No',
            ]);

        });

        $grid->actions(function ($actions) {
            $actions->disableView();
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
        $show = new Show(Platform::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->description('Description');
        $show->color('Color');
        $show->acronym('Acronym');
        $show->cover_position('Cover position');
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
        $form = new Form(new Platform);

        $form->text('name', 'Name')->rules('required')->required();
        $form->text('acronym', 'Acronym')->rules(function ($form) {
            if (!$id = $form->model()->id) {
                return 'required|unique:platforms,acronym';
            } else {
                return 'required|unique:platforms,acronym,' . $id;
            }
        })->required();
        $form->editor('description', 'Description');
        $form->color('color', 'Color')->rules('required')->required();

        $form->select('cover_position', 'Cover position')->default('left')->options(function () {
            return array("left" => "Left", "center" => "Center", "right" => "Right");
        });;

        $form->multipleSelect('digitals')->options(\App\Models\Digital::all()->pluck('name', 'id'));


        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
        });

        return $form;
    }
}
