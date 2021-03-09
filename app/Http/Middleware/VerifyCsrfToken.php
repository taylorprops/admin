<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/logout',
    ];

    private $openRoutes = [
        'esign_callback',
        'login',
    ];

    public function handle($request, Closure $next)
    {
        foreach ($this->openRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        return $next($request);
    }
}
