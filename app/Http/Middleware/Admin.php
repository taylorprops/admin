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
            if ($group == 'agent') {
                $redirect_url = '/dashboard_agent';
            } else {
                // TODO: this might be an issue
                $redirect_url = '/dashboard_'.auth() -> user() -> group;
            }

            return redirect($redirect_url) -> with('error', 'You do not have access');
        }

        return redirect('/') -> with('error', 'Session Has Expired');
        //echo '<script>top.location.href="/";</script>';
    }
}
