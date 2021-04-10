<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use App\Models\Employees\Agents;
use App\Models\Employees\InHouse;
use App\Http\Controllers\Controller;
use App\Models\Employees\TransactionCoordinators;

class SessionVariables
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

        if(auth() -> user()) {

            $user_id = auth() -> user() -> user_id;

            session(['header_logo_src' => '/images/logo/logos.png']);
            session(['email_logo_src' => '/images/emails/TP-flat-white.png']);

            if (auth() -> user() -> group == 'admin') {

                $user_details = InHouse::whereId($user_id) -> first();

            } elseif (stristr(auth() -> user() -> group, 'agent')) {

                $user_details = Agents::whereId($user_id) -> first();

                if (stristr($user_details -> company, 'Anne')) {
                    session(['header_logo_src' => '/images/logo/logo_aap.png']);
                    session(['email_logo_src' => '/images/emails/AAP-flat-white.png']);
                }

            } elseif (stristr(auth() -> user() -> group, 'transaction_coordinator')) {

                $user_details = TransactionCoordinators::whereId($user_id) -> first();

            }

        session(['user_details' => $user_details]);

        }/*  else {

            if(!stristr($_SERVER['REQUEST_URI'], 'login') && !stristr($_SERVER['REQUEST_URI'], 'callback')) {
                return redirect('/login');
            }
        } */

        //session([]);

        return $next($request);

    }
}
