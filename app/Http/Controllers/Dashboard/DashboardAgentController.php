<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardAgentController extends Controller
{
    public function dashboard_agent(Request $request)
    {
        $listings_select = [
            'Agent_ID',
            'City',
            'CloseDate',
            'DocsMissingCount',
            'ExpirationDate',
            'FullStreetAddress',
            'MLSListDate',
            'PostalCode',
            'StateOrProvince',
            'Listing_ID as id',
        ];

        $contracts_select = [
            'Agent_ID',
            'City',
            'CloseDate',
            'ContractDate',
            'DocsMissingCount',
            'EarnestHeldBy',
            'FullStreetAddress',
            'ListPictureURL',
            'PostalCode',
            'StateOrProvince',
            'Contract_ID as id',
        ];

        $referrals_select = [
            'Agent_ID',
            'City',
            'ClientFirstName',
            'ClientLastName',
            'CloseDate',
            'DocsMissingCount',
            'FullStreetAddress',
            'PostalCode',
            'StateOrProvince',
            'Referral_ID as id',
        ];

        // LISTINGS
        $active_listing_statuses = ResourceItems::GetActiveListingStatuses('yes', 'yes', 'no');
        $expired_listing_status = ResourceItems::GetResourceID('Expired', 'listing_status');

        $active_listings_count = Listings::whereIn('Status', $active_listing_statuses) // include 'Under Contract' and 'Expired'
            -> count();


        // alerts
        $alert_type = 'missing-docs-listings';
        $title = 'Missing Documents - Listings';
        $details = 'The listings below have required checklist items that you have not submitted yet.';
        $missing_docs_listings = Listings::select($listings_select)
            -> addSelect(DB::raw('"listing" as transaction_type, "'.$alert_type.'" as alert_type, "'.$title.'" as title, "'.$details.'" as details'))
            -> where('DocsMissingCount', '>', '0')
            -> whereIn('Status', $active_listing_statuses)
            -> orderBy('MlsListDate', 'desc')
            -> get();

        $alert_type = 'expired-listings';
        $title = 'Expired Listings';
        $details = 'The listings below are past their expiration date. They need to be withdrawn or extended.';
        $expired_listings = Listings::select($listings_select)
            -> addSelect(DB::raw('"listing" as transaction_type, "'.$alert_type.'" as alert_type, "'.$title.'" as title, "'.$details.'" as details'))
            -> where('Status', $expired_listing_status)
            -> orderBy('ExpirationDate', 'desc')
            -> get();



        // CONTRACTS
        $active_contract_statuses = ResourceItems::GetActiveContractStatuses();

        $active_contracts_count = Contracts::whereIn('Status', $active_contract_statuses)
            -> count();

        // alerts
        $alert_type = 'missing-earnest';
        $title = 'Missing Earnest Deposits';
        $details = 'Our records indicate that we are holding the earnest deposit for the properties below but we have not received a deposit yet.<br><br><div class=\'text-danger\'><i class=\'fa fa-exclamation-circle mr-2\'></i> Contact the office immediately about the contracts listed below.</div>';
        $missing_earnest = Contracts::select($contracts_select)
            -> addSelect(DB::raw('"contract" as transaction_type, "'.$alert_type.'" as alert_type, "'.$title.'" as title, "'.$details.'" as details'))
            -> where('EarnestHeldBy', 'us')
            -> whereHas('earnest', function (Builder $query) {
                $query -> where('amount_received', '0.00');
            })
            -> whereIn('Status', $active_contract_statuses)
            -> orderBy('CloseDate', 'desc')
            -> get();

        $alert_type = 'contracts-past-settle-date';
        $title = 'Contracts Past Settle Date';
        $details = 'The contracts below are still active yet past their settlement date. If the contract just closed and you have submitted all required closing docs the contract will be automatically removed once processed.';
        $contracts_past_settle_date = Contracts::select($contracts_select)
            -> addSelect(DB::raw('"contract" as transaction_type, "'.$alert_type.'" as alert_type, "'.$title.'" as title, "'.$details.'" as details'))
            -> where('CloseDate', '<', date('Y-m-d'))
            -> whereIn('Status', $active_contract_statuses)
            -> orderBy('CloseDate', 'desc')
            -> get();

        $alert_type = 'missing-docs-contracts';
        $title = 'Missing Documents - Contracts';
        $details = 'The contracts below have required checklist items that you have not submitted yet.';
        $missing_docs_contracts = Contracts::select($contracts_select)
            -> addSelect(DB::raw('"contract" as transaction_type, "'.$alert_type.'" as alert_type, "'.$title.'" as title, "'.$details.'" as details'))
            -> where('DocsMissingCount', '>', '0')
            -> whereIn('Status', $active_contract_statuses)
            -> orderBy('CloseDate', 'desc')
            -> get();

        // non alerts
        $contracts_closing_this_month = Contracts::select($contracts_select)
            -> where('CloseDate', '<=', date('Y-m-t'))
            -> where('CloseDate', '>=', date('Y-m-d'))
            -> whereIn('Status', $active_contract_statuses)
            -> orderBy('CloseDate', 'asc')
            -> get();

        // REFERRALS
        $active_referrals_count = Referrals:: whereIn('Status', ResourceItems::GetActiveReferralStatuses())
            -> count();

        // alerts
        $alert_type = 'missing-docs-referrals';
        $title = 'Referrals Missing Documents';
        $details = 'The referrals below have required checklist items that you have not submitted yet.';
        $missing_docs_referrals = Referrals::select($referrals_select)
            -> addSelect(DB::raw('"referral" as transaction_type, "'.$alert_type.'" as alert_type, "'.$title.'" as title, "'.$details.'" as details'))
            -> where('DocsMissingCount', '>', '0')
            -> whereIn('Status', ResourceItems::GetActiveReferralStatuses())
            -> get();

        // merge all alerts
        $alerts = collect();
        $alerts = $alerts -> merge($missing_docs_listings) -> merge($expired_listings) -> merge($missing_earnest) -> merge($contracts_past_settle_date) -> merge($missing_docs_contracts) -> merge($missing_docs_referrals);

        // commission checks status
        // unread messages

        $show_alerts = null;
        $alert_types = [];
        if (
            count($contracts_past_settle_date) > 0 ||
            count($missing_docs_listings) > 0 ||
            count($missing_docs_contracts) > 0 ||
            count($missing_docs_referrals) > 0 ||
            count($expired_listings) > 0 ||
            count($missing_earnest) > 0
        ) {
            $show_alerts = 'yes';
            foreach ($alerts as $alert) {
                $alert_types[] = $alert -> alert_type;
            }
            $alert_types = array_unique($alert_types);
        }

        return view('/dashboard/agent/dashboard', compact('active_listings_count', 'alert_types', 'alerts', 'active_contracts_count', 'active_referrals_count', 'contracts_closing_this_month', 'contracts_past_settle_date', 'missing_docs_listings', 'missing_docs_contracts', 'missing_docs_referrals', 'expired_listings', 'missing_earnest', 'show_alerts'));
    }
}
