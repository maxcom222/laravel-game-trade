<?php

namespace App\Http\Middleware;

use Closure, Config;

/**
 * Class ThemeMiddleware
 * @package App\Http\Middleware
 */
class SettingsMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
	    // Show cookie consent if option is enabled in the admin dashboard
	     if (config('settings.cookie_consent')) {
	        Config::set('cookie-consent.enabled', true);
	     }


		 \Debugbar::enable();

  		return $next($request);
	}
}
