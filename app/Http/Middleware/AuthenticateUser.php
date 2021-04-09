<?php

namespace App\Http\Middleware;

use Closure;

class AuthenticateUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    /*  NOT USED */
    public function handle($request, Closure $next)
    {
        if (basename(request() -> path()) != 'login') {
            if (auth() -> user() == null) {
                return redirect() -> route('login');
            }
        }

        return $next($request);
    }
}
