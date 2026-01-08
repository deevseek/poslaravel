<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CentralDomain
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->getHost() !== 'profesionalservis.my.id') {
            abort(403);
        }

        return $next($request);
    }
}
