<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

/**
 * Class LocaleMiddleware
 * @package App\Http\Middleware
 */
class LocaleMiddleware
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

  		if (session()->has('locale') && config('settings.locale_selector')) {
  			/**
  			 * Set the Laravel locale
  			 */
  			app()->setLocale(session()->get('locale'));
  			/**
  			 * setLocale to use Carbon source locales. Enables diffForHumans() localized
  			 */
  			Carbon::setLocale(session()->get('locale'));
  		} else {
  			/**
  			 * Set the Laravel locale
  			 */
  			app()->setLocale(config('settings.default_locale'));
  			/**
  			 * setLocale to use Carbon source locales. Enables diffForHumans() localized
  			 */
  			Carbon::setLocale(config('settings.default_locale'));
  		}

  		return $next($request);
	}
}
