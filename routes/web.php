<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

// Startpage
Route::get('/', 'PageController@startpage')->name('index');

/**
 * These routes require no user to be logged in
 */
Route::group(['middleware' => 'guest','namespace' => 'Frontend\Auth', 'as' => 'frontend.auth.'], function () {
    // Authentication Routes
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login')->name('login.post');

    // Socialite Routes
    Route::get('login/{provider}', 'SocialLoginController@login')->name('social.login');

    // Confirm Account Routes
    Route::get('account/confirm/{token}', 'ConfirmAccountController@confirm')->name('account.confirm');
    Route::get('account/confirm/resend/{user}', 'ConfirmAccountController@sendConfirmationEmail')->name('account.confirm.resend');

    // Password Reset Routes
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.email');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');

    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset.form');
    Route::post('password/reset', 'ResetPasswordController@reset')->name('password.reset');
});

/**
 * These routes require the user to be logged in
 */


// Game Routes
Route::group(['prefix' => 'games'], function()
{
    Route::get('/', 'GameController@index')->middleware('contentlength')->name('games');
    Route::get('add', 'GameController@add')->middleware('auth');
    Route::post('add/{json?}', 'GameController@addgame');
    Route::get('search', function () {
        return view('frontend.game.search');
    });
    Route::get('{slug}', 'GameController@show')->name('game');
    Route::get('{id}/media', 'GameController@showMedia');
    Route::get('{id}/trade', 'GameController@showTrade');
    Route::get('search/json/{value}', 'GameController@searchJson');
    Route::post('api/search', 'GameController@searchApi');
    Route::get('order/{sort}/{desc?}', 'GameController@order')->middleware('contentlength');

    // Wishlist
    Route::post('{slug}/wishlist/add', 'WishlistController@add');
    Route::post('{slug}/wishlist/update', 'WishlistController@update');
    Route::get('{slug}/wishlist/delete', 'WishlistController@delete');

    // Admin quick actions
    Route::get('{game_id}/refresh/metacritic', 'GameController@refresh_metacritic')->middleware('permission:edit_games');
    Route::post('change/giantbomb', 'GameController@change_giantbomb')->middleware('permission:edit_games');
});
Route::get('search/{value}', 'GameController@search')->name('search');

// Listing Routes
Route::group(['prefix' => 'listings'], function()
{
    Route::get('', 'ListingController@index')->middleware('contentlength')->name('listings');
    Route::get('add', 'ListingController@add')->middleware('auth');
    Route::post('add', 'ListingController@store')->middleware('auth');
    Route::post('edit', 'ListingController@edit')->middleware('auth');
    Route::post('delete', 'ListingController@delete')->middleware('auth');
    Route::get('{slug}/edit', 'ListingController@editForm')->middleware('auth');
    Route::get('{slug}/add', 'ListingController@gameForm')->middleware('auth');
    Route::get('{slug}', 'ListingController@selectIndex')->middleware('contentlength')->name('listing');
    Route::get('{id}/images', 'ListingController@images')->name('listing.images');
    Route::post('{id}/images/sort', 'ListingController@imagesSort')->name('listing.images.sort');
    Route::post('{id}/images/upload', 'ListingController@imagesUpload')->name('listing.images.upload');
    Route::post('images/upload', 'ListingController@imagesUpload');
    Route::post('{id}/images/remove', 'ListingController@imagesRemove')->name('listing.images.remove');
    Route::get('order/{sort}/{desc?}', 'ListingController@order');
    Route::post('filter', 'ListingController@filter')->middleware('contentlength');
    Route::get('filter/remove', 'ListingController@filterRemove')->middleware('contentlength');
});

// Offer Routes
Route::group(['prefix' => 'offer', 'as' => 'frontend.offer.'], function()
{
    Route::post('add', 'OfferController@add');
    Route::post('accept', 'OfferController@accept');
    Route::post('decline', 'OfferController@decline');
    Route::post('rating', 'OfferController@rate');
    Route::post('delete', 'OfferController@delete');
    Route::get('{id}', 'OfferController@show')->name('show');
    Route::post('message', 'OfferController@newMessage');
    Route::post('report', 'OfferController@report');

    // Payment routes
    Route::get('{id}/pay', 'OfferController@pay')->name('pay');
    Route::post('pay/balance', 'OfferController@payBalance')->name('pay.balance');
    Route::get('{id}/pay/cancel', 'OfferController@payCancel')->name('pay.cancel');
    Route::get('{id}/pay/success', 'OfferController@paySuccess')->name('pay.success');
    Route::get('{id}/pay/refund', 'OfferController@payRefund')->name('pay.refund');
    Route::get('{id}/pay/release', 'OfferController@payRelease')->name('pay.release');
    Route::get('{id}/transaction', 'OfferController@transaction')->name('transaction');

    // Stripe routes
    Route::get('{id}/pay/stripe/success/{token}', 'OfferController@payStripe')->name('pay.stripe.success');


    // Offer Admin Report Routes
    Route::group(['prefix' => 'admin', 'as' => 'frontend.offer.admin.', 'middleware' => ['permission:edit_offers']], function()
    {
        Route::get('report/{id}', 'OfferController@reportShow');
        Route::get('report/close/{id}', 'OfferController@reportClose');
        Route::get('{id}/ban/{user_id}', 'OfferController@reportBan');
        Route::get('{id}/close/{reopen?}', 'OfferController@reportOfferClose');
        Route::get('{id}/revoke/{rating_id}', 'OfferController@reportRevoke');

        // Rating Admin Route
        Route::get('rating/{id}', 'OfferController@ratingShow');
    });
});
Route::get('/ajaxchat/{demand_id}', 'OfferController@chatOverview');

