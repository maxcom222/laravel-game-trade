<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use App\Models\MenuItem;
use App\Models\Language;
use Cache;

class FooterComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $menu;
    protected $languages;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct(MenuItem $menu, Language $languages)
    {
        // Dependencies automatically resolved by service container...
        $this->menu = $menu;
        $this->languages = $languages;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Get all menu items from the cache
        $menu = Cache::rememberForever('menu_items', function () {
            return $this->menu->with('page','children','parent')->orderBy('lft')->get();
        });
        // Get all language items from the cache
        $languages = Cache::rememberForever('languages', function () {
            return $this->languages->where('active',1)->get();
        });
        $view->with(['menu' => $menu, 'languages' => config('settings.locale_selector') ? $languages : null]);
    }
}
