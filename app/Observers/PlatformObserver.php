<?php
namespace App\Observers;

use App\Models\Platform;
use App\Models\Game;

class PlatformObserver
{

    /**
     * Listen to the Platform deleting event.
     *
     * @param  Platform  $platform
     * @return void
     */
    public function deleting(Platform $platform)
    {

        // Get all games
        $games = Game::where('platform_id', $platform->id)->get();

        foreach ($games as $game) {
            // remove game
            $game->delete();
        }

        return true;
    }
}
