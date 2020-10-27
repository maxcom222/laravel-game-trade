<?php

namespace App\Http\Middleware;

use Closure, Theme;

/**
 * Class ThemeMiddleware
 * @package App\Http\Middleware
 */
class ThemeMiddleware
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

      // Check if user have changed the theme and theme selector is enabled
      if (session()->has('theme') && config('settings.theme_selector')) {
          // Set theme choosed by user
          Theme::set(session()->get('theme'));
      } else {
          // Set default theme set by admin
          if (config('settings.default_theme')) {
              Theme::set(config('settings.default_theme'));
          } else {
              Theme::set('default');
          }
      }

      // Set global theme layout
      Theme::setLayout('frontend.layouts.app');

  		return $next($request);
	}
}
