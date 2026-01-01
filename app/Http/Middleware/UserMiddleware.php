<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sentinel;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Sentinel::check()) {
            return redirect('/login');
        }

        return $next($request);
    }
}
