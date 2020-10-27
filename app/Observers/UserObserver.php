<?php
namespace App\Observers;

use App\Models\User;
use App\Notifications\ListingDeleted;
use Carbon\Carbon;

class UserObserver
{

    /**
     * Listen to the User deleting event.
     *
     * @param  Listing  $listing
     * @return void
     */
    public function deleting(User $user)
    {

        return true;
    }
}
