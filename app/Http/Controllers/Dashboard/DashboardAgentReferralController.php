<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DocManagement\Resources\ResourceItems;

class DashboardAgentReferralController extends Controller
{

    public function dashboard_agent_referral(Request $request) {

        $Agent_ID = auth() -> user() -> user_id;

        $resource_items = new ResourceItems();


        return view('/dashboard/agent/dashboard', compact('Agent_ID', 'resource_items'));
    }

}
