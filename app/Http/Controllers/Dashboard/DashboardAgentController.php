<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Referrals\Referrals;

use App\Models\DocManagement\Resources\ResourceItems;

class DashboardAgentController extends Controller
{
    public function dashboard_agent(Request $request) {

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
            'TransactionCoordinator_ID'
        ];

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
            'TransactionCoordinator_ID'
        ];

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
            'TransactionCoordinator_ID'
        ];

        $active_listings = Listings::select($listings_select)
            -> whereIn('Status', ResourceItems::GetActiveListingStatuses('yes', 'no', 'no'))
            -> orderBy('MlsListDate', 'desc')
            -> get();

        $active_contracts = Contracts::select($contracts_select)
            -> whereIn('Status', ResourceItems::GetActiveContractStatuses())
            -> orderBy('CloseDate', 'desc')
            -> get();

        $pending_referrals = Referrals::select($referrals_select)
            -> whereIn('Status', ResourceItems::GetActiveReferralStatuses())
            -> orderBy('CloseDate', 'desc')
            -> get();

        $contracts_closing_this_month = $active_contracts -> where('CloseDate', '<=', date('Y-m-t')) -> where('CloseDate', '>=', date('Y-m-1'));

        $contracts_past_settle_date = $active_contracts -> where('CloseDate', '>=', date('Y-m-d'));

        $expired_listings = Listings::select($listings_select)
            -> where('Status', ResourceItems::GetResourceID('Expired', 'listing_status'))
            -> get();


        return view('/dashboard/agent/dashboard', compact('active_listings', 'active_contracts', 'pending_referrals'));

    }
}
