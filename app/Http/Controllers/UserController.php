<?php

namespace App\Http\Controllers;

use App\Models\Employees\Agents;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct() {
        $this -> middleware('auth');
    }

    /* public function dashboard(Request $request) {

		if (Auth::check()) {

            if (auth() -> user() -> group == 'admin') {

                return view('/dashboard/admin/dashboard');

            } elseif (auth() -> user() -> group == 'agent') {

                $agent_details = Agents::whereId(auth() -> user() -> user_id) -> first();
                $request -> session() -> put('agent_details', $agent_details);

                $request -> session() -> put('logo_src', '/images/emails/AAP-flat-white.png');

                if (stristr($agent_details -> company, 'Taylor')) {
                    $request -> session() -> put('logo_src', '/images/emails/TP-flat-white.png');
                }

                $data = $request -> session() -> all();

                return view('/dashboard/agent/dashboard', compact('data'));

            }

        }

        return redirect('/');
    }

    public function dashboard_agent(Request $request) {} */
}
