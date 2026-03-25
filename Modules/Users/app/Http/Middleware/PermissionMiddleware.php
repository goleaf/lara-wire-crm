<?php

namespace Modules\Users\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
