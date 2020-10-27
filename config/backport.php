<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backport name
    |--------------------------------------------------------------------------
    |
    | This value is the name of backport, This setting is displayed on the
    | login page.
    |
    */
    'name' => 'Backport',

    /*
    |--------------------------------------------------------------------------
    | Backport logo
    |--------------------------------------------------------------------------
    |
    | The logo of all admin pages. You can also set it as an image by using a
    | `img` tag, eg '<img src="http://logo-url" alt="Admin logo">'. Leave
    | blank to use default Backport logo.
    |
    */
    'logo' => '<img alt="Logo" src="/img/backend/logo.png">',

    /*
    |--------------------------------------------------------------------------
    | Backport mini logo
    |--------------------------------------------------------------------------
    |
    | The logo of all admin pages when the sidebar menu is collapsed. You can
    | also set it as an image by using a `img` tag, eg
    | '<img src="http://logo-url" alt="Admin logo">'. Leave blank to use
    | default Backport logo.
    |
    */
    'logo-mini' => '',

    /*
    |--------------------------------------------------------------------------
    | Backport route settings
    |--------------------------------------------------------------------------
    |
    | The routing configuration of the admin page, including the path prefix,
    | the controller namespace, and the default middleware. If you want to
    | access through the root path, just set the prefix to empty string.
    |
    */
    'route' => [

        'prefix' => env('BACKPORT_ROUTE_PREFIX', 'admin'),

        'namespace' => 'App\\Backport\\Controllers',

        'middleware' => ['web', 'admin'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Backport settings configuration
    |--------------------------------------------------------------------------
    |
    | The settings configuration including the prefix for all settings. You can
    | access all settings within the application with config('prefix.key').
    |
    */
    'setting' => [

        'prefix' => 'settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Backport install directory
    |--------------------------------------------------------------------------
    |
    | The installation directory of the controller and routing configuration
    | files of the administration page. The default is `app/Backport`, which
    | must be set before running `artisan backport::install` to take effect.
    |
    */
    'directory' => app_path('Backport'),

    /*
    |--------------------------------------------------------------------------
    | Backport html title
    |--------------------------------------------------------------------------
    |
    | Html title for all pages.
    |
    */
    'title' => 'Backport',

    /*
    |--------------------------------------------------------------------------
    | Access via `https`
    |--------------------------------------------------------------------------
    |
    | If your page is going to be accessed via https, set it to `true`.
    |
    */
    'https' => env('BACKPORT_HTTPS', false),

    /*
    |--------------------------------------------------------------------------
    | Backport auth setting
    |--------------------------------------------------------------------------
    |
    | Authentication settings for all admin pages. Include an authentication
    | guard and a user provider setting of authentication driver.
    |
    | You can specify a controller for `login` `logout` and other auth routes.
    |
    */
    'auth' => [

        'controller' => Wiledia\Backport\Controllers\AuthController::class,

        'guards' => [
            'backport' => [
                'driver'   => 'session',
                'provider' => 'backport',
            ],
        ],

        'providers' => [
            'backport' => [
                'driver' => 'eloquent',
                'model'  => App\Models\User::class,
            ],
        ],

        // Add "remember me" to login form
        'remember' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Backport upload setting
    |--------------------------------------------------------------------------
    |
    | File system configuration for form upload files and images, including
    | disk and upload path.
    |
    */
    'upload' => [

        // Disk in `config/filesystem.php`.
        'disk' => 'local',

        // Image and file upload path under the disk above.
        'directory' => [
            'image' => 'images',
            'file'  => 'files',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Backport database settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for backport builtin model & tables.
    |
    */
    'database' => [

        // Database connection for following tables.
        'connection' => '',

        // User tables and model.
        'users_table' => 'users',
        'users_model' => App\Models\User::class,

        // Role table and model.
        'roles_table' => 'roles',
        'roles_model' => Wiledia\Backport\Auth\Database\Role::class,

        // Permission table and model.
        'permissions_table' => 'permissions',
        'permissions_model' => Wiledia\Backport\Auth\Database\Permission::class,

        // Menu table and model.
        'menu_table' => 'backport_menu',
        'menu_model' => Wiledia\Backport\Auth\Database\Menu::class,

        // Pivot table for table above.
        'operation_log_table'    => 'backport_operation_log',
        'user_permissions_table' => 'permission_users',
        'role_users_table'       => 'role_users',
        'role_permissions_table' => 'permission_roles',
        'role_menu_table'        => 'backport_role_menu',
        'settings_table'         => 'settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | User operation log setting
    |--------------------------------------------------------------------------
    |
    | By setting this option to open or close operation log in backport.
    |
    */
    'operation_log' => [

        'enable' => true,

        /*
         * Only logging allowed methods in the list
         */
         'allowed_methods' => ['POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'],

        /*
         * Routes that will not log to database.
         *
         * All method to path like: admin/auth/logs
         * or specific method to path like: get:admin/auth/logs.
         */
        'except' => [
            'admin/logs*',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin map field provider
    |--------------------------------------------------------------------------
    |
    | Supported: "tencent", "google", "yandex".
    |
    */
    'map_provider' => 'google',

    /*
    |--------------------------------------------------------------------------
    | Additional CSS
    |--------------------------------------------------------------------------
    |
    | Add additional CSS files.
    |
    */
    'additional_css' => [
        'css/backport-custom.css'
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional JS
    |--------------------------------------------------------------------------
    |
    | Add additional JS files.
    |
    */
    'additional_js' => [
        'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js'
    ],

    /*
    |--------------------------------------------------------------------------
    | Show version at footer
    |--------------------------------------------------------------------------
    |
    | Whether to display the version number of backport at the footer of
    | each page
    |
    */
    'show_version' => true,

    /*
    |--------------------------------------------------------------------------
    | Show environment at footer
    |--------------------------------------------------------------------------
    |
    | Whether to display the environment at the footer of each page
    |
    */
    'show_environment' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu bind to permission
    |--------------------------------------------------------------------------
    |
    | whether enable menu bind to a permission
    */
    'menu_bind_permission' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable default breadcrumb
    |--------------------------------------------------------------------------
    |
    | Whether enable default breadcrumb for every page content.
    */
    'enable_default_breadcrumb' => true,

    /*
    |--------------------------------------------------------------------------
    | Extension Directory
    |--------------------------------------------------------------------------
    |
    | When you use command `php artisan  backport:extend` to generate
    | extensions, the extension files will be generated in this directory.
    */
    'extension_dir' => app_path('Backport/Extensions'),

    /*
    |--------------------------------------------------------------------------
    | Settings for extensions.
    |--------------------------------------------------------------------------
    |
    | You can find all available extensions here
    | https://github.com/backport-extensions.
    |
    */
    'extensions' => [

    ],
];
