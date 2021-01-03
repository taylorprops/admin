<?php

namespace App\Http\Controllers\DocManagement\Earnest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DocManagement\Earnest\Earnest;
use App\Models\DocManagement\Earnest\EarnestChecks;

use App\Models\DocManagement\Resources\ResourceItems;

class BalanceEarnestController extends Controller
{
    public function balance_earnest(Request $request) {

        return view('/doc_management/earnest/balance_earnest');

    }

    public function get_earnest_totals(Request $request) {

        // get totals for all accounts
        $accounts = ResourceItems::where('resource_type', 'earnest_accounts') -> orderBy('resource_order') -> get();

        $earnest_account_totals = [];
        foreach($accounts as $account) {

            $account_total = Earnest::where('earnest_account_id', $account -> resource_id) -> where('amount_total', '>', 0) -> sum('amount_total');

            $earnest_account_totals[] = [
                'id' => $account -> resource_id,
                'total' => $account_total,
                'account_number' => $account -> resource_account_number,
                'state' => $account -> resource_state,
                'company' => $account -> resource_name
            ];

        }

        return view('/doc_management/earnest/get_earnest_totals_html', compact('earnest_account_totals'));

    }

    public function get_earnest_checks(Request $request) {

        $accounts = ResourceItems::where('resource_type', 'earnest_accounts') -> with('earnest') -> orderBy('resource_order') -> get();

        return view('/doc_management/earnest/get_earnest_checks_html', compact('accounts'));

    }

}
