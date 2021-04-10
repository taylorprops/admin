<?php

namespace App\Http\Controllers\DocManagement\Commission;

use App\Http\Controllers\Controller;
use App\Models\Commission\Commission;
use App\Models\Commission\CommissionChecksIn;
use App\Models\Commission\CommissionChecksInQueue;
use App\Models\Employees\Agents;
use App\Models\Employees\AgentsNotes;
use App\Models\Employees\AgentsTeams;
use App\Models\Resources\LocationData;
use Illuminate\Http\Request;

class CommissionController extends Controller
{

    public function commission(Request $request) {

        // pending - where checks in and checks out = 0 - where checks in more than checks out
        $agents = Agents::select('id', 'first_name', 'last_name', 'full_name', 'llc_name') -> where('active', 'yes') -> orderBy('last_name') -> get();
        $states = LocationData::AllStates();
        $type = 'sale';

        return view('/doc_management/commission/commission', compact('agents', 'states', 'type'));
    }

    public function get_commissions_pending(Request $request) {

		$select = ['id', 'commission_type', 'Agent_ID', 'Contract_ID', 'Referral_ID', 'close_date', 'earnest_deposit_amount', 'checks_in_total', 'checks_out_total', 'total_left'];

        $commission_contracts = Commission::select($select)
            -> where(function($query) {
                $query -> where(function ($query) {
                    $query -> where('total_left', '!=', '0')
                    -> where('checks_in_total', '>', '0');
                })
                -> orWhereHas('breakdown', function ($query) {
                    $query -> where('submitted', 'yes');
                });
            })
            -> where('Contract_ID', '>', '0')
            -> with(['property_contract:Contract_ID,FullStreetAddress,City,StateOrProvince,PostalCode,created_at'])
            -> with(['agent:id,first_name,last_name,full_name'])
            -> with(['breakdown'])
            -> get();


        $commission_referrals = Commission::select($select)
            -> where(function($query) {
                $query -> where(function ($query) {
                    $query -> where('total_left', '!=', '0')
                    -> where('checks_in_total', '>', '0');
                })
                -> orWhereHas('breakdown', function ($query) {
                    $query -> where('submitted', 'yes');
                });
            })
            -> where('Referral_ID', '>', '0')
            -> with(['property_referral:Referral_ID,FullStreetAddress,City,StateOrProvince,PostalCode,ClientFirstName,ClientLastName,created_at'])
            -> with(['agent:id,first_name,last_name,full_name'])
            -> get();


        /* $commission_contracts = Commission::select($select)
            -> where(function ($query) {
                $query -> where('total_left', '>', '0')
                -> orWhere('total_left', '<', '0');
            })
            -> where('Contract_ID', '>', '0')
            -> with('property_contract:Contract_ID,FullStreetAddress,City,StateOrProvince,PostalCode,created_at')
            -> with('agent:id,first_name,last_name,full_name')
            -> get();

        $commission_referrals = Commission::select($select)
            -> where('total_left', '>', '0')
            -> where('Referral_ID', '>', '0')
            -> with('property_referral:Referral_ID,FullStreetAddress,City,StateOrProvince,PostalCode,ClientFirstName,ClientLastName,created_at')
            -> with('agent:id,first_name,last_name,full_name')
            -> get(); */

        $commissions = $commission_contracts -> merge($commission_referrals);

        return view('/doc_management/commission/get_commissions_pending_html', compact('commissions'));
    }

    public function get_checks_queue(Request $request) {

		$checks = CommissionChecksInQueue::whereNull('Commission_ID')
            -> where('exported', 'no')
            -> with('agent:id,first_name,last_name,full_name')
            -> orderBy('created_at', 'DESC')
            -> get();

        $checks_other = Commission::where('commission_type', 'other')
            -> where(function ($q) {
                $q -> whereNull('total_left')
                -> orWhere('total_left', '>', '0');
            })
            -> with('agent:id,first_name,last_name,full_name')
            -> with('other_checks')
            -> orderBy('created_at', 'DESC')
            -> get();

        return view('/doc_management/commission/get_checks_html', compact('checks', 'checks_other'));
    }

