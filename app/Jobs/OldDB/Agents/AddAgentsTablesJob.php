<?php

namespace App\Jobs\OldDB\Agents;

use App\User;
use Illuminate\Bus\Queueable;
use App\Models\OldDB\OldAgents;
use App\Models\Employees\Agents;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AddAgentsTablesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $delete_agents = Agents::truncate();
        $delete_users = User::where('group', 'agent') -> orWhere('group', 'agent_referral') -> delete();

        $agents = OldAgents::where('email1', '!=', '') -> get();

        foreach ($agents as $agent) {

            $full_name = $agent -> first.' '.$agent -> last;
            if ($agent -> suffix != '') {
                $full_name .= ', '.$agent -> suffix;
            }

            $agent_email = $agent -> email1;
            $social_security = Crypt::encrypt($agent -> soc_sec);

            if(config('global.app_stage') == 'development') {

                $agent_email = 'test_'.$agent -> email1;
                $social_security = Crypt::encrypt('1111-22-333');
                if($agent -> email1 == 'mike@taylorprops.com') {
                    $agent_email = $agent -> email1;
                }
            }


            // add to emp_agents
            $add_agent = new Agents();
            $add_agent -> id = $agent -> id;
            $add_agent -> first_name = $agent -> first;
            $add_agent -> middle_name = $agent -> middle_name;
            $add_agent -> last_name = $agent -> last;
            $add_agent -> suffix = $agent -> suffix;
            $add_agent -> full_name = $full_name;
            $add_agent -> dob_day = $agent -> dob_day;
            $add_agent -> dob_month = $agent -> dob_month;
            $add_agent -> social_security = $social_security;
            $add_agent -> email = $agent_email;
            $add_agent -> cell_phone = $agent -> cell_phone;
            $add_agent -> home_phone = $agent -> home_phone;
            $add_agent -> address_street = $agent -> street;
            $add_agent -> address_city = $agent -> city;
            $add_agent -> address_state = $agent -> state;
            $add_agent -> address_zip = $agent -> zip;
            $add_agent -> address_county = $agent -> res_county;
            $add_agent -> company = $agent -> company;
            $add_agent -> active = $agent -> active;
            $add_agent -> start_date = $agent -> start_date;
            $commission_percent = $agent -> commission != 'none' ? str_replace('%', '', $agent -> commission) : '';
            $add_agent -> commission_percent = $commission_percent;
            $add_agent -> photo_location = $agent -> picURL;
            $add_agent -> bright_mls_id_md_dc_tp = $agent -> mris_id_tp_md;
            $add_agent -> bright_mls_id_va_tp = $agent -> mris_id_tp_va;
            $add_agent -> bright_mls_id_md_aap = $agent -> mris_id_tp_va;
            $add_agent -> llc_name = $agent -> llc_name;
            $add_agent -> owe_other = $agent -> owe_other;
            $add_agent -> owe_other_notes = $agent -> owe_other_notes;
            $add_agent -> commission_plan = $agent -> commission_plan;
            $add_agent -> bill_cycle = $agent -> bill_cycle;
            $add_agent -> bill_amount = $agent -> bill_amount;
            $add_agent -> admin_fee = $agent -> admin_fee;
            $add_agent -> admin_fee_rentals = $agent -> admin_fee_rentals;
            $add_agent -> balance = $agent -> balance;
            $add_agent -> balance_eno = $agent -> balance_eno;
            $add_agent -> balance_rent = $agent -> balance_rent;
            $add_agent -> auto_bill = $agent -> auto_bill;
            $add_agent -> ein = $agent -> ein;
            $add_agent -> team_id = $agent -> team_id;

            $add_agent -> save();

            $add_user = new User();
            $add_user -> user_id = $agent -> id;
            $add_user -> group = 'agent';
            if (stristr($agent -> company, 'referral')) {
                $add_user -> group = 'agent_referral';
            }
            $add_user -> active = $agent -> active;
            $add_user -> name = $agent -> fullname;
            $add_user -> first_name = $agent -> first;
            $add_user -> last_name = $agent -> last;
            $add_user -> email = $agent_email;
            //$add_user -> password = '$2y$10$P.O4F.rVfRRin81HksyCie0Wf0TEJQ9KlPYFoI2dMEzdtPFYD11FC';
            $add_user -> save();

        }

    }
}
