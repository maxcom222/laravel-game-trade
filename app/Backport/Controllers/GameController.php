<?php

namespace App\Backport\Controllers;

use App\Models\Game;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class GameController extends Controller
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
            ->header('Games')
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
            ->body($this->form($id)->edit($id));
    }

    public function update($id, Request $request)
    {

        $data = $request->all();

        if ($request->has('cover')) {
            $cover = $request->file('cover');

            $filename = time().'-'.$id.'.jpg';
            $disk = "local";
            $destination_path = "public/games";

            Storage::disk($disk)->put($destination_path.'/'.$filename,  File::get($cover));

            $game = \App\Models\Game::findOrFail($id);

            // Delete old image
            if (!is_null($game->cover)) {
                \Storage::disk($disk)->delete('/public/games/' . $game->cover);
            }

            // Save to database
            $game->cover = $filename;
            $game->save();

            unset($data['cover']);

        }

        return $this->form($id)->update($id, $data);
    }

    public function store(Request $request)
    {

        $data = $request->all();

        if ($request->has('cover')) {
            $cover = $request->file('cover');

            $filename = time().'.jpg';
            $disk = "local";
            $destination_path = "public/games";

            Storage::disk($disk)->put($destination_path.'/'.$filename,  File::get($cover));

            $data['cover'] = $filename;
        }

        return $this->form()->store($data);
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
        $grid = new Grid(new Game);

        $grid->id('ID')->sortable();

        $grid->cover('Cover')->display(function ($cover) {
            if (!is_null($this->cover)) {
                return $cover;
            } else {
                return 'no_cover.jpg';
            }
        })->image(asset('images/square_tiny/'), 50, 50);

        $grid->name('Name')->editable()->sortable();

        $grid->column('Platform')->display(function () {
            return "<span class='badge badge-dark' style='background-color:{$this->platform->color} !important;'>{$this->platform->name}</span>";
        });

        $grid->publisher('Publisher');

        $grid->release_date('Release Date')->display(function () {
            return $this->release_date->format(config('settings.date_format'));
        })->sortable();

        $grid->listings('Active Listings')->display(function ($listings) {
            $count = count($listings);

            if ($count == 0) {
                return "<span class='badge badge-secondary'>{$count}</span>";
            } else {
                if ($this->cheapestListing !== 0) {
                    return "<span class='badge badge-primary'>{$count}</span> from <strong>{$this->cheapestListing}</strong>";
                } else {
                    return "<span class='badge badge-primary'>{$count}</span>";
                }
            }
        });



        $grid->filter(function($filter){


            $filter->column(1/2, function ($filter) {
                // Add a column filter
                $filter->like('name', 'Name');

                $filter->equal('platform_id', 'Platform')->select(function () {
                    $options = array();
                    $platforms = \App\Models\Platform::all();
                    foreach ($platforms as $platform) {
                        $options[$platform['id']] = $platform['name'];
                    }
                    return $options;
                });
            });

            $filter->column(1/2, function ($filter) {
                // Add a column filter
                $filter->like('publisher', 'Publisher');

                $filter->between('release_date', 'Release')->date();

                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'active':
                            $query->has('listings');
                            break;
                        case 'none':
                            $query->doesntHave('listings');
                            break;
                    }
                }, 'Listings', 'listings')->radio([
                    'active' => 'Active',
                    'none' => 'None',
                ]);

            });


        });


        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->prepend('<a class="badge badge-primary mr-1" target="_blank" href="' . url('games/admin-' . $actions->getKey()) . '"><i class="fa fa-eye"></i></a>');
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
        $show = new Show(Game::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->cover('Cover');
        $show->cover_generator('Cover generator');
        $show->description('Description');
        $show->release_date('Release date');
        $show->publisher('Publisher');
        $show->developer('Developer');
        $show->pegi('Pegi');
        $show->tags('Tags');
        $show->source_name('Source name');
        $show->metacritic_id('Metacritic id');
        $show->giantbomb_id('Giantbomb id');
        $show->platform_id('Platform id');
        $show->genre_id('Genre id');
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
    protected function form($id = null)
    {
        // Get database value here
        if ($id) {
            $game = Game::find($id);
        }

        $form = new Form(new Game);

        $form->text('name', 'Name')->rules('required')->required();
        $form->editor('description', 'Description');
        $form->switch('cover_generator', 'Cover generator')->help('Add platform bar with logo on top of game cover.');

        if (isset($game)) {
            $form->avatar('cover', 'Cover')->placeholder('images/square/' . $game->cover);
        } else {
            $form->avatar('cover', 'Cover');
        }

        $form->date('release_date', 'Release date')->format('yyyy-mm-dd')->rules('required')->required();
        $form->text('publisher', 'Publisher');
        $form->text('developer', 'Developer');
        $form->select('pegi', 'PEGI')->options([0 => 'None', 3 => '3+', 7 => '7+', 12 => '12+', 16 => '16+', 18 => '18+']);

        $form->select('platform_id', 'Platform')->options(function () {
            $options = array();
            $platforms = \App\Models\Platform::all();
            foreach ($platforms as $key => $platform) {
                $options[$platform['id']] = $platform['name'];
            }
            return $options;
        })->rules('required')->required();


        $form->select('genre_id', 'Genre')->options(function () {
            $options = array();
            $genres = \App\Models\Genre::all();
            foreach ($genres as $key => $genre) {
                $options[$genre['id']] = $genre['name'];
            }
            return $options;
        })->rules('required')->required();



        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
        });

        return $form;
    }
}
