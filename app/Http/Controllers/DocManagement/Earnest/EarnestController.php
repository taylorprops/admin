<?php

namespace App\Http\Controllers\DocManagement\Earnest;

use App\Http\Controllers\Controller;
use App\Mail\DefaultEmail;
use App\Models\DocManagement\Earnest\Earnest;
use App\Models\DocManagement\Earnest\EarnestChecks;
use App\Models\DocManagement\Earnest\EarnestNotes;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\Employees\Agents;
use App\Models\OldDB\OldEarnest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EarnestController extends Controller
{
    public function active_earnest(Request $request)
    {
        $earnest_accounts = ResourceItems::where('resource_type', 'earnest_accounts')
            ->orderBy('resource_order')
            ->get();

        return view('/doc_management/earnest/active_earnest', compact('earnest_accounts'));
    }

    public function get_earnest_deposits(Request $request)
    {
        $account_id = $request->account_id;
        $tab = $request->tab;

        $contracts_select = [
            'Agent_ID',
            'City',
            'CloseDate',
            'Contract_ID',
            'ContractDate',
            'ContractPrice',
            'FullStreetAddress',
            'PostalCode',
            'SaleRent',
            'StateOrProvince',
            'Status',
            'TransactionCoordinator_ID',
        ];

        // active and expired
        $active_status_ids = [
            ResourceItems::GetResourceID('Active', 'contract_status'),
            ResourceItems::GetResourceID('Expired', 'contract_status'),
        ];

        if ($tab == 'active' || $tab == 'missing') {
            $contracts = Contracts::select($contracts_select)
                ->where('EarnestHeldBy', 'us')
                ->whereIn('Status', $active_status_ids)
                ->with('status:resource_id,resource_name');
        } elseif ($tab == 'waiting') {

            // waiting for release
            $waiting_status_ids = [
                ResourceItems::GetResourceID('Waiting For Release', 'contract_status'),
            ];

            $contracts = Contracts::select($contracts_select)
                ->whereIn('Status', $waiting_status_ids);
        }

        $contracts = $contracts->with('agent:id,full_name')
            ->whereHas('earnest', function (Builder $query) use ($account_id, $tab) {
                if ($account_id != 'all') {
                    $query->where('earnest_account_id', $account_id);
                }
                if ($tab == 'active') {
                    $query->where('amount_received', '>', '0');
                } elseif ($tab == 'missing') {
                    $query->where('amount_received', '0.00');
                }
            })
            ->get();

        return view('/doc_management/earnest/get_earnest_html', compact('contracts', 'tab'));
    }

    public function balance_earnest(Request $request)
    {
        return view('/doc_management/earnest/balance_earnest');
    }

    public function get_earnest_totals(Request $request)
    {

        // get totals for all accounts
        $accounts = ResourceItems::where('resource_type', 'earnest_accounts')->orderBy('resource_order')->get();

        $earnest_old = OldEarnest::EarnestBalances();

        $earnest_account_totals = [];
        foreach ($accounts as $account) {
            $account_total = Earnest::where('earnest_account_id', $account->resource_id)->where('amount_total', '>', 0)->sum('amount_total');

            $company = stristr($account->resource_name, 'taylor') ? 'TP' : 'AAP';
            $account_total += $earnest_old[$company.'_'.$account->resource_state];

            $earnest_account_totals[] = [
                'id' => $account->resource_id,
                'total' => $account_total,
                'account_number' => $account->resource_account_number,
                'state' => $account->resource_state,
                'company' => $account->resource_name,
            ];
        }

        return view('/doc_management/earnest/get_earnest_totals_html', compact('earnest_account_totals'));
    }

    public function get_earnest_checks(Request $request)
    {
        $accounts = ResourceItems::where('resource_type', 'earnest_accounts')->with('earnest')->orderBy('resource_order')->get();

        return view('/doc_management/earnest/get_earnest_checks_html', compact('accounts'));
    }

    public function search_earnest_checks(Request $request)
    {
        $value = $request->value;

        $agent_ids = Agents::where('first_name', 'like', '%'.$value.'%')
            ->orWhere('last_name', 'like', '%'.$value.'%')
            ->orWhere('full_name', 'like', '%'.$value.'%')
            ->pluck('id');

        $contract_ids = Contracts::where('FullStreetAddress', 'like', '%'.$value.'%')
            ->pluck('Contract_ID');

        $checks = EarnestChecks::where('active', 'yes')
            ->where(function ($query) use ($contract_ids, $agent_ids, $value) {
                $query->whereIn('Contract_ID', $contract_ids)
                    ->orWhereIn('Agent_ID', $agent_ids)
                    ->orWhere('check_number', 'like', '%'.$value.'%')
                    ->orWhere('check_name', 'like', '%'.$value.'%')
                    ->orWhere('check_amount', 'like', '%'.$value.'%')
                    ->orWhere('payable_to', 'like', '%'.$value.'%');
            })
            ->with('agent:full_name')->with('property:FullStreetAddress,City,StateOrProvince,PostalCode')->with('earnest')
            ->get();

        $checks_in = $checks->where('check_type', 'in');
        $checks_out = $checks->where('check_type', 'out');

        return view('/doc_management/earnest/get_search_results_html', compact('checks_in', 'checks_out'));
    }

    public function email_agents_missing_earnest(Request $request)
    {
        $contract_ids = explode(',', $request->contract_ids);

        $subject = $request->subject;
        $message = $request->message;

        $from_name = auth()->user()->name;
        $from_address = auth()->user()->email;

        $email['from'] = ['address' => $from_address, 'name' => $from_name];

        $select = [
            'Agent_ID',
            'Contract_ID',
            'FullStreetAddress',
            'City',
            'StateOrProvince',
            'PostalCode',
        ];
        $contracts = Contracts::select($select)
            ->whereIn('Contract_ID', $contract_ids)
            ->with('agent:id,first_name,full_name,email', 'earnest')
            ->get();

        foreach ($contracts as $contract) {
            $agent = $contract->agent;
            $to_name = $agent->full_name;
            $to_address = $agent->email;
            $property_address = $contract->FullStreetAddress.' '.$contract->City.', '.$contract->StateOrProvince.' '.$contract->PostalCode;
            $subject = preg_replace('/%%PropertyAddress%%/', $property_address, $subject);
            $message = preg_replace('/%%PropertyAddress%%/', $property_address, $message);
            $message = preg_replace('/%%FirstName%%/', $agent->first_name, $message);

            $email['subject'] = $subject;
            $email['message'] = $message;

            $to_name = $agent->full_name;
            $to_address = $agent->email;

            $new_mail = new DefaultEmail($email);

            //return ($new_mail) -> render();

            Mail::to([['name' => $to_name, 'email' => $to_address]])
                ->queue($new_mail);

            // update earnest last emailed date
            $earnest = $contract->earnest;
            $earnest->last_emailed_date = date('Y-m-d');
            $earnest->save();
        }

        return response()->json(['status' => 'success']);
    }
}
