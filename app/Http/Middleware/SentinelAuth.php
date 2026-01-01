<?php

namespace App\Http\Middleware;

use Closure;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class SentinelAuth
{
    public function handle($request, Closure $next)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        return $next($request);
    }
}
