<?php

namespace App\Http\Middleware;

use Closure;

class AddContentLength {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (!config('settings.minify_html')) {
            $response->header('Content-Length',strlen($response->getContent()));
        }
        return $response;
    }
}
