<?php

namespace App\Http\Middleware;

use Closure;

class Admin
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
        // return $next($request);
        //dd($request);
        if (auth() -> user()) {
            $group = auth() -> user() -> group;
            if ($group == 'admin') {
                return $next($request);
            }
            $redirect_url = '/test';

            return redirect($redirect_url) -> with('error', 'You do not have access');
        }

        return redirect('/') -> with('error', 'Session Has Expired');
        //echo '<script>top.location.href="/";</script>';
    }
}
