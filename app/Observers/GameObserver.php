<?php
namespace App\Observers;

use App\Models\Game;
use App\Models\Listing;
use App\Models\User;
use App\Models\Wishlist;
use App\Notifications\ListingDeleted;
use Carbon\Carbon;
use Cache;

class GameObserver
{

    /**
     * Listen to the Game deleting event.
     *
     * @param  Listing  $listing
     * @return void
     */
    public function deleting(Game $game)
    {

        // Get all listing
        $listings = Listing::where('game_id', $game->id)->get();

        foreach ($listings as $listing) {
            // Check status of listing
            if ($listing->status == 0) {
                // Notifications to all open offer user and delete all offers
                foreach ($listing->offers as $offer) {
                    if ($offer->status == 0 && $offer->declined == 0) {
                        $offer_user = User::find($offer->user_id);
                        $offer_user->notify(new ListingDeleted($offer));
                        $offer->declined = 1;
                        $offer->decline_note = 'listings.general.deleted_game';
                        $offer->closed_at = new Carbon;
                        $offer->save();
                    }
                }
                // Remove images
                if (count($listing->images) > 0) {
                    foreach ($listing->images as $image) {
                        // Remove file image
                        $destination_path = 'public/listings';
                        $disk = "local";
                        \Storage::disk($disk)->delete($destination_path.'/'.$image->filename);

                        // Delete database entry
                        $image->delete();
                    }
                    $listing->picture = null;
                    $listing->save();
                }
                $listing->delete();
            }
        }

        // Get all wishlists
        $wishlists = Wishlist::where('game_id', $game->id)->get();

        foreach ($wishlists as $wishlist) {
            $wishlist->delete();
        }

        Cache::forget('games_slider');
        Cache::forget('popular_games');

        return true;
    }

    /**
     * Listen to the Game created event.
     *
     * @param  Game  $game
     * @return void
     */
    public function created(Game $game)
    {
        Cache::forget('games_slider');
        Cache::forget('popular_platforms');

        return true;
    }

    /**
     * Listen to the Game deleting event.
     *
     * @param  Game  $menuitem
     * @return void
     */
    public function updated(Game $game)
    {
        Cache::forget('games_slider');

        return true;
    }
}
