<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

    public function credentials(Request $request)
    {
        return [
            'email'     => $request -> email,
            'password'  => $request -> password,
            'active' => 'yes'

        ];
    }

    public function redirectTo() {


        if (auth() -> user() -> super_user == 'yes') {
            session(['super_user' => true]);
        }

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

        /* $path = parse_url($this -> previous_url, PHP_URL_PATH);

        // redirect to page requested or dashboard
        if ($this -> previous_url != '' && stristr($this -> previous_url, $_SERVER['HTTP_HOST']) && stristr($this -> previous_url, 'login') === false && $path != '/' && ! preg_match('/dashboard/', $path)) {
            $this -> redirectTo = $this -> previous_url;
        } else {
            $this -> redirectTo = '/dashboard';
        } */

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
