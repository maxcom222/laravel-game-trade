<?php

namespace App\Providers;

use App\Models\Listing;
use App\Observers\ListingObserver;
use App\Models\Game;
use App\Observers\GameObserver;
use App\Models\User;
use App\Observers\UserObserver;
use App\Models\MenuItem;
use App\Observers\MenuItemObserver;
use App\Models\Language;
use App\Observers\LanguageObserver;
use App\Models\Wishlist;
use App\Observers\WishlistObserver;
use App\Models\Page;
use App\Observers\PageObserver;
use App\Models\Platform;
use App\Observers\PlatformObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Listing::observe(ListingObserver::class);
        Game::observe(GameObserver::class);
        User::observe(UserObserver::class);
        MenuItem::observe(MenuItemObserver::class);
        Language::observe(LanguageObserver::class);
        Wishlist::observe(WishlistObserver::class);
        Page::observe(PageObserver::class);
        Platform::observe(PlatformObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      if ($this->app->environment() == 'local') {
      // Jeffrey Way's generators
          $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
      }
    }



}
