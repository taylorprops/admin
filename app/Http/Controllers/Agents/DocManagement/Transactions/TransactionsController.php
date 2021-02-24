<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Referrals\Referrals;

use Yajra\Datatables\Facades\Datatables;

use App\Models\DocManagement\Resources\ResourceItems;

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

        $select_listings = [
            'City',
            'Contract_ID',
            'ExpirationDate',
            'FullStreetAddress',
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
            'BuyerOneFullName',
            'BuyerTwoFullName',
            'City',
            'Contract_ID',
            'FullStreetAddress',
            'PostalCode',
            'StateOrProvince',
            'Status'
        ];

        $select_referrals = [
            'City',
            'FullStreetAddress',
            'PostalCode',
            'Referral_ID',
            'StateOrProvince',
            'Status'
        ];


        if($type == 'listings') {
            $transactions = Listings::select($select_listings) -> with('contract:Contract_ID,CloseDate');
        } else if($type == 'contracts') {
            $transactions = Contracts::select($select_contracts);
        } else if($type == 'referrals') {
            $transactions = Referrals::select($select_referrals);
        }
        $transactions = $transactions -> with('status') -> orderBy('Status') -> get();

        return view('/agents/doc_management/transactions/get_'.$type.'_html', compact('transactions'));

    }


}
