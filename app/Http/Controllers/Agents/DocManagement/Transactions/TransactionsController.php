<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Yajra\Datatables\Facades\Datatables;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Listings\Listings;

use App\Models\DocManagement\Transactions\Contracts\Contracts;

use App\Models\DocManagement\Transactions\Referrals\Referrals;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;

class TransactionsController extends Controller
{

    public function transactions_all(Request $request) {

        $agent_referral = null;
        if(stristr(auth() -> user() -> group, 'referral')) {
            $agent_referral = 'yes';
        }
        return view('/agents/doc_management/transactions/transactions_all', compact('agent_referral'));
    }

    public function get_transactions(Request $request) {

        $type = $request -> type;
        $status = $request -> status;

        $select_listings = [
            'City',
            'Contract_ID',
            'ExpirationDate',
            'FullStreetAddress',
            'ListAgentFullName',
            'Listing_ID',
            'ListPictureURL',
            'MlsListDate',
            'PostalCode',
            'SellerOneFullName',
            'SellerTwoFullName',
            'StateOrProvince',
            'Status'
        ];

        $select_contracts = [
            'BuyerAgentFullName',
            'BuyerOneFullName',
            'BuyerTwoFullName',
            'City',
            'CloseDate',
            'ContractDate',
            'Contract_ID',
            'FullStreetAddress',
            'ListAgentFullName',
            'Listing_ID',
            'ListPictureURL',
            'PostalCode',
            'StateOrProvince',
            'Status'
        ];

        $select_referrals = [
            'Agent_ID',
            'City',
            'ClientFirstName',
            'ClientLastName',
            'CloseDate',
            'FullStreetAddress',
            'PostalCode',
            'ReceivingAgentFirstName',
            'ReceivingAgentLastName',
            'Referral_ID',
            'StateOrProvince',
            'Status'
        ];

        $active_listing_statuses = ResourceItems::GetActiveListingStatuses('yes', 'yes', 'no') -> toArray();
        $closed_status_listing = ResourceItems::GetResourceID('Closed', 'listing_status');
        $active_contract_statuses = ResourceItems::GetActiveContractStatuses() -> toArray();
        $closed_status_contract = ResourceItems::GetResourceID('Closed', 'contract_status');
        $active_referral_statuses = ResourceItems::GetActiveReferralStatuses() -> toArray();
        $closed_status_referral = ResourceItems::GetResourceID('Closed', 'referral_status');


        if($type == 'listings') {
            $transactions = Listings::select($select_listings) -> with('contract:Contract_ID,CloseDate');
            if($status == 'active') {
                $transactions = $transactions -> whereIn('Status', $active_listing_statuses);
            } else if($status == 'closed') {
                $transactions = $transactions -> where('Status', $closed_status_listing);
            }
        } else if($type == 'contracts') {
            $transactions = Contracts::select($select_contracts) -> with('listing:Listing_ID,SellerOneFullName,SellerTwoFullName');
            if($status == 'active') {
                $transactions = $transactions -> whereIn('Status', $active_contract_statuses);
            } else if($status == 'closed') {
                $transactions = $transactions -> where('Status', $closed_status_contract);
            }
        } else if($type == 'referrals') {
            $transactions = Referrals::select($select_referrals) -> with('agent:id,full_name');
            if($status == 'active') {
                $transactions = $transactions -> whereIn('Status', $active_referral_statuses);
            } else if($status == 'closed') {
                $transactions = $transactions -> where('Status', $closed_status_referral);
            }
        }
        $transactions = $transactions -> with('status') -> with('checklist') -> orderBy('Status') -> get();

        $checklist_items_modal = new TransactionChecklistItems();

        $contract_closed_status = ResourceItems::GetResourceId('Closed', 'contract_status');


        return view('/agents/doc_management/transactions/get_'.$type.'_html', compact('transactions', 'checklist_items_modal', 'contract_closed_status'));

    }


}