    public function search_deleted_checks(Request $request) {

		$val = $request -> val;

        $select_cols = ['id', 'agent_name', 'date_received', 'check_date', 'check_number', 'check_amount', 'client_name', 'street', 'city', 'state', 'zip', 'file_location'];
        $checks_in = CommissionChecksIn::select($select_cols) -> where('check_type', 'other')
            -> where('active', 'no')
            -> whereNull('Agent_ID')
            -> where('street', 'like', '%'.$val.'%')
            -> get();
        $checks_in_queue = CommissionChecksInQueue::select($select_cols)
            -> where('active', 'no')
            -> where('exported', 'no')
            -> where(function ($q) use ($val) {
                $q -> where('agent_name', 'like', '%'.$val.'%') -> orWhere('street', 'like', '%'.$val.'%');
            })
            -> get();

        foreach ($checks_in as $check_in) {
            $check_in -> type = 'other';
        }
        foreach ($checks_in_queue as $check_in_queue) {
            $check_in_queue -> type = 'sale';
        }

        return compact('checks_in', 'checks_in_queue');
    }

    public function commission_other(Request $request, $Commission_ID) {
        $commission = Commission::find($Commission_ID);
        $Agent_ID = $commission['Agent_ID'];
        $agents = Agents::select('id', 'first_name', 'last_name', 'full_name', 'llc_name') -> where('active', 'yes') -> orderBy('last_name') -> get();
        $states = LocationData::AllStates();
        $type = 'other';

        return view('/doc_management/commission/commission_other', compact('Commission_ID', 'Agent_ID', 'agents', 'states', 'type'));
    }

    public function commission_other_details(Request $request) {

		$Commission_ID = $request -> Commission_ID;
        $commission = Commission::find($Commission_ID);

        $commission_percentages = Agents::select('commission_percent') -> groupBy('commission_percent') -> pluck('commission_percent');
        $teams = new AgentsTeams();
        $agent_notes = AgentsNotes::where('Agent_ID', $commission -> Agent_ID) -> get();
        $agents = Agents::select('id', 'first_name', 'last_name', 'full_name', 'llc_name') -> where('active', 'yes') -> orderBy('last_name') -> get();
        $states = LocationData::AllStates();

        $agent_details = Agents::find($commission -> Agent_ID);
        $type = 'other';

        $property = [];

        return view('/agents/doc_management/transactions/details/data/get_commission', compact('Commission_ID', 'commission', 'commission_percentages', 'teams', 'agent_notes', 'agents', 'states', 'agent_details', 'type', 'property'));
    }

    public function save_edit_queue_check(Request $request) {

		$check_id = $request -> edit_queue_check_id;

        if ($request -> edit_queue_check_type == 'sale') {
            $check = CommissionChecksInQueue::find($check_id);
        } else {
            $commission = Commission::find($request -> edit_queue_commission_id);
            $commission -> Agent_ID = $request -> edit_queue_check_agent_id;
            $commission -> other_street = $request -> edit_queue_check_street;
            $commission -> other_city = $request -> edit_queue_check_city;
            $commission -> other_state = $request -> edit_queue_check_state;
            $commission -> other_zip = $request -> edit_queue_check_zip;
            $commission -> other_client_name = $request -> edit_queue_check_client_name;
            $commission -> total_left = preg_replace('/[\$,]+/', '', $request -> edit_queue_check_amount);
            $commission -> save();

            $check = CommissionChecksIn::find($check_id);
            $check -> client_name = $request -> edit_queue_check_client_name;
        }

        $agent_name = '';
        if ($request -> edit_queue_check_agent_id) {
            $agent = Agents::find($request -> edit_queue_check_agent_id);
            $agent_name = $agent -> full_name;
        }

        $check -> check_date = $request -> edit_queue_check_date;
        $check -> check_amount = preg_replace('/[\$,]+/', '', $request -> edit_queue_check_amount);
        $check -> check_number = $request -> edit_queue_check_number;
        $check -> date_received = $request -> edit_queue_check_date_received;
        $check -> date_deposited = $request -> edit_queue_check_date_deposited;
        $check -> Agent_ID = $request -> edit_queue_check_agent_id;
        $check -> agent_name = $agent_name;
        $check -> street = $request -> edit_queue_check_street;
        $check -> city = $request -> edit_queue_check_city;
        $check -> state = $request -> edit_queue_check_state;
        $check -> zip = $request -> edit_queue_check_zip;
        $check -> save();

        return response() -> json(['success' => true]);
    }
}
