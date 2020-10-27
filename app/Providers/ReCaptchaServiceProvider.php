<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ReCaptchaServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $app = $this->app;

        $app['validator']->extend('captcha', function ($attribute, $value) use ($app) {
            return $app['captcha']->verifyResponse($value, $app['request']->getClientIp());
        });

        if ($app->bound('form')) {
            $app['form']->macro('captcha', function ($attributes = []) use ($app) {
                return $app['captcha']->display($attributes, $app->getLocale());
            });
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('captcha', function ($app) {
            return new \App\Http\Controllers\API\ReCaptcha(
                $app['config']['settings.recaptcha_secret'],
                $app['config']['settings.recaptcha_sitekey']
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['captcha'];
    }
}
