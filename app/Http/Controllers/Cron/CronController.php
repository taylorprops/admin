<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsEmailed;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
use App\Models\Employees\Agents;
use App\Models\Employees\AgentsLicenses;
use App\Models\Employees\AgentsNotes;
use App\Models\Employees\AgentsTeams;
use App\Models\OldDB\OldAgents;
use App\Models\OldDB\OldAgentsLicenses;
use App\Models\OldDB\OldAgentsNotes;
use App\Models\OldDB\OldAgentsTeams;
use App\User;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CronController extends Controller
{
    public function update_tables_agents(Request $request)
    {
        $delete_agents = Agents::truncate();
        $delete_users = User::where('group', 'agent')->delete();

        $agents = OldAgents::where('active', 'yes')->get();

        foreach ($agents as $agent) {
            $full_name = $agent->first.' '.$agent->last;
            if ($agent->suffix != '') {
                $full_name .= ', '.$agent->suffix;
            }

            // add to emp_agents
            $add_agent = new Agents();
            $add_agent->id = $agent->id;
            $add_agent->first_name = $agent->first;
            $add_agent->middle_name = $agent->middle_name;
            $add_agent->last_name = $agent->last;
            $add_agent->suffix = $agent->suffix;
            $add_agent->full_name = $full_name;
            $add_agent->dob_day = $agent->dob_day;
            $add_agent->dob_month = $agent->dob_month;
            // TODO: remove fake number
            $add_agent->social_security = /* $agent -> soc_sec */ '111-22-3333';
            // TODO: remove test_
            $add_agent->email = 'test_'.$agent->email1;
            $add_agent->cell_phone = $agent->cell_phone;
            $add_agent->home_phone = $agent->home_phone;
            $add_agent->address_street = $agent->street;
            $add_agent->address_city = $agent->city;
            $add_agent->address_state = $agent->state;
            $add_agent->address_zip = $agent->zip;
            $add_agent->address_county = $agent->res_county;
            $add_agent->company = $agent->company;
            $add_agent->active = $agent->active;
            $add_agent->start_date = $agent->start_date;
            $commission_percent = $agent->commission != 'none' ? str_replace('%', '', $agent->commission) : '';
            $add_agent->commission_percent = $commission_percent;
            $add_agent->photo_location = $agent->picURL;
            $add_agent->bright_mls_id_md_dc_tp = $agent->mris_id_tp_md;
            $add_agent->bright_mls_id_va_tp = $agent->mris_id_tp_va;
            $add_agent->bright_mls_id_md_aap = $agent->mris_id_tp_va;
            $add_agent->llc_name = $agent->llc_name;
            $add_agent->owe_other = $agent->owe_other;
            $add_agent->owe_other_notes = $agent->owe_other_notes;
            $add_agent->commission_plan = $agent->commission_plan;
            $add_agent->bill_cycle = $agent->bill_cycle;
            $add_agent->bill_amount = $agent->bill_amount;
            $add_agent->admin_fee = $agent->admin_fee;
            $add_agent->admin_fee_rentals = $agent->admin_fee_rentals;
            $add_agent->balance = $agent->balance;
            $add_agent->balance_eno = $agent->balance_eno;
            $add_agent->balance_rent = $agent->balance_rent;
            $add_agent->auto_bill = $agent->auto_bill;
            $add_agent->ein = $agent->ein;
            $add_agent->team_id = $agent->team_id;

            $add_agent->save();

            $add_user = new User();
            $add_user->user_id = $agent->id;
            $add_user->group = 'agent';
            if (stristr($agent->company, 'referral')) {
                $add_user->group = 'agent_referral';
            }
            $add_user->name = $agent->fullname;
            // TODO: remove test_
            $add_user->email = 'test_'.$agent->email1;
            $add_user->password = '$2y$10$P.O4F.rVfRRin81HksyCie0Wf0TEJQ9KlPYFoI2dMEzdtPFYD11FC';
            $add_user->save();
        }
    }

    public function update_tables_other(Request $request)
    {
        $delete_agents_licenses = AgentsLicenses::truncate();
        $delete_agents_teams = AgentsTeams::truncate();
        $delete_agents_notes = AgentsNotes::truncate();

        $licenses = OldAgentsLicenses::where('active', 'yes')->get();
        $teams = OldAgentsTeams::get();
        $notes = OldAgentsNotes::where('deleted', 'no')->get();

        foreach ($licenses as $license) {
            $add_license = new AgentsLicenses();
            $add_license->Agent_ID = $license->agent_id;
            $add_license->state = $license->lic_state;
            $add_license->number = $license->lic_number;
            $add_license->expiration = $license->lic_expire;
            $add_license->company = $license->lic_comp;
            $add_license->file_location = $license->lic_location;
            $add_license->save();
        }

        foreach ($teams as $team) {
            $add_team = new AgentsTeams();
            $add_team->team_name = $team->team_name;
            $add_team->team_leader_id = $team->team_leader;
            $add_team->active = $team->active;
            $add_team->save();
        }

        foreach ($notes as $note) {
            $add_note = new AgentsNotes();
            $add_note->Agent_ID = $note->agent_id;
            $add_note->agent_name = $note->agent_name;
            $add_note->notes = $note->notes;
            $add_note->created_by = $note->creator;
            $add_note->created_at = $note->date_added;
            $add_note->save();
        }
    }
}
