<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Cookie;
use Illuminate\Http\Request;
use App\Models\Employees\Agents;
use App\Models\Employees\InHouse;
use App\Http\Controllers\Controller;
use App\Models\Employees\TransactionCoordinators;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function redirectTo() {
        $super_user = false;

        if (auth() -> user() -> group == 'admin') {
            session(['header_logo_src' => '/images/logo/logos.png']);
            session(['email_logo_src' => '/images/emails/TP-flat-white.png']);

            $user_id = auth() -> user() -> user_id;

            // get admin details and add to session
            $admin_details = InHouse::whereId($user_id) -> first();
            session(['admin_details' => $admin_details]);

            if (auth() -> user() -> super_user == 'yes') {
                session(['super_user' => true]);
            }
        } elseif (stristr(auth() -> user() -> group, 'agent')) {
            $user_id = auth() -> user() -> user_id;

            // get agent details and add to session
            $agent_details = Agents::whereId($user_id) -> first();
            session(['agent_details', $agent_details]);

            // set logo for header logo and EMAILS by company and add to session
            session(['header_logo_src' => '/images/logo/logo_aap.png']);
            session(['email_logo_src' => '/images/emails/AAP-flat-white.png']);
            if (stristr($agent_details -> company, 'Taylor')) {
                session(['header_logo_src' => '/images/logo/logo_tp.png']);
                session(['email_logo_src' => '/images/emails/TP-flat-white.png']);
            }
        } elseif (stristr(auth() -> user() -> group, 'agent_referral')) {
        } elseif (stristr(auth() -> user() -> group, 'transaction_coordinator')) {

            session(['header_logo_src' => '/images/logo/logos.png']);
            session(['email_logo_src' => '/images/emails/TP-flat-white.png']);

            $user_id = auth() -> user() -> user_id;

            // get admin details and add to session
            $transaction_coordinator_details = TransactionCoordinators::whereId($user_id) -> first();
            session(['transaction_coordinator_details' => $transaction_coordinator_details]);

        }

        $path = parse_url($this -> previous_url, PHP_URL_PATH);

        // redirect to page requested or dashboard
        if ($this -> previous_url != '' && stristr($this -> previous_url, $_SERVER['HTTP_HOST']) && stristr($this -> previous_url, 'login') === false && $path != '/' && ! preg_match('/dashboard/', $path)) {
            $this -> redirectTo = $this -> previous_url;
        } else {
            if(auth() -> user() -> group == 'transaction_coordinator') {
                $this -> redirectTo = 'dashboard_agent';
            } else {
                $this -> redirectTo = 'dashboard_'.auth() -> user() -> group;
            }
        }

        $maxlifetime = ini_get('session.gc_maxlifetime');

        Cookie::queue(Cookie::make('user_group', auth() -> user() -> group, $maxlifetime, null, null, false, false));

        return $this -> redirectTo;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {

		$this -> previous_url = $request -> previous_url;
        $this -> middleware('guest') -> except(['logout', 'login']);
    }
}
