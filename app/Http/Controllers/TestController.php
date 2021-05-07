<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Tasks\Tasks;
use Illuminate\Http\Request;
use App\Models\Employees\Agents;
use App\Models\Calendar\Calendar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\BrightMLS\CompanyListings;
use App\Notifications\GlobalNotification;
use Illuminate\Support\Facades\Notification;


class TestController extends Controller
{
    public function test(Request $request) {

        //return view('tests/test');

        $agent_mls_id = '98082';
        $agent_id = Agents::where('bright_mls_id_md_dc_tp', $agent_mls_id)
            -> orWhere('bright_mls_id_va_tp', $agent_mls_id)
            -> orWhere('bright_mls_id_md_aap', $agent_mls_id)
            -> pluck('id');

        dd($agent_id[0]);

    }
}
