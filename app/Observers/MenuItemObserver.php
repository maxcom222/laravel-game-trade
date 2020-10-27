<?php
namespace App\Observers;

use App\Models\MenuItem;
use Cache;

class MenuItemObserver
{
    /**
     * Listen to the MenuItem created event.
     *
     * @param  MenuItem  $menuitem
     * @return void
     */
    public function created(MenuItem $menu_item)
    {
        Cache::forget('menu_items');

        return true;
    }

    /**
     * Listen to the MenuItem deleting event.
     *
     * @param  MenuItem  $menuitem
     * @return void
     */
    public function updated(MenuItem $menu_item)
    {
        Cache::forget('menu_items');

        return true;
    }

    /**
     * Listen to the MenuItem deleted event.
     *
     * @param  MenuItem  $menuitem
     * @return void
     */
    public function deleted(MenuItem $menu_item)
    {
        Cache::forget('menu_items');

        return true;
    }
}
