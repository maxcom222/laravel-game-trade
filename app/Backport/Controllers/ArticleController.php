<?php

namespace App\Backport\Controllers;

use App\Models\Article;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Wiledia\Backport\Controllers\HasResourceActions;
use Wiledia\Backport\Form;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Show;

class ArticleController extends Controller
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
            ->header('Articles')
            ->description('description')
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

        if ($request->has('image')) {
            $image = $request->file('image');

            $filename = time().'-'.$id.'.jpg';
            $disk = "local";
            $destination_path = "public/articles";

            Storage::disk($disk)->put($destination_path.'/'.$filename,  File::get($image));

            $article = \App\Models\Article::findOrFail($id);

            // Delete old image
            if (!is_null($article->image)) {
                \Storage::disk($disk)->delete('/public/articles/' . $article->image);
            }

            // Save to database
            $article->image = $filename;
            $article->save();

            unset($data['image']);

        }

        return $this->form($id)->update($id, $data);
    }

    public function store(Request $request)
    {

        $data = $request->all();

        if ($request->has('image')) {
            $cover = $request->file('image');

            $filename = time().'.jpg';
            $disk = "local";
            $destination_path = "public/articles";

            Storage::disk($disk)->put($destination_path.'/'.$filename,  File::get($cover));

            $data['image'] = $filename;
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
        $grid = new Grid(new Article);

        $grid->image('Image')->display(function ($image) {
            if (!is_null($this->image)) {
                return $image;
            } else {
                return 'no_cover.jpg';
            }
        })->image(asset('images/square_tiny/'), 50, 50);


        $grid->title('Title')->editable()->sortable();

        $grid->status('Status')->display(function ($status) {
            if ($status == 'PUBLISHED') {
                return "<span class='badge badge-success'>Published</span>";
            } else {
                return "<span class='badge badge-warning'>Draft</span></strong>";
            }
        });


        $grid->featured('Featured')->display(function ($featured) {
            if ($featured == '1') {
                return '<i class="fas fa-check-circle bp-font-xl"></i>';
            } else {
                return '<i class="fas fa-times-circle bp-font-xl"></i>';
            }
        });

        $grid->column('Category')->display(function () {
            return "<span class='badge badge-secondary'>{$this->category->name}</span>";
        });

        $grid->date('Date')->display(function () {
            return '<strong>' . $this->date->format(config('settings.date_format')) . '</strong><br />' . $this->date->format('H:i:m');
        })->sortable();

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('title', 'Title');
            $filter->equal('category_id', 'Category')->select(function () {
                $options = array();
                $categories = \App\Models\Category::all();
                foreach ($categories as $category) {
                    $options[$category['id']] = $category['name'];
                }
                return $options;
            });
            $filter->between('date', 'Date')->date();
            $filter->where(function ($query) {
                switch ($this->input) {
                    case 'yes':
                        $query->where('featured', 1);
                        break;
                    case 'no':
                        $query->where('featured', 0);
                        break;
                }
            }, 'Featured', 'Featured')->radio([
                'yes' => 'Yes',
                'no' => 'No',
            ]);
        });


        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->prepend('<a class="badge badge-primary mr-1" target="_blank" href="' . url('blog/show-article-' . $actions->getKey()) . '"><i class="fa fa-eye"></i></a>');
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
        $show = new Show(Article::findOrFail($id));

        $show->id('Id');
        $show->category_id('Category id');
        $show->title('Title');
        $show->slug('Slug');
        $show->content('Content');
        $show->image('Image');
        $show->status('Status');
        $show->date('Date');
        $show->featured('Featured');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->deleted_at('Deleted at');

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
            $article = Article::find($id);
        }

        $form = new Form(new Article);

        $form->text('title', 'Title')->rules('required')->required();
        $form->text('slug', 'Slug (URL)')->help('Will be automatically generated from your title, if left empty.');
        $form->date('date', 'Date')->rules('required')->required();
        $form->editor('content', 'Content');

        if (isset($article)) {
            $form->avatar('image', 'Image')->placeholder('images/square/' . $article->getOriginal('image'));
        } else {
            $form->avatar('image', 'Image');
        }

        $form->select('category_id', 'Category')->options(function () {
            $options = array();
            $categories = \App\Models\Category::all();
            foreach ($categories as $key => $category) {
                $options[$category['id']] = $category['name'];
            }
            return $options;
        })->rules('required')->required();

        $form->multipleSelect('tags')->options(\App\Models\Tag::all()->pluck('name', 'id'));

        $form->select('status', 'Status')->default('PUBLISHED')->options(['PUBLISHED' => 'Published', 'DRAFT' => 'Draft']);

        $form->switch('featured', 'Featured');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
        });

        return $form;
    }
}
