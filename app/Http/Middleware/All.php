<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class All
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth() -> user()) {
            return $next($request);
        }
        $redirect_url = '/dashboard';
        return redirect($redirect_url) -> with('error', 'You do not have access');
    }

}
