<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Input;
use ClickNow\Money\Money;
use App\Models\Game;
use App\Models\Giantbomb;
use App\Models\Platform;
use App\Models\Genre;
use GuzzleHttp\Client;
use Searchy;
use Redirect;
use Request;
use Config;
use SEO;
use Session;
use Theme;

class GameController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Index all games
     *
     * @return Response
     */
    public function index()
    {
        // Games query
        $games = Game::query();

        // Order - default order is created_at
        $games_order = session()->has('gamesOrder') ? session()->get('gamesOrder') : 'release_date';

        // Platform filters
        if (session()->has('listingsPlatformFilter')) {
            $games = $games->whereIn('platform_id', session()->get('listingsPlatformFilter'));
        }

        // Load other tables
        $games = $games->with('platform','giantbomb','listingsCount','wishlistCount','metacritic');

        // Order direction - default is asc
        // Order by metascore
        if ($games_order == 'metascore') {
            $games = $games->join('games_metacritic', 'games.id', 'games_metacritic.game_id')->orderBy('games_metacritic.score', session()->has('gamesOrderByDesc') && session()->get('gamesOrderByDesc') ? 'asc' : 'desc')->select('games.*');
        // Order by listings count
        } elseif($games_order == 'listings') {
            $games = $games->withCount('listings')->orderBy('listings_count', session()->has('gamesOrderByDesc') && session()->get('gamesOrderByDesc') ? 'asc' : 'desc');
        // Order by popularity
        } elseif($games_order == 'popularity') {
            $games = $games->withCount('heartbeat')->orderBy('heartbeat_count', session()->has('gamesOrderByDesc') && session()->get('gamesOrderByDesc') ? 'asc' : 'desc');
        // default order
        } else {
            $games = $games->orderBy($games_order, session()->has('gamesOrderByDesc') && session()->get('gamesOrderByDesc') ? 'asc' : 'desc');
        }

        // Paginate games results
        $games = $games->paginate('36');

        // Cloudfare SSL fix
        if (config('settings.ssl')) {
            $games->setPath('https://' . Request::getHttpHost() . '/' . Request::path());
        }

        // Get the current page from the url if it's not set default to 1
        $page = Input::get('page', 0);

        // Redirect to first page if page from the get request don't exist
        if ($games->lastPage() < $page) {
            return redirect('games');
        }

        // Page title
        SEO::setTitle(trans('general.title.games_all', ['page_name' => config('settings.page_name'), 'sub_title' => config('settings.sub_title')]));

        // Page description
        SEO::setDescription(trans('general.description.games_all', ['games_count' => $games->total(), 'page_name' => config('settings.page_name'), 'sub_title' => config('settings.sub_title')]));

        // Check if ajax request
        if (Request::ajax()) {
            return view('frontend.game.ajax.index', ['games' => $games]);
        } else {
            return view('frontend.game.index', ['games' => $games]);
        }
    }

    /**
     * Display game infos with all listing
     *
     * @param  string   $slug
     * @return Response
     */
    public function show($slug)
    {
        // Get game id from slug string
        $game_id = ltrim(strrchr($slug,'-'),'-');
        $game = Game::with('listings')->find($game_id);

        // Check if game exists
        if (is_null($game)) {
            return abort('404');
        }

        // Check if slug is right
        $slug_check = str_slug($game->name) . '-' . $game->platform->acronym . '-' . $game->id;

        // Redirect to correct slug link
        if ($slug_check != $slug) {
            return Redirect::to(url('games/' . $slug_check));
        }

        // Page title & description
        SEO::setTitle(trans('general.title.game', ['game_name' => $game->name, 'platform' => $game->platform->name,'page_name' => config('settings.page_name')]));
        SEO::setDescription( (strlen($game->description) > 147) ? substr($game->description, 0, 147) . '...' : $game->description );

        // Get different platforms for the game
        $different_platforms = Game::where('giantbomb_id','!=','0')->where('giantbomb_id', $game->giantbomb_id )->where('id', '!=', $game->id)->where('platform_id', '!=', $game->platform_id)->with('platform')->get();

        // Get image size for og
        if ($game->image_cover) {
          // Check if image is corrupted
          try {
              $imgsize = getimagesize($game->image_cover);
              SEO::opengraph()->addImage(['url' => $game->image_cover, ['height' => $imgsize[1], 'width' => $imgsize[0]]]);
              // Twitter Card Image
              SEO::twitter()->setImage($game->image_cover);
          } catch(\Exception $e) {
              // Delete corrupted image
              // $disk = "local";
              // \Storage::disk($disk)->delete('/public/games/' . $game->cover );
              // $game->cover = null;
              // $game->save();
          }
        }

        return view('frontend.game.show', ['game' => $game, 'different_platforms' => $different_platforms]);
    }

    /**
     * Get media (images & videos) tab in game and listing overview
     *
     * @param  int  $id
     * @return Response
     */
    public function showMedia($id)
    {
        $game = Game::with('giantbomb')->find($id);

        // Accept only ajax requests
        if (!Request::ajax()) {
            // redirect to game if no AJAX request
            if ($game) {
                return Redirect::to(url($game->url_slug . '#!media'));
            } else {
                return abort('404');
            }
        }

        // Check if game exist
        if (!$game) {
            return abort('404');
        }

        // Get images from giantbomb
        if ($game->giantbomb_id != 0) {
            $images = json_decode($game->giantbomb->images);
            $videos = json_decode($game->giantbomb->videos);
        } else {
            $images = NULL;
            $videos = NULL;
        }

        // don't loose backUrl session if one is set
        if (Session::has('backUrl')) {
            Session::keep('backUrl');
        }

        return view('frontend.game.showMedia', ['game' => $game,'images' =>$images,'videos' =>$videos]);
    }

    /**
     * Get available trade games for the specific game in the tab in game overview
     *
     * @param  int  $id
     * @return Response
     */
    public function showTrade($id)
    {
        $game = Game::find($id);

        // Accept only ajax requests
        if (!Request::ajax()) {
            // redirect to game if no AJAX request
            if ($game) {
                return Redirect::to(url($game->url_slug . '#!trade'));
            } else {
                return abort('404');
            }
        }

        // Check if game exist
        if (!$game) {
            return abort('404');
        }

        // help to check if trade games was removed in the next step
        $removed_games = false;

        // Remove not active listings
        foreach ($game->tradegames as $listing) {
            // check if listing is removed or not active
            if ($listing->status == 1 || $listing->status == 2 || $listing->deleted_at) {
                \DB::table('game_trade')->where('listing_id', $listing->id)->where('game_id', $game->id)->delete();
                $removed_games = true;
            }
        }

        if ($removed_games) {
            // Refresh game model
            $game = $game->fresh();
        }

        return view('frontend.game.showTrade', ['tradegames' => $game->tradegames]);
    }

    /**
     * Form for adding a new game
     *
     * @return Response
     */
    public function add()
    {

        // Check if user can add games to the system
        if (!Config::get('settings.user_add_item') && !(\Auth::user()->can('edit_games'))) {
            return abort(404);
        }

        // Page title
        SEO::setTitle(trans('general.title.game_add', ['page_name' => config('settings.page_name')]));

        return view('frontend.game.add', ['platforms' => Platform::all()]);
    }

    /**
     * Search games
     *
     * @param  int  $id
     * @return Response
     */
    public function search($value)
    {
        // get all inpus
        $input = Input::all();

        // search for games
        $games = Game::hydrate(Searchy::games('name', 'tags')->query($value)->get()->toArray() );

        $games->load('platform','giantbomb');

        // Get the current page from the url if it's not set default to 1
        $page = Input::get('page', 1);

        // Number of items per page
        $perPage = 36;

        // Start displaying items from this number;
        $offSet = ($page * $perPage) - $perPage; // Start displaying items from this number

        // Get only the items you need using array_slice (only get 10 items since that's what you need)
        //$itemsForCurrentPage = array_slice($deals_query->toArray(), $offSet, $perPage, true);

        // Page title
        SEO::setTitle(trans('general.title.search_result', ['page_name' => config('settings.page_name'), 'sub_title' => config('settings.sub_title'),'value' => $value]));

        // and return to typeahead
        return view('frontend.game.searchindex', ['games' => new \Illuminate\Pagination\LengthAwarePaginator($games->forPage($page,$perPage), count($games), $perPage, $page, ['path' => Request::url()]), 'value' => $value]);
    }

    /**
     * Metacritic api search
     *
     * @param  Request  $request
     * @return Response
     */
    public function searchApi(\Illuminate\Http\Request $request)
    {
        // Accept only ajax requests
        if(!Request::ajax()){
            return abort('404');
        }

        $client = new Client();

        // search with metacritic api
        $res = $client->request('GET', url('metacritic/search/game?platform=' . $request->search_param . '&title=' . $request->game));

        $json_results = json_decode($res->getBody())->results;

        $platform = Platform::where('acronym',$request->search_param)->first();

        if(!$platform){
          $platform->id = NULL;
          $platform->name = $result->platform;
          $platform->color = "#e8eff0";
          $platform->acronym = NULL;
        }

        // and return view to ajax
        return view('frontend.game.api.search', ['json_results' => $json_results, 'platform' => $platform, 'value' => $request->game, 'trade_search' => $request->trade_search]);
    }

    /**
     * Search with json response
     *
     * @param  String  $value
     * @return JSON
     */
    public function searchJson($value)
    {
        // Accept only ajax requests
        if(!Request::ajax()){
            return abort('404');
        }

        $games = Game::hydrate(Searchy::games('name', 'tags')->query($value)
      ->getQuery()->limit(10)->get()->toArray() );

        $games->load('platform','giantbomb','listingsCount','cheapestListing');

        $data = array();

        foreach ($games as $game) {
            $image_name = substr($game->cover, 0, -4);
            $data[" " . $game->id]['id'] = $game->id;
            $data[" " . $game->id]['name'] = $game->name;
            $data[" " . $game->id]['pic'] = $game->image_square_tiny;
            $data[" " . $game->id]['platform_name'] = $game->platform->name;
            $data[" " . $game->id]['platform_color'] = $game->platform->color;
            $data[" " . $game->id]['platform_acronym'] = $game->platform->acronym;
            $data[" " . $game->id]['platform_digital'] = $game->platform->digitals->count() > 0 ? true : false;
            $data[" " . $game->id]['listings'] = $game->listings_count;
            $data[" " . $game->id]['release_year'] = $game->release_date ? $game->release_date->format('Y') : 'unknown';
            $data[" " . $game->id]['cheapest_listing'] = $game->cheapest_listing;
            $data[" " . $game->id]['url'] = $game->url_slug;
            $data[" " . $game->id]['avgprice'] = $game->getAveragePrice();
            $data[" " . $game->id]['avgprice_string'] = trans('listings.form.sell.avgprice', ['game_name' => $game->name, 'avgprice' => $game->getAveragePrice() ]);
        }

        // and return to typeahead
        return response()->json($data);
    }

    /**
     * Add new game to database
     *
     * @param  Request  $request
     * @param  boolean  $json
     * @return respnose
     */
    public function addgame(\Illuminate\Http\Request $request, $json = null)
    {
        // Accept only ajax requests
        if (!Request::ajax()) {
            return abort('404');
        }

        // Ignore user aborts and allow the script
        // to run forever
        ignore_user_abort(true);
        // set_time_limit(0);

        // Check and get platform data
        $platform = Platform::where('acronym', $request->platform)->first();

        // get all genres
        $genres = Genre::all();

        if ($platform) {
            $platform_id = $platform->id;
        } else {
            $platform_id = 0;
        }

        try {
            // New request to mc api
            $client = new Client();
            $res = $client->request('GET', url('metacritic/find/game?platform=' . $request->platform . '&title='  .  urlencode($request->value) ) );
        } catch (\Exception $e) {
            // show a error message
            \Alert::error('<i class="fa fa-times m-r-5"></i> API Error!')->flash();
            return url()->previous();
        }

        // decode results
        $json_results = json_decode($res->getBody())->result;

        // abort and return 404 on error
        if (!$json_results) {
            return urlencode($request->value);
        }

        // check if release is unknown
        $unknown_release = $json_results->rlsdate == '1970-01-01';

        // create new game and add data
        $game = new Game;

        $game->name = $json_results->name;
        $game->platform_id = $platform_id;
        $game->publisher = $json_results->publisher;
        $game->developer = $json_results->developer;
        $game->release_date = $unknown_release ? (date('Y') + 1) . '-01-01'  : $json_results->rlsdate;

        // Save game in database
        $game->save();

        // get game ID
        $game_id = $game->id;

        try {
            // JSON Data for new metacritic for SQL Insert
            $data_meta = array(
                'game_id' => $game_id,
                'name' => $json_results->name,
      	        'score' => isset($json_results->score) && $json_results->score != '' ? $json_results->score : NULL,
      	        'userscore' =>  isset($json_results->userscore) ? $json_results->userscore*10 : NULL,
                'thumbnail' => $json_results->thumbnail,
                'summary' => $json_results->summary,
                'platform' => $json_results->platform,
                'genre' => json_encode($json_results->genre),
      	        'publisher' => $json_results->publisher,
      	        'developer' => $json_results->developer,
                'rating' => $json_results->rating,
                'release_date' => $unknown_release ? (date('Y') + 1) . '-01-01' : $json_results->rlsdate,
      	        'url' => $json_results->url
            );

            // Insert Data in Table
            $metacritic_id = \DB::table('games_metacritic')->insertGetId($data_meta);
        } catch (\Exception $e) {
            // Delete game
            $game->forceDelete();
            // show a error message
            \Alert::error('<i class="fa fa-times m-r-5"></i> MC Error!')->flash();
            return url()->previous();
        }

        // START GIANTBOMB
        $metacritic_name = \DB::table('games_metacritic')->where('game_id', $game_id)->pluck('name');

        $apiKey = str_replace(' ', '', Config::get('settings.giantbomb_key'));

        try {
            // Create a Config object and pass it to the Client
            $config = new \DBorsatto\GiantBomb\Config($apiKey);
            $client = new \DBorsatto\GiantBomb\Client($config);
            $results = $client->search('"'.$metacritic_name.'"', 'game');
        } catch (\Exception $e) {
            // Delete game
            $game->forceDelete();
            // show a error message
            \Alert::error('<i class="fa fa-times m-r-5"></i> GiantBomb Error! Wrong API Key?')->flash();
            return url()->previous();
        }

        if (count($results)>0) {
            // Check Releaseyear
            $game_number = 0;
            $metacritic_year = $unknown_release ? date('Y') : substr($json_results->rlsdate, 0, 4);

            do {
                if (isset($results{$game_number})) {
                    if ($unknown_release) {
                        $giantbomb_year = substr($results{$game_number}->original_release_date, 0, 4);
                        $giantbomb_added = substr($results{$game_number}->date_added, 0, 4);

                        // Check for release date
                        if ($giantbomb_year >= $metacritic_year || $results{$game_number}->expected_release_year >= $metacritic_year || $giantbomb_added >= $metacritic_year-1) {
                            break;
                        } else {
                            $game_number++;
                        }
                    } else {
                        $giantbomb_year = substr($results{$game_number}->original_release_date, 0, 4);
                        $giantbomb_added = substr($results{$game_number}->date_added, 0, 4);

                        // Check if name is exact the same
                        if (strcmp($results{$game_number}->name, $json_results->name) == 0 ) {
                            break;
                        }

                        // Check for release date
                        if ($giantbomb_year == $metacritic_year || $results{$game_number}->expected_release_year == $metacritic_year || $giantbomb_added == $metacritic_year) {
                            break;
                        } else {
                            $game_number++;
                        }
                    }
                } else {
                    break;
                }
            } while (true);

            if (isset($results{$game_number})) {

                $gameid = '3030-'. $results{$game_number}->id;

                // Check if giantbomb data already exists
                $giantbomb_check = \DB::table('games_giantbomb')->where('id', $results{$game_number}->id)->first();

                if (!$giantbomb_check) {

                    $gamegb = $client->findOne('Game', $gameid);

                    $images = $gamegb->get('images');
                    $cover_image = $gamegb->get('image');
                    $videos = $gamegb->get('videos');

                    // Get genres if exists
                    try {
                      $genres = $gamegb->get('genres');
                    } catch(\InvalidArgumentException $ex) {
                      $genres = null;
                    }

                    $new_images = array();
                    $new_videos = array();

                    if ($genres) {
                        $new_genres = array();

                        // Genres add
                        foreach ($genres as $genre) {
                            array_push($new_genres, $genre['name']);
                            $check_genre = Genre::where('name', $genre['name'])->first();
                            if (!$check_genre) {
                                if (Config::get('settings.automatic_genres')) {
                                    $new_genre = new Genre;
                                    $new_genre->name = $genre['name'];
                                    $new_genre->save();
                                    $game->genre_id = $new_genre->id;
                                }
                            } else {
                                $game->genre_id = $check_genre->id;
                            }
                        }
                    }

                    // Image add
                    $image_help = 0;

                    foreach ($images as $image) {
                        $new_images[$image_help]['image'] = substr($image['icon_url'], 50 );
                        $new_images[$image_help]['tags'] = $image['tags'];
                        $image_help++;
                    }

                    // Video Add

                    $video_help = 0;

                    foreach ($videos as $video_api) {
                        if ($video_help == 20) {
                            break;
                        }

                        if (substr($video_api['name'], 0, 16 ) != "Bombin' the A.M.") {
                            try {

                                $video = $client->findOne('Video', substr($video_api['api_detail_url'], 36, -1 ) );

                                $new_videos[$video_help]['name'] = $video_api['name'];
                                $new_videos[$video_help]['api_id'] = substr($video_api['api_detail_url'], 36, -1 );

                                $new_videos[$video_help]['id'] = $video->get('id');
                                $new_videos[$video_help]['length_seconds'] = $video->get('length_seconds');
                                $new_videos[$video_help]['deck'] = $video->get('deck');
                                $new_videos[$video_help]['video_type'] = $video->get('video_type');

                                if ($video->get('youtube_id') == '') {
                                    $new_videos[$video_help]['youtube_id'] = 0;
                                } else {
                                    $new_videos[$video_help]['youtube_id'] = $video->get('youtube_id');
                                }


                                $video_image = $video->get('image');

                                $help_image = substr($video_image['icon_url'], 50 );

                                $datatype = substr( $help_image, -3 );
                                $imgend = substr( $help_image, -6, 2 );


                                //$imageArray = @getimagesize('http://www.giantbomb.com/api/image/scale_small/'. $help_image);


                                $url = 'https://www.giantbomb.com/api/image/scale_small/'. $help_image;
                                $imgHeaders = @get_headers( str_replace(' ', "%20", $url) )[0];
                                $imgfix = $help_image;

                                if( $imgHeaders == 'HTTP/1.1 403 Forbidden' ) {
                                    $imgfix = $help_image;
                                }elseif( $imgHeaders == 'HTTP/1.1 404 Not Found' ) {
                                    $imgfix = substr($help_image, 0, -4 ) . '.jpg';
                                }

                                $new_videos[$video_help]['image'] = $imgfix;

                                $video_help++;


                            } catch(\RuntimeException $e) {
                                // catch code
                            }

                        }

                    }

                    // PEGI Rating and get all ratings
                    $ratings = $gamegb->get('original_game_rating');

                    $all_ratings = array();

                    if ($ratings != '') {

                        $pegi = 0;

                        foreach ($ratings as $rating) {
                            // For array
                            array_push($all_ratings, $rating['name']);

                            // For database
                            $rating_name = substr($rating['name'], 0, 4);

                            if ($rating_name == "PEGI") {
                                $pegi = substr($rating['name'], 6, -1 );
                            }
                        }

                        if ($pegi != 0) {
                            $game->pegi = $pegi;
                        }
                    }

                    // Tags add
                    if ($gamegb->get('aliases') != '') {
                        $game->tags = $gamegb->get('aliases');
                    }

                    // Data for SQL Insert
                    $data = array(
                        'id' => $results{$game_number}->id,
                        'name' => $results{$game_number}->name,
                        'summary' => $results{$game_number}->deck,
                        'genres' => $genres ? json_encode($new_genres) : null,
                        'image' => substr($cover_image['icon_url'], 50 ),
                        'images' => json_encode($new_images),
                        'videos' => json_encode($new_videos),
                        'ratings' => json_encode($all_ratings)
                    );

                    // Inser Data in Table
                    \DB::table('games_giantbomb')->insert($data);

                    // Image Beta
                    $extension = 'jpg';
                    $newfilename = time().'-'.$game_id.'.'.$extension;
                    $disk = "local";
                    $destination_path = "public/games";

                    $image_client = new Client();
                    $image = $image_client->request('GET', $cover_image['super_url']);

                    // 2. Store the image on disk.
                    \Storage::disk($disk)->put($destination_path.'/'.$newfilename, $image->getBody()->getContents());


                    // Update Game Data with Giantbomb Info
                    $game->giantbomb_id = $results{$game_number}->id;
                    $game->cover = $newfilename;
                    $game->description = $results{$game_number}->deck;
                    $game->save();

                } else {

                    // get genre from giantbomb
                    if ($giantbomb_check->genres) {
                        $giantbomb_genres = json_decode($giantbomb_check->genres);
                        $db_genre = Genre::where('name', $giantbomb_genres[0])->first();
                        if ($db_genre) {
                            $game->genre_id = $db_genre->id;
                        } else {
                            if (Config::get('settings.automatic_genres')) {
                                $new_genre = new Genre;
                                $new_genre->name = $genre['name'];
                                $new_genre->save();
                                $game->genre_id = $new_genre->id;
                            }
                        }
                    }

                    if ($giantbomb_check->image) {
                        // Image Beta
                        $extension = 'jpg';
                        $newfilename = time().'-'.$game_id.'.'.$extension;
                        $disk = "local";
                        $destination_path = "public/games";

                        $image_client = new Client();
                        $image = $image_client->request('GET', 'https://www.giantbomb.com/api/image/scale_super/' . $giantbomb_check->image);

                        // 2. Store the image on disk.
                        \Storage::disk($disk)->put($destination_path.'/'.$newfilename, $image->getBody()->getContents());

                        $game->cover = $newfilename;
                    }

                    // get game with giantbomb id for tags and PEGI, when game exists
                    $giantbomb_game = Game::where('giantbomb_id', $giantbomb_check->id)->first();
                    if ($giantbomb_game) {
                        // Tags add
                        if ($giantbomb_game->tags){
                          $game->tags = $giantbomb_game->tags;
                        }

                        // Pegi add
                        if($giantbomb_game->pegi){
                          $game->pegi = $giantbomb_game->pegi;
                        }
                    }

                    // Get data from giantbomb and save to new game
                    $game->giantbomb_id = $giantbomb_check->id;
                    $game->description = $giantbomb_check->summary;
                    $game->save();
                }
            }

        }

        if (is_null($json)) {
            // output url from game
            return url($game->url_slug);
        } else {
            // output game data as json
            $data = array();

            $data['id'] = $game->id;
            $data['name'] = $game->name;
            $data['pic'] = $game->image_square_tiny;
            $data['platform_name'] = $game->platform->name;
            $data['platform_color'] = $game->platform->color;
            $data['listings'] = $game->listings_count;
            $data['cheapest_listing'] = $game->cheapest_listing;
            $data['url'] = $game->url_slug;

            return response()->json($data);
        }
  	}

    /**
     * Refresh metacritic data for game
     *
     * @param  int  $game_id
     * @return redirect
     */

    public function refresh_metacritic($game_id)
    {
        $game = Game::with('listings')->find($game_id);

        // Check if game exists
        if (is_null($game)) {
            return abort('404');
        }

        // Check if logged in
        if (!(\Auth::check())) {
          return Redirect::to(url('login'));
        }

        // Check if user can edit games
        if (!(\Auth::user()->can('edit_games'))) {
           return abort('403');
        }

        // Ignore user aborts and allow the script
        // to run forever
        ignore_user_abort(true);
        // set_time_limit(0);

        // New request to mc api
        $client = new Client();

        $res = $client->request('GET', url('metacritic/find/game?platform=' . $game->platform->acronym . '&title='  .  urlencode($game->metacritic->name) ) );

        // decode results
        $json_results = json_decode($res->getBody())->result;

        // abort and return 404 on error
        if (!$json_results) {
            return abort('404');
        }

        // JSON Data for new metacritic for SQL Insert
        $data_meta = array(
            'game_id' => $game->id,
            'name' => $json_results->name,
            'score' => isset($json_results->score) && $json_results->score != '' ? $json_results->score : NULL,
            'userscore' =>  isset($json_results->userscore) ? $json_results->userscore*10 : NULL,
            'thumbnail' => $json_results->thumbnail,
            'summary' => $json_results->summary,
            'platform' => $json_results->platform,
            'genre' => json_encode($json_results->genre),
            'publisher' => $json_results->publisher,
            'developer' => $json_results->developer,
            'rating' => $json_results->rating,
            'release_date' => $json_results->rlsdate,
            'url' => $json_results->url
        );

        // Inser Data in Table
        $metacritic_id = \DB::table('games_metacritic')->where('id', $game->metacritic->id)->update($data_meta);

        // show a success message
        \Alert::success('<i class="fa fa-save m-r-5"></i> ' . $game->name . ' Metacritic data successfully refreshed!')->flash();

        return Redirect::to(url($game->url_slug));
    }

    /**
     * Change giantbomb id
     *
     * @param  Request  $request
     * @return redirect
     */

    public function change_giantbomb (\Illuminate\Http\Request $request) {

        // decrypt input
        $request->merge(array('game_id' => decrypt($request->game_id)));

        $this->validate($request, [
            'game_id' => 'required|exists:games,id'
        ]);

        $game = Game::with('listings')->find($request->game_id);

        // Check if game exists
        if (is_null($game)) {
            return abort('404');
        }

        // Check if logged in
        if (!(\Auth::check())) {
            return Redirect::to(url('login'));
        }

        // Check if user can edit games
        if (!(\Auth::user()->can('edit_games'))) {
	         return abort('403');
        }

        // Ignore user aborts and allow the script
        // to run forever
        ignore_user_abort(true);
        // set_time_limit(0);

        $apiKey = str_replace(' ', '', Config::get('settings.giantbomb_key'));

        // Create a Config object and pass it to the Client
        $config = new \DBorsatto\GiantBomb\Config($apiKey);
        $client = new \DBorsatto\GiantBomb\Client($config);

        // New Giantbomb ID
        $new_giantbomb_id = '3030-'. $request->giantbomb_id;

        // Check if giantbomb id is already in the database
        $giantbomb_check = Giantbomb::find($request->giantbomb_id);

        // Giantbomb ID is in database
        if ($giantbomb_check) {

            // get genre from giantbomb
            if ($giantbomb_check->genres) {
                $giantbomb_genres = json_decode($giantbomb_check->genres);
                $db_genre = Genre::where('name', $giantbomb_genres[0])->first();
                if ($db_genre) {
                    $game->genre_id = $db_genre->id;
                } else {
                    if (Config::get('settings.automatic_genres')) {
                        $new_genre = new Genre;
                        $new_genre->name = $genre['name'];
                        $new_genre->save();
                        $game->genre_id = $new_genre->id;
                    }
                }
            }

            if ($giantbomb_check->image) {
                // Image Beta
                $extension = 'jpg';
                $newfilename = time().'-'.$game->id.'.'.$extension;
                $disk = "local";
                $destination_path = "public/games";

                // https giantbomb fix
                if ($giantbomb_check->image[0] == '/') {
                    $giantbomb_check->image = substr($giantbomb_check->image, 1);
                    $giantbomb_check->save();
                }

                $image_client = new Client();
                $image = $image_client->request('GET', 'http://www.giantbomb.com/api/image/scale_super/' . $giantbomb_check->image);

                // 2. Store the image on disk.
                \Storage::disk($disk)->put($destination_path.'/'.$newfilename, $image->getBody()->getContents());

                // Delete old image
                if (!is_null($game->cover)) {
                    \Storage::disk($disk)->delete('/public/games/' . $game->cover );
                }

                $game->cover = $newfilename;
            }

            // get game with giantbomb id for tags and PEGI, when game exists
            $giantbomb_game = Game::where('giantbomb_id', $giantbomb_check->id)->first();
            if ($giantbomb_game) {
                // Tags add
                if ($giantbomb_game->tags) {
                    $game->tags = $giantbomb_game->tags;
                }

                // Pegi add
                if ($giantbomb_game->pegi){
                    $game->pegi = $giantbomb_game->pegi;
                }
            }

            // Get data from giantbomb and save to new game
            $game->giantbomb_id = $giantbomb_check->id;
            $game->description = $giantbomb_check->summary;

            $game->save();

        // Add new Giantbomb data to database
        } else {

            // get giantbomb data
            try {
                $giantbomb_game = $client->findOne('Game', $new_giantbomb_id);

                // Catch 404 error, when Giantbomb ID does not exists
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // show a error message
                \Alert::error('<i class="fa fa-times m-r-5"></i> Sorry, this Giantbomb ID does not exists!')->flash();
                return Redirect::to(url($game->url_slug));
            }

            $images = $giantbomb_game->get('images');
            $cover_image = $giantbomb_game->get('image');
            $videos = $giantbomb_game->get('videos');

            // Get genres if exists
            try {
              $genres = $giantbomb_game->get('genres');
            } catch (\InvalidArgumentException $ex) {
              $genres = null;
            }

            $new_images = array();
            $new_videos = array();

            if ($genres) {
                $new_genres = array();

                // Genres add
                foreach ($genres as $genre) {
                    array_push($new_genres, $genre['name']);
                    $check_genre = Genre::where('name', $genre['name'])->first();
                    if (!$check_genre) {
                        if (Config::get('settings.automatic_genres')) {
                            $new_genre = new Genre;
                            $new_genre->name = $genre['name'];
                            $new_genre->save();
                            $game->genre_id = $new_genre->id;
                        }
                    } else {
                        $game->genre_id = $check_genre->id;
                    }
                }
            }

            // Image add
            $image_help = 0;

            foreach ($images as $image) {
                $new_images[$image_help]['image'] = substr($image['icon_url'], 51 );
                $new_images[$image_help]['tags'] = $image['tags'];
                $image_help++;
            }

            // Video Add

            $video_help = 0;

            foreach($videos as $video_api) {
                if ($video_help == 20) {
                  break;
                }

                if (substr($video_api['name'], 0, 16 ) != "Bombin' the A.M.") {

                    try {

                        $video = $client->findOne('Video', substr($video_api['api_detail_url'], 36, -1 ) );

                        $new_videos[$video_help]['name'] = $video_api['name'];
                        $new_videos[$video_help]['api_id'] = substr($video_api['api_detail_url'], 36, -1 );

                        $new_videos[$video_help]['id'] = $video->get('id');
                        $new_videos[$video_help]['length_seconds'] = $video->get('length_seconds');
                        $new_videos[$video_help]['deck'] = $video->get('deck');
                        $new_videos[$video_help]['video_type'] = $video->get('video_type');

                        if ($video->get('youtube_id') == '') {
                            $new_videos[$video_help]['youtube_id'] = 0;
                        } else {
                            $new_videos[$video_help]['youtube_id'] = $video->get('youtube_id');
                        }

                        $video_image = $video->get('image');

                        $help_image = substr($video_image['icon_url'], 50 );

                        $datatype = substr( $help_image, -3 );
                        $imgend = substr( $help_image, -6, 2 );

                        $url = 'https://www.giantbomb.com/api/image/scale_small/'. $help_image;
                        $imgHeaders = @get_headers( str_replace(' ', "%20", $url) )[0];
                        $imgfix = $help_image;

                        if ($imgHeaders == 'HTTP/1.1 403 Forbidden') {
                            $imgfix = $help_image;
                        } elseif ($imgHeaders == 'HTTP/1.1 404 Not Found') {
                            $imgfix = substr($help_image, 0, -4 ) . '.jpg';
                        }

                        $new_videos[$video_help]['image'] = $imgfix;

                        $video_help++;


                    } catch (\RuntimeException $e) {
                        // catch code
                    }
                }

            }

            // PEGI Rating and get all ratings
            $ratings = $giantbomb_game->get('original_game_rating');

            $all_ratings = array();

            if ($ratings != '') {
                $pegi = 0;

                foreach ($ratings as $rating) {
                    // For array
                    array_push($all_ratings, $rating['name']);

                    // For database
                    $rating_name = substr($rating['name'], 0, 4);

                    if ($rating_name == "PEGI") {
                        $pegi = substr($rating['name'], 6, -1 );
                    }
                }

                if ($pegi != 0) {
                    $game->pegi = $pegi;
                }
            }

            // Tags add
            if ($giantbomb_game->get('aliases') != '') {
              $game->tags = $giantbomb_game->get('aliases');
            }

            // Data for SQL Insert
            $data = array(
                'id' => $giantbomb_game->id,
                'name' => $giantbomb_game->name,
                'summary' => $giantbomb_game->deck,
                'genres' => $genres ? json_encode($new_genres) : null,
                'image' => substr($cover_image['icon_url'], 50 ),
                'images' => json_encode($new_images),
                'videos' => json_encode($new_videos),
                'ratings' => json_encode($all_ratings)
            );

            // Inser Data in Table
            \DB::table('games_giantbomb')->insert($data);

            // Image Beta
            $extension = 'jpg';
            $newfilename = time().'-'.$game->id.'.'.$extension;
            $disk = "local";
            $destination_path = "public/games";

            $image_client = new Client();
            $image = $image_client->request('GET', $cover_image['super_url']);

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$newfilename, $image->getBody()->getContents());

            // Delete old image
            if (!is_null($game->cover)) {
                \Storage::disk($disk)->delete('/public/games/' . $game->cover );
            }

            // Update Game Data with Giantbomb Info
            $game->giantbomb_id = $giantbomb_game->id;
            $game->cover = $newfilename;
            $game->description = $giantbomb_game->deck;
            $game->save();
        }

        // show a success message
    		\Alert::success('<i class="fa fa-save m-r-5"></i> ' . $game->name . ' Giantbomb ID successfully changed!')->flash();
        return Redirect::to(url($game->url_slug));
    }

    /**
     * Sort games
     *
     * @param  string  $slug
     * @return mixed
     */
    public function order($order, $desc = null)
    {

        if ($order == 'release_date' || $order == 'metascore' || $order == 'listings' || $order == 'popularity') {
            session()->put('gamesOrder', $order);
        } else {
            session()->remove('gamesOrder');
        }

        if ($desc == 'desc') {
            session()->put('gamesOrderByDesc', true);
        } else {
            session()->put('gamesOrderByDesc', false);
        }

        return Redirect::to(url()->current() == url()->previous() ? url('/') : url()->previous());
    }
}
