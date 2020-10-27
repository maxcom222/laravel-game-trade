<?php

use Illuminate\Database\Seeder;

class BackportMenuTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('backport_menu')->delete();

        \DB::table('backport_menu')->insert(array (
            0 =>
            array (
                'id' => 1,
                'parent_id' => 0,
                'order' => 1,
                'title' => 'System',
                'icon' => '',
                'uri' => NULL,
                'permission' => 'access_backend',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Dashboard',
                'icon' => 'fa-tachometer-alt',
                'uri' => '/',
                'permission' => 'access_backend',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'parent_id' => 0,
                'order' => 3,
                'title' => 'Settings',
                'icon' => 'fa-cog',
                'uri' => 'settings',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 =>
            array (
                'id' => 4,
                'parent_id' => 3,
                'order' => 4,
                'title' => 'General',
                'icon' => '',
                'uri' => 'settings/general',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 =>
            array (
                'id' => 5,
                'parent_id' => 3,
                'order' => 5,
                'title' => 'Design',
                'icon' => '',
                'uri' => 'settings/design',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 =>
            array (
                'id' => 6,
                'parent_id' => 3,
                'order' => 6,
                'title' => 'Theme',
                'icon' => '',
                'uri' => 'settings/theme',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 =>
            array (
                'id' => 7,
                'parent_id' => 3,
                'order' => 7,
                'title' => 'Localization',
                'icon' => '',
                'uri' => 'settings/localization',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 =>
            array (
                'id' => 8,
                'parent_id' => 3,
                'order' => 8,
                'title' => 'Countries',
                'icon' => '',
                'uri' => 'settings/countries',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 =>
            array (
                'id' => 9,
                'parent_id' => 3,
                'order' => 9,
                'title' => 'Listing',
                'icon' => '',
                'uri' => 'settings/listing',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 =>
            array (
                'id' => 10,
                'parent_id' => 3,
                'order' => 10,
                'title' => 'Game',
                'icon' => '',
                'uri' => 'settings/game',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 =>
            array (
                'id' => 11,
                'parent_id' => 3,
                'order' => 11,
                'title' => 'Authentication',
                'icon' => '',
                'uri' => 'settings/auth',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 =>
            array (
                'id' => 12,
                'parent_id' => 3,
                'order' => 12,
                'title' => 'Ads',
                'icon' => '',
                'uri' => 'settings/ads',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 =>
            array (
                'id' => 13,
                'parent_id' => 3,
                'order' => 13,
                'title' => 'Payment',
                'icon' => '',
                'uri' => 'settings/payment',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            13 =>
            array (
                'id' => 14,
                'parent_id' => 3,
                'order' => 14,
                'title' => 'Comment',
                'icon' => '',
                'uri' => 'settings/comment',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            14 =>
            array (
                'id' => 15,
                'parent_id' => 3,
                'order' => 15,
                'title' => 'Notification',
                'icon' => '',
                'uri' => 'settings/notification',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            15 =>
            array (
                'id' => 16,
                'parent_id' => 3,
                'order' => 16,
                'title' => 'Legal & Privacy',
                'icon' => '',
                'uri' => 'settings/legal',
                'permission' => 'edit_settings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            16 =>
            array (
                'id' => 17,
                'parent_id' => 0,
                'order' => 17,
                'title' => 'Translations',
                'icon' => 'fa-globe-europe',
                'uri' => 'language',
                'permission' => 'edit_translations',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            17 =>
            array (
                'id' => 18,
                'parent_id' => 17,
                'order' => 18,
                'title' => 'Languages',
                'icon' => '',
                'uri' => 'language',
                'permission' => 'edit_translations',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            18 =>
            array (
                'id' => 19,
                'parent_id' => 17,
                'order' => 19,
                'title' => 'Site Texts',
                'icon' => '',
                'uri' => 'translation/texts',
                'permission' => 'edit_translations',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            19 =>
            array (
                'id' => 20,
                'parent_id' => 0,
                'order' => 20,
                'title' => 'Logs',
                'icon' => 'fa-history',
                'uri' => 'logs',
                'permission' => 'access_logs',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            20 =>
            array (
                'id' => 21,
                'parent_id' => 20,
                'order' => 21,
                'title' => 'Operation',
                'icon' => '',
                'uri' => 'logs/operation',
                'permission' => 'access_logs',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            21 =>
            array (
                'id' => 22,
                'parent_id' => 20,
                'order' => 22,
                'title' => 'Laravel',
                'icon' => '',
                'uri' => 'logs/laravel',
                'permission' => 'access_logs',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            22 =>
            array (
                'id' => 23,
                'parent_id' => 0,
                'order' => 23,
                'title' => 'Content',
                'icon' => '',
                'uri' => NULL,
                'permission' => 'access_backend',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            23 =>
            array (
                'id' => 24,
                'parent_id' => 0,
                'order' => 24,
                'title' => 'Users & Permissions',
                'icon' => 'fa-users',
                'uri' => 'users',
                'permission' => 'edit_users',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            24 =>
            array (
                'id' => 25,
                'parent_id' => 24,
                'order' => 25,
                'title' => 'Users',
                'icon' => '',
                'uri' => 'users',
                'permission' => 'edit_users',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            25 =>
            array (
                'id' => 26,
                'parent_id' => 24,
                'order' => 26,
                'title' => 'Roles',
                'icon' => '',
                'uri' => 'roles',
                'permission' => 'edit_users',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            26 =>
            array (
                'id' => 27,
                'parent_id' => 24,
                'order' => 27,
                'title' => 'Permissions',
                'icon' => '',
                'uri' => 'permissions',
                'permission' => 'edit_users',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            27 =>
            array (
                'id' => 28,
                'parent_id' => 0,
                'order' => 28,
                'title' => 'Games & Genres',
                'icon' => 'fa-gamepad',
                'uri' => 'games',
                'permission' => 'edit_games',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            28 =>
            array (
                'id' => 29,
                'parent_id' => 28,
                'order' => 29,
                'title' => 'Games',
                'icon' => '',
                'uri' => 'games',
                'permission' => 'edit_games',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            29 =>
            array (
                'id' => 30,
                'parent_id' => 28,
                'order' => 30,
                'title' => 'Genres',
                'icon' => '',
                'uri' => 'genres',
                'permission' => 'edit_games',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            30 =>
            array (
                'id' => 31,
                'parent_id' => 0,
                'order' => 31,
                'title' => 'Platforms',
                'icon' => 'fa-boxes',
                'uri' => 'platforms',
                'permission' => 'edit_platforms',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            31 =>
            array (
                'id' => 32,
                'parent_id' => 31,
                'order' => 32,
                'title' => 'Platforms',
                'icon' => '',
                'uri' => 'platforms',
                'permission' => 'edit_platforms',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            32 =>
            array (
                'id' => 33,
                'parent_id' => 31,
                'order' => 33,
                'title' => 'Digital',
                'icon' => '',
                'uri' => 'digital',
                'permission' => 'edit_platforms',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            33 =>
            array (
                'id' => 34,
                'parent_id' => 0,
                'order' => 34,
                'title' => 'Listings',
                'icon' => 'fa-tags',
                'uri' => 'listings',
                'permission' => 'edit_listings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            34 =>
            array (
                'id' => 35,
                'parent_id' => 0,
                'order' => 35,
                'title' => 'Offers',
                'icon' => 'fa-briefcase',
                'uri' => 'offers',
                'permission' => 'edit_offers',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            35 =>
            array (
                'id' => 36,
                'parent_id' => 0,
                'order' => 36,
                'title' => 'Reports',
                'icon' => 'fa-life-ring',
                'uri' => 'reports',
                'permission' => 'edit_offers',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            36 =>
            array (
                'id' => 37,
                'parent_id' => 0,
                'order' => 37,
                'title' => 'Payments',
                'icon' => 'fa-money-bill',
                'uri' => 'payments',
                'permission' => 'edit_payments',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            37 =>
            array (
                'id' => 38,
                'parent_id' => 37,
                'order' => 38,
                'title' => 'Payments',
                'icon' => '',
                'uri' => 'payments',
                'permission' => 'edit_payments',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            38 =>
            array (
                'id' => 39,
                'parent_id' => 37,
                'order' => 39,
                'title' => 'Transactions',
                'icon' => '',
                'uri' => 'transactions',
                'permission' => 'edit_payments',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            39 =>
            array (
                'id' => 40,
                'parent_id' => 37,
                'order' => 40,
                'title' => 'Withdrawal',
                'icon' => '',
                'uri' => 'withdrawals',
                'permission' => 'edit_payments',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            40 =>
            array (
                'id' => 41,
                'parent_id' => 0,
                'order' => 41,
                'title' => 'Ratings',
                'icon' => 'fa-thumbs-up',
                'uri' => 'ratings',
                'permission' => 'edit_ratings',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            41 =>
            array (
                'id' => 42,
                'parent_id' => 0,
                'order' => 42,
                'title' => 'Blog',
                'icon' => 'fa-newspaper',
                'uri' => 'blog',
                'permission' => 'edit_articles',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            42 =>
            array (
                'id' => 43,
                'parent_id' => 42,
                'order' => 43,
                'title' => 'Articles',
                'icon' => '',
                'uri' => 'articles',
                'permission' => 'edit_articles',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            43 =>
            array (
                'id' => 44,
                'parent_id' => 42,
                'order' => 44,
                'title' => 'Categories',
                'icon' => '',
                'uri' => 'categories',
                'permission' => 'edit_articles',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            44 =>
            array (
                'id' => 45,
                'parent_id' => 42,
                'order' => 45,
                'title' => 'Tags',
                'icon' => '',
                'uri' => 'tags',
                'permission' => 'edit_articles',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            45 =>
            array (
                'id' => 46,
                'parent_id' => 0,
                'order' => 46,
                'title' => 'Comments',
                'icon' => 'fa-comments',
                'uri' => 'comments',
                'permission' => 'edit_comments',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            46 =>
            array (
                'id' => 47,
                'parent_id' => 0,
                'order' => 47,
                'title' => 'Pages',
                'icon' => 'fa-file',
                'uri' => 'pages',
                'permission' => 'edit_pages',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            47 =>
            array (
                'id' => 48,
                'parent_id' => 0,
                'order' => 48,
                'title' => 'Footer Links',
                'icon' => 'fa-list',
                'uri' => 'menu-item',
                'permission' => 'edit_pages',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));


    }
}
