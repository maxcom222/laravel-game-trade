<?php
namespace App\Http\Middleware;

use Closure;
use Carbon;
use Cache;
use Illuminate\Support\Facades\Auth;

class LogLastUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(5);
            Cache::put('user-is-online-' . Auth::user()->id, true, $expiresAt);
            if (Auth::user()->last_activity_at < Carbon::now()) {
                Auth::user()->lastActivity($expiresAt);
            }
        }
        return $next($request);
    }
}
