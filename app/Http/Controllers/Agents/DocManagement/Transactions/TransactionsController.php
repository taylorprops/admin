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


        if($type == 'listings') {
            $select = ['Listing_ID', 'FullStreetAddress', 'City', 'StateOrProvince', 'PostalCode', 'SellerOneFullName'];
            $transactions = Listings::select($select);
        } else if($type == 'contracts') {
            $select = ['Contract_ID', 'FullStreetAddress', 'City', 'StateOrProvince', 'PostalCode', 'BuyerOneFullName'];
            $transactions = Contracts::select($select);
        } else if($type == 'referrals') {
            $select = ['Referral_ID', 'FullStreetAddress', 'City', 'StateOrProvince', 'PostalCode'];
            $transactions = Referrals::select($select);
        }
        $transactions = $transactions -> orderBy('Status') -> get();

        return view('/agents/doc_management/transactions/get_'.$type.'_html', compact('transactions'));

    }


}
