<?php
namespace App\Observers;

use App\Models\Wishlist;
use Carbon\Carbon;
use Cache;

class WishlistObserver
{

    /**
     * Listen to the Wishlist deleting event.
     *
     * @param  Wishlist  $wishlist
     * @return void
     */
    public function deleting(Wishlist $wishlist)
    {
        Cache::forget('wishlist_' . $wishlist->user_id);
        Cache::forget('popular_games');

        return true;
    }

    /**
     * Listen to the Wishlist created event.
     *
     * @param  Wishlist  $wishlist
     * @return void
     */
    public function created(Wishlist $wishlist)
    {
        Cache::forget('wishlist_' . $wishlist->user_id);
        Cache::forget('popular_games');

        return true;
    }
}
