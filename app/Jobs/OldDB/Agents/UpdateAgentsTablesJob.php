<?php

namespace App\Jobs\OldDB\Agents;

use App\User;
use Illuminate\Bus\Queueable;
use App\Models\OldDB\OldAgents;
use App\Models\Employees\Agents;
use App\Models\Users\PasswordResets;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Queue\SerializesModels;
use App\Notifications\RegisterEmployee;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UpdateAgentsTablesJob implements ShouldQueue
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

    public function create_password_reset_url($user, $action) {

        $token = str_random(60);
        PasswordResets::where('email', $user -> email) -> delete();
        PasswordResets::insert([
            'email' => $user -> email,
            'token' => Hash::make($token),
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $user -> email,
            'action' => $action
        ], false));

        return $url;

    }

    public function handle()
    {

        $agents = OldAgents::where('email1', '!=', '') -> get();

        foreach ($agents as $agent) {

            $full_name = $agent -> first.' '.$agent -> last;
            if ($agent -> suffix != '') {
                $full_name .= ', '.$agent -> suffix;
            }

            $agent_email = $agent -> email1;
            $social_security = Crypt::encrypt($agent -> soc_sec);

            if(config('app.env') == 'local') {

                $agent_email = 'test_'.$agent -> email1;
                $social_security = Crypt::encrypt('1111-22-333');
                if($agent -> email1 == 'mike@taylorprops.com') {
                    $agent_email = $agent -> email1;
                }

            }


            // add to emp_agents
            $update_agent = Agents::with(['user_account']) -> find($agent -> id);
            if(!$update_agent) {
                $update_agent = new Agents();
                $update_agent -> id = $agent -> id;
            }
            $update_agent -> first_name = $agent -> first;
            $update_agent -> middle_name = $agent -> middle_name;
            $update_agent -> last_name = $agent -> last;
            $update_agent -> suffix = $agent -> suffix;
            $update_agent -> full_name = $full_name;
            $update_agent -> dob_day = $agent -> dob_day;
            $update_agent -> dob_month = $agent -> dob_month;
            $update_agent -> social_security = $social_security;
            $update_agent -> email = $agent_email;
            $update_agent -> cell_phone = $agent -> cell_phone;
            $update_agent -> home_phone = $agent -> home_phone;
            $update_agent -> address_street = $agent -> street;
            $update_agent -> address_city = $agent -> city;
            $update_agent -> address_state = $agent -> state;
            $update_agent -> address_zip = $agent -> zip;
            $update_agent -> address_county = $agent -> res_county;
            $update_agent -> company = $agent -> company;
            $update_agent -> active = $agent -> active;
            $update_agent -> start_date = $agent -> start_date;
            $commission_percent = $agent -> commission != 'none' ? str_replace('%', '', $agent -> commission) : '';
            $update_agent -> commission_percent = $commission_percent;
            $update_agent -> photo_location = $agent -> picURL;
            $update_agent -> bright_mls_id_md_dc_tp = $agent -> mris_id_tp_md;
            $update_agent -> bright_mls_id_va_tp = $agent -> mris_id_tp_va;
            $update_agent -> bright_mls_id_md_aap = $agent -> mris_id_tp_va;
            $update_agent -> llc_name = $agent -> llc_name;
            $update_agent -> owe_other = $agent -> owe_other;
            $update_agent -> owe_other_notes = $agent -> owe_other_notes;
            $update_agent -> commission_plan = $agent -> commission_plan;
            $update_agent -> bill_cycle = $agent -> bill_cycle;
            $update_agent -> bill_amount = $agent -> bill_amount;
            $update_agent -> admin_fee = $agent -> admin_fee;
            $update_agent -> admin_fee_rentals = $agent -> admin_fee_rentals;
            $update_agent -> balance = $agent -> balance;
            $update_agent -> balance_eno = $agent -> balance_eno;
            $update_agent -> balance_rent = $agent -> balance_rent;
            $update_agent -> auto_bill = $agent -> auto_bill;
            $update_agent -> ein = $agent -> ein;
            $update_agent -> team_id = $agent -> team_id;

            $update_agent -> save();

            $user = $update_agent -> user_account;

            $new_user = null;
            if(!$user) {
                $user = new User();
                $new_user = 'yes';
                $user -> user_id = $agent -> id;
                $user -> password = '$2y$10$P.O4F.rVfRRin81HksyCie0Wf0TEJQ9KlPYFoI2dMEzdtPFYD11FC';
            }


            $user -> group = 'agent';
            if (stristr($agent -> company, 'referral')) {
                $user -> group = 'agent_referral';
            }
            $user -> active = $agent -> active;
            $user -> name = $agent -> fullname;
            $user -> first_name = $agent -> first;
            $user -> last_name = $agent -> last;
            $user -> email = $agent_email;
            $user -> save();

            if(config('app.env') == 'production') {
                if($new_user) {
                    $url = $this -> create_password_reset_url($user, 'register');
                    Notification::send($user, new RegisterEmployee($url));
                }
            }

        }

    }
}
