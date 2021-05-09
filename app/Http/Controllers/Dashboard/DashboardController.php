<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Tasks\Tasks;
use Illuminate\Http\Request;
use App\Models\Calendar\Calendar;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Commission\Commission;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DocManagement\Earnest\Earnest;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;

class DashboardController extends Controller
{
    public function dashboard(Request $request) {

        if(!auth() -> user()) {
            return redirect('/login');
        }

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

        // alerts
        $alert_type = 'missing-docs-listings';
        $title = 'Missing Documents - Listings';
        $details = 'The listings below have required checklist items that you have not submitted yet.';
        $missing_docs_listings = Listings::select($listings_select)
            -> addSelect(DB::raw('"listing" as transaction_type, "'.$alert_type.'" as alert_type, "'.$title.'" as title, "'.$details.'" as details'))
            -> where('DocsMissingCount', '>', '0')
            -> whereIn('Status', $active_listing_statuses)
            -> with(['agent:id,full_name'])
            -> orderBy('MlsListDate', 'desc')
            -> get();

        $alert_type = 'expired-listings';
        $title = 'Expired Listings';
        $details = 'The listings below are past their expiration date. They need to be withdrawn or extended.';
        $expired_listings = Listings::select($listings_select)
            -> addSelect(DB::raw('"listing" as transaction_type, "'.$alert_type.'" as alert_type, "'.$title.'" as title, "'.$details.'" as details'))
            -> where('Status', $expired_listing_status)
            -> with(['agent:id,full_name'])
            -> orderBy('ExpirationDate', 'desc')
            -> get();



        // CONTRACTS
        $active_contract_statuses = ResourceItems::GetActiveContractStatuses();

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
            -> with(['agent:id,full_name'])
            -> orderBy('CloseDate', 'desc')
            -> get();

        $alert_type = 'contracts-past-settle-date';
        $title = 'Contracts Past Settle Date';
        $details = 'The contracts below are still active yet past their settlement date. If the contract just closed and you have submitted all required closing docs the contract will be automatically removed once processed.';
        $contracts_past_settle_date = Contracts::select($contracts_select)
            -> addSelect(DB::raw('"contract" as transaction_type, "'.$alert_type.'" as alert_type, "'.$title.'" as title, "'.$details.'" as details'))
            -> where('CloseDate', '<', date('Y-m-d'))
            -> whereIn('Status', $active_contract_statuses)
            -> with(['agent:id,full_name'])
            -> orderBy('CloseDate', 'desc')
            -> get();

        $alert_type = 'missing-docs-contracts';
        $title = 'Missing Documents - Contracts';
        $details = 'The contracts below have required checklist items that you have not submitted yet.';
        $missing_docs_contracts = Contracts::select($contracts_select)
            -> addSelect(DB::raw('"contract" as transaction_type, "'.$alert_type.'" as alert_type, "'.$title.'" as title, "'.$details.'" as details'))
            -> where('DocsMissingCount', '>', '0')
            -> whereIn('Status', $active_contract_statuses)
            -> with(['agent:id,full_name'])
            -> orderBy('CloseDate', 'desc')
            -> get();


        // REFERRALS

        // alerts
        $alert_type = 'missing-docs-referrals';
        $title = 'Missing Documents - Referrals';
        $details = 'The referrals below have required checklist items that you have not submitted yet.';
        $missing_docs_referrals = Referrals::select($referrals_select)
            -> addSelect(DB::raw('"referral" as transaction_type, "'.$alert_type.'" as alert_type, "'.$title.'" as title, "'.$details.'" as details'))
            -> where('DocsMissingCount', '>', '0')
            -> whereIn('Status', ResourceItems::GetActiveReferralStatuses())
            -> with(['agent:id,full_name'])
            -> get();

        // merge all alerts
        $alerts = collect();
        $alerts = $alerts -> merge($missing_earnest) -> merge($missing_docs_listings) -> merge($expired_listings) -> merge($contracts_past_settle_date) -> merge($missing_docs_contracts) -> merge($missing_docs_referrals);

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

        $notifications = auth() -> user() -> unreadNotifications;

        return view('dashboard/dashboard', compact('alert_types', 'alerts', 'contracts_past_settle_date', 'missing_docs_listings', 'missing_docs_contracts', 'missing_docs_referrals', 'expired_listings', 'missing_earnest', 'show_alerts', 'notifications'));

    }

    public function get_transactions(Request $request) {

        $active_listing_statuses = ResourceItems::GetActiveListingStatuses('yes', 'yes', 'no');
        $expired_listing_status = ResourceItems::GetResourceID('Expired', 'listing_status');

        $active_listings_count = Listings::whereIn('Status', $active_listing_statuses) // include 'Under Contract' and 'Expired'
            -> count();

        $active_contract_statuses = ResourceItems::GetActiveContractStatuses();

        $active_contracts_count = Contracts::whereIn('Status', $active_contract_statuses)
            -> count();

        $active_referrals_count = Referrals:: whereIn('Status', ResourceItems::GetActiveReferralStatuses())
            -> count();

        return view('dashboard/mods/get_transactions_html', compact('active_listings_count',  'active_contracts_count', 'active_referrals_count',));

    }


    public function get_upcoming_closings(Request $request) {

        $contracts_select = [
            'Agent_ID',
            'City',
            'CloseDate',
            'ContractDate',
            'Contract_ID',
            'DocsMissingCount',
            'EarnestHeldBy',
            'FullStreetAddress',
            'ListPictureURL',
            'PostalCode',
            'StateOrProvince'
        ];

        $active_contract_statuses = ResourceItems::GetActiveContractStatuses();

        $contracts = Contracts::select($contracts_select)
            // -> where('CloseDate', '<=', date('Y-m-t'))
            -> where('CloseDate', '>=', date('Y-m-d'))
            -> whereIn('Status', $active_contract_statuses)
            -> with(['agent:id,full_name'])
            -> orderBy('CloseDate', 'asc')
            -> get();

        return view('dashboard/mods/get_upcoming_closings_html', compact('contracts'));

    }

