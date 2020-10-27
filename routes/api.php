<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/games/{id}', function ($id) {

    $game = \App\Models\Game::find($id);

    if (!$game) {
        return abort('404');
    }

    $data = array();

    $data['id'] = $game->id;
    $data['name'] = $game->name;
    $data['pic'] = $game->image_square_tiny;
    $data['console_name'] = $game->platform->name;
    $data['console_color'] = $game->platform->color;
    $data['listings'] = $game->listings_count;
    $data['cheapest_listing'] = $game->cheapest_listing;
    $data['url'] = $game->url_slug;

    return response()->json($data);

});

Route::get('/digitals/{acronym}', function ($acronym) {

    $platform = \App\Models\Platform::where('acronym', $acronym)->first();

    if(!$platform) {
        return abort('404');
    }

    return response()->json($platform->digitals);

});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
