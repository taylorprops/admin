<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
use App\Models\Employees\Agents;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class SearchController extends Controller
{
    public function search(Request $request) {

		$value = $request -> value;

        $agent_ids = [];

        // get agent ids from names that match search value
        // if no session group - abort
        if (Cookie::get('user_group')) {
            if (Cookie::get('user_group') == 'admin') {
                $agent_ids = Agents::where('full_name', 'like', '%'.$value.'%') -> pluck('id');
            }
        } else {
            abort(404);
        }

        $listings_select = [
            'Agent_ID',
            'City',
            'Contract_ID',
            'ExpirationDate',
            'FullStreetAddress',
            'Listing_ID',
            'ListPrice',
            'ListPictureURL',
            'MLSListDate',
            'PostalCode',
            'SaleRent',
            'StateOrProvince',
            'Status',
            'TransactionCoordinator_ID',
        ];
        $listings = Listings::select($listings_select)
            -> where('FullStreetAddress', 'like', '%'.$value.'%')
            -> orWhere(function ($query) use ($agent_ids) {
                if (count($agent_ids) > 0) {
                    $query -> whereIn('Agent_ID', $agent_ids);
                }
            })
            -> with(['status:resource_id,resource_name,resource_color', 'agent:id,full_name', 'transaction_coordinator'])
            -> orderBy('MLSListDate', 'DESC')
            -> get();

        $contracts_select = [
            'Agent_ID',
            'City',
            'CloseDate',
            'ContractPrice',
            'ContractDate',
            'EarnestHeldBy',
            'FullStreetAddress',
            'Contract_ID',
            'ListPictureURL',
            'PostalCode',
            'SaleRent',
            'StateOrProvince',
            'Status',
            'TransactionCoordinator_ID',
        ];
        $contracts = Contracts::select($contracts_select)
            -> where('FullStreetAddress', 'like', '%'.$value.'%')
            -> orWhere(function ($query) use ($agent_ids) {
                if (count($agent_ids) > 0) {
                    $query -> whereIn('Agent_ID', $agent_ids);
                }
            })
            -> with(['status:resource_id,resource_name,resource_color', 'agent:id,full_name', 'earnest:Contract_ID,amount_received,amount_total', 'transaction_coordinator'])
            -> orderBy('ContractDate', 'DESC')
            -> get();


        $referrals_select = [
            'Agent_ID',
            'City',
            'ClientFirstName',
            'ClientLastName',
            'CloseDate',
            'FullStreetAddress',
            'Referral_ID',
            'PostalCode',
            'StateOrProvince',
            'Status',
            'TransactionCoordinator_ID',
        ];
        $referrals = Referrals::select($referrals_select)
        -> where('FullStreetAddress', 'like', '%'.$value.'%')
        -> orWhere(function ($query) use ($agent_ids) {
            if (count($agent_ids) > 0) {
                $query -> whereIn('Agent_ID', $agent_ids);
            }
        })
        -> with(['status:resource_id,resource_name,resource_color','agent:id,full_name','transaction_coordinator'])
        -> orderBy('CloseDate', 'DESC')
        -> get();

        return view('/search/search_results_html', compact('listings', 'contracts', 'referrals'));
    }
}
