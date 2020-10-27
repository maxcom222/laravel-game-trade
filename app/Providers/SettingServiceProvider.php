<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Meta tags
        config(['seotools.meta.defaults.description' => config('settings.meta_description')]);
        config(['seotools.opengraph.defaults.description' => config('settings.meta_description')]);
        // Open Graph
        config(['seotools.opengraph.defaults.title' => config('settings.page_name')]);
        config(['seotools.opengraph.defaults.site_name' => config('settings.page_name')]);
        // Twitter
        config(['seotools.twitter.defaults.card' => 'summary']);
        if (config('settings.twitter_link')) {
            config(['seotools.twitter.defaults.site' => '@' . basename(config('settings.twitter_link')) ]);
        }

        // Mail
        config(['mail.from.address' => config('settings.contact_email')]);
        config(['mail.from.name' => config('settings.page_name')]);

        // Set OneSignal API Keys
        if (config('settings.onesignal')) {
            config(['services.onesignal.app_id' => config('settings.onesignal_app_id')]);
            config(['services.onesignal.rest_api_key' => config('settings.onesignal_rest_api_key')]);
        }

        if (config('settings.ssl')) {
            // Start - force SSL
            \URL::forceScheme("https");
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
