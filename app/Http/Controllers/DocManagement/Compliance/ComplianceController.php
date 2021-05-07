<?php

namespace App\Http\Controllers\DocManagement\Compliance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BrightMLS\CompanyListings;

class ComplianceController extends Controller
{

    public function missing_listings(Request $request) {

        $listings_select = [
            'City',
            'FullStreetAddress',
            'ListingId',
            'ListingKey',
            'MLSListDate',
            'PostalCode',
            'StateOrProvince'
        ];

        $listings = CompanyListings::select($listings_select)
        -> whereIn('ListOfficeMlsId', config('bright_office_codes'))
        -> where('added_to_transactions', 'no')
        -> with(['agent:id,full_name,email,cell_phone'])
        -> get();

        return view('doc_management/compliance/missing_listings', compact('listings'));

    }

    public function missing_contracts(Request $request) {

        $contracts_select = [
            'City',
            'CloseDate',
            'ListingContractDate',
            'FullStreetAddress',
            'ListPictureURL',
            'ListingId',
            'ListingKey',
            'PostalCode',
            'StateOrProvince'
        ];

        $contracts = CompanyListings::select($contracts_select)
        -> whereIn('BuyerOfficeMlsId', config('bright_office_codes'))
        -> where('added_to_transactions', 'no')
        -> with(['agent:id,full_name,email,cell_phone'])
        -> get();

        return view('doc_management/compliance/missing_contracts', compact('contracts'));

    }

}
