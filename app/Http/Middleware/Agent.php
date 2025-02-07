<?php

namespace App\Http\Middleware;

use Closure;

class Agent
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
        if (auth() -> user()) {
            if (stristr(auth() -> user() -> group, 'agent') || auth() -> user() -> group == 'transaction_coordinator' || auth() -> user() -> group == 'admin') {
                return $next($request);
            }
        }

        //return abort(403);
        return redirect(url() -> previous()) -> with('error','You do not have access');
        //echo '<script>top.location.href="/";</script>';
    }
}