// User Routes
Route::get('/user/{slug}', 'UserController@show')->name('profile');
Route::post('/user/push/{func}', 'UserController@push');
Route::get('/user/search/json/{value}', 'UserController@searchJson');

// Logout Route
Route::get('logout', 'Frontend\Auth\LoginController@logout')->middleware('auth')->name('logout');
// Registration Route
Route::post('register', 'Frontend\Auth\RegisterController@register')->name('register');

// Dashboard Routes
Route::group(['prefix' => 'dash', 'middleware' => 'auth'], function()
{
    Route::get('', 'UserController@dashboard')->name('frontend.dash');
    Route::get('notifications', 'UserController@notifications');
    Route::post('notifications/read', 'UserController@notificationsRead');
    Route::get('notifications/read/all', 'UserController@notificationsReadAll');
    Route::get('listings', 'UserController@listings');
    Route::get('listings/{sort?}', 'UserController@listings');
    Route::get('offers', 'UserController@offers');
    Route::get('offers/{sort?}', 'UserController@offers');
    Route::get('wishlist', 'WishlistController@index');
    Route::get('settings', 'UserController@settingsForm');
    Route::post('settings', 'UserController@settingsSave')->name('dashboard.settings');;
    Route::get('settings/password', 'UserController@passwordForm');
    Route::post('settings/password', 'UserController@changePassword');
    Route::post('settings/location', 'UserController@locationSave');
    Route::get('notifications/api', 'UserController@notificationsApi');

    // Dashboard payment
    Route::get('balance', 'UserController@balance');
    Route::get('balance/withdrawal', 'UserController@withdrawal');
    Route::post('balance/withdrawal/{method?}', 'UserController@addWithdrawal');
});

// Metacritic API Routes
Route::get('metacritic/search/{type}', 'API\MetacriticController@search');
Route::get('metacritic/find/{type}', 'API\MetacriticController@find');

// Switch between the included languages
Route::get('lang/{lang}', 'LanguageController@swap');

// Switch between themes
Route::get('theme/{lang}', 'ThemeController@swap');

// Contact form
Route::post('contact', 'PageController@contact');

// SEO Routes
Route::get('sitemap', 'SeoController@sitemapIndex');
Route::get('sitemap/listings', 'SeoController@sitemapListings');
Route::get('sitemap/games', 'SeoController@sitemapGames');
Route::get('opensearch.xml', 'SeoController@openSearch')->name('opensearch');
Route::get('robots.txt', 'SeoController@robots')->name('robots');

// Post route for guest geo location
Route::post('geolocation/save', 'UserController@guestGeoLocation');

// Comment Routes
Route::group(['prefix' => 'comments'], function()
{
    Route::get('show/{type}/{type_id}', 'CommentController@show');
    Route::get('likes/{id}', 'CommentController@likes');
    Route::post('new', 'CommentController@post');
    Route::post('new/reply', 'CommentController@postReply');
    Route::post('like', 'CommentController@like');
    Route::get('delete/{id}/{page}', 'CommentController@delete');
});


Route::get('blog', 'PageController@blog')->name('blog');
Route::get('blog/{slug}', 'PageController@article')->name('article');

Route::group(['prefix' => 'messages'], function () {
    Route::get('/', ['as' => 'messages', 'uses' => 'MessagesController@index']);
    Route::get('create', ['as' => 'messages.create', 'uses' => 'MessagesController@create']);
    Route::post('/', ['as' => 'messages.store', 'uses' => 'MessagesController@store']);
    Route::get('{id}', ['as' => 'messages.show', 'uses' => 'MessagesController@show'])->middleware('contentlength');
    Route::post('{id}', ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
    Route::get('{id}/check', ['as' => 'messages.check', 'uses' => 'MessagesController@check']);
});


// CATCH-ALL ROUTE for PageManager
Route::get('page/{page}/{subs?}', ['uses' => 'PageController@index'])
    ->where(['page' => '^((?!admin).)*$', 'subs' => '.*'])->name('page');
