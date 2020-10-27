<?php

use Illuminate\Routing\Router;

Backport::registerLogRoutes();

Route::group([
    'prefix'        => config('backport.route.prefix'),
    'namespace'     => config('backport.route.namespace'),
    'middleware'    => config('backport.route.middleware'),
], function (Router $router) {

    // Dashboard Routes
    $router->get('/', 'DashboardController@index');
    $router->get('/update/check', 'DashboardController@checkUpdate')->name('check.update');

    // User Routes
    $router->resource('users', UserController::class)->except([
        'create', 'store', 'destroy', 'show'
    ]);
    $router->get('users/{user_id}/ban', '\App\Http\Controllers\UserController@ban');

    // Role Routes
    $router->resource('roles', RoleController::class)->except([
        'show'
    ]);

    // Permission Routes
    $router->resource('permissions', PermissionController::class)->except([
        'show'
    ]);

    // Game Routes
    $router->resource('games', GameController::class)->except([
        'show'
    ]);

    // Genre Routes
    $router->resource('genres', GenreController::class)->except([
        'show'
    ]);

    // Platform Routes
    $router->resource('platforms', PlatformController::class)->except([
        'show'
    ]);

    // Digital Ditributor Routes
    $router->resource('digital', DigitalController::class)->except([
        'show'
    ]);

    // Listing Routes
    $router->resource('listings', ListingController::class)->except([
        'create', 'store', 'show', 'edit', 'update'
    ]);

    // Offer Routes
    $router->resource('offers', OfferController::class)->only([
        'index'
    ]);

    // Report Roues
    $router->resource('reports', ReportController::class)->only([
        'index'
    ]);

    // Payment Routes
    $router->resource('payments', PaymentController::class)->only([
        'index'
    ]);

    // Transaction Routes
    $router->resource('transactions', TransactionController::class)->only([
        'index'
    ]);

    // Withdrawal Routes
    $router->resource('withdrawals', WithdrawalController::class)->only([
        'index', 'update'
    ]);

    // Rating Routes
    $router->resource('ratings', UserRatingController::class)->only([
        'index', 'update'
    ]);

    // Article Routes
    $router->resource('articles', ArticleController::class)->except([
        'show'
    ]);

    // Category Routes
    $router->resource('categories', CategoryController::class)->except([
        'show'
    ]);

    // Tag Routes
    $router->resource('tags', TagController::class)->except([
        'show'
    ]);

    // Comment Routes
    $router->resource('comments', CommentController::class)->only([
        'index'
    ]);

    // Page Routes
    $router->resource('pages', PageController::class)->except([
        'show'
    ]);

    // Menu Item Routes
    $router->resource('menu-item', MenuItemController::class)->except([
        'show'
    ]);

    // Setting Routes
    $router->resource('settings/countries', Settings\CountriesController::class);

    $router->get('settings/general', 'Settings\GeneralController@index');
    $router->post('settings/general', 'Settings\GeneralController@update');

    $router->get('settings/design', 'Settings\DesignController@index');
    $router->post('settings/design', 'Settings\DesignController@update');

    $router->get('settings/localization', 'Settings\LocalizationController@index');
    $router->post('settings/localization', 'Settings\LocalizationController@update');

    $router->get('settings/listing', 'Settings\ListingController@index');
    $router->post('settings/listing', 'Settings\ListingController@update');

    $router->get('settings/game', 'Settings\GameController@index');
    $router->post('settings/game', 'Settings\GameController@update');

    $router->get('settings/auth', 'Settings\AuthController@index');
    $router->post('settings/auth', 'Settings\AuthController@update');

    $router->get('settings/ads', 'Settings\AdsController@index');
    $router->post('settings/ads', 'Settings\AdsController@update');

    $router->get('settings/payment', 'Settings\PaymentController@index');
    $router->post('settings/payment', 'Settings\PaymentController@update');

    $router->get('settings/comment', 'Settings\CommentController@index');
    $router->post('settings/comment', 'Settings\CommentController@update');

    $router->get('settings/notification', 'Settings\NotificationController@index');
    $router->post('settings/notification', 'Settings\NotificationController@update');

    $router->get('settings/legal', 'Settings\LegalController@index');
    $router->post('settings/legal', 'Settings\LegalController@update');

    $router->get('settings/theme', 'Settings\ThemeController@index');
    $router->get('settings/theme/{slug}', 'Settings\ThemeController@update');

    // Translation Routes
    $router->get('translation/texts/{lang?}/{file?}', 'Translations\TextController@index')->name('lang.index');
    $router->post('translation/texts/{lang}/{file}', 'Translations\TextController@update');

    // Language Routes
    $router->resource('language', LanguageController::class);





});