    public function get_commissions(Request $request) {

        $active_contract_statuses = ResourceItems::GetActiveAndClosedContractStatuses();

        $contracts_select = [
            'Agent_ID',
            'City',
            'CloseDate',
            'ContractDate',
            'Contract_ID',
            'DocsMissingCount',
            'FullStreetAddress',
            'PostalCode',
            'StateOrProvince'
        ];

        $contracts = Contracts::select($contracts_select)
            -> whereIn('Status', $active_contract_statuses)
            -> where('CloseDate', '<', date('Y-m-d', strtotime('+1 week')))
            -> where('CloseDate', '>', date('Y-m-d', strtotime('-2 month')))
            -> with(['commission:Contract_ID,total_commission_to_agent', 'commission_breakdown:Contract_ID,submitted,status,total_commission_to_agent'])
            -> orderBy('CloseDate', 'DESC')
            -> get();

        return view('dashboard/mods/get_commissions_html', compact('contracts'));

    }

    public function get_admin_todo(Request $request) {

        $select = ['id', 'commission_type', 'Agent_ID', 'Contract_ID', 'Referral_ID', 'close_date', 'total_left'];
        $pending_contracts = Commission::select($select)
            -> where(function ($query) {
                $query -> where('total_left', '>', '0')
                -> orWhere('total_left', '<', '0');
            })
            -> where('Contract_ID', '>', '0')
            -> count();

        $pending_referrals = Commission::select($select)
            -> where('total_left', '>', '0')
            -> where('Referral_ID', '>', '0')
            -> count();

        $pending_commissions_count = $pending_contracts + $pending_referrals;


        $pending_earnest_count = Earnest::with('checks')
        -> whereHas('checks', function (Builder $query) {
            $query -> where('active', 'yes')
                -> where('check_status', 'pending');
        })
        -> count();


        $released_status_id = ResourceItems::GetResourceID('Released', 'contract_status');
        $deposits_to_release_count = Contracts::select('ListingKey')
            -> where('EarnestHeldBy', 'us')
            -> where('Status', $released_status_id)
            -> with(['status:resource_id,resource_name'])
            -> whereHas('earnest', function (Builder $query) {
                $query -> where('amount_total', '>', '0');
            }) -> count();

        $listing_docs_to_review = TransactionChecklistItemsDocs::select('Listing_ID')
            -> where('doc_status', 'pending')
            -> where('Listing_ID', '>', '0')
            -> groupBy('Listing_ID')
            -> get();
        $listing_docs_to_review_count = count($listing_docs_to_review);

        $contract_docs_to_review = TransactionChecklistItemsDocs::select('Contract_ID', 'document_id')
            -> where('doc_status', 'pending')
            -> where('Contract_ID', '>', '0')
            -> where('is_release', 'no')
            -> groupBy('Contract_ID')
            -> get();
        $contract_docs_to_review_count = count($contract_docs_to_review);

        $referral_docs_to_review = TransactionChecklistItemsDocs::select('Referral_ID')
            -> where('doc_status', 'pending')
            -> where('Referral_ID', '>', '0')
            -> groupBy('Referral_ID')
            -> get();
        $referral_docs_to_review_count = count($referral_docs_to_review);

        $releases_to_review = TransactionChecklistItemsDocs::select('Contract_ID', 'document_id')
            -> where('doc_status', 'pending')
            -> where('Contract_ID', '>', '0')
            -> where('is_release', 'yes')
            -> groupBy('Contract_ID')
            -> get();
        $releases_to_review_count = count($releases_to_review);

        $docs_to_review_count = $listing_docs_to_review_count + $contract_docs_to_review_count + $referral_docs_to_review_count;

        return view('dashboard/mods/get_admin_todo_html', compact('pending_commissions_count', 'pending_earnest_count', 'deposits_to_release_count', 'docs_to_review_count', 'releases_to_review_count'));

    }


    public function get_upcoming_events(Request $request) {

        $tasks_select = [
            'id',
            'task_date as start_date',
            'task_time as start_time',
            'task_title as event_title',
            'reminder',
            'transaction_type',
            'Listing_ID',
            'Contract_ID'
        ];

        $tasks = Tasks::select($tasks_select)
        -> where('task_date', '>=', date('Y-m-d'))
        -> where('status', 'active')
        -> whereHas('members', function($query){
            $query -> where('user_id', auth() -> user() -> id);
        })
        -> get();

        $events_select = [
            'id',
            'start_date',
            'start_time',
            'event_title',
            'all_day',
        ];

        $events = Calendar::select($events_select)
        -> where('start_date', '>=', date('Y-m-d'))
        -> where('user_id', auth() -> user() -> id)
        -> get();

        foreach($tasks as $task) {
            if($task -> reminder == 0) {
                $task -> event_type = 'task';
            } else {
                $task -> event_type = 'reminder';
            }
        }

        foreach($events as $event) {
            if($event -> all_day == 1) {
                $event -> event_type = 'event';
            } else {
                $event -> event_type = 'reminder';
            }
        }

        $upcoming_events = $tasks -> merge($events) -> sortBy('start_date');

        return view('dashboard/mods/get_upcoming_events_html', compact('upcoming_events'));

    }

}
