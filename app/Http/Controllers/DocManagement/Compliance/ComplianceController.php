<?php

namespace App\Http\Controllers\DocManagement\Compliance;

use App\Mail\DefaultEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\BrightMLS\CompanyListings;
use App\Models\BrightMLS\CompanyListingsNotes;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;

class ComplianceController extends Controller
{

    public function missing_transactions(Request $request) {

        return view('doc_management/compliance/missing_transactions');

    }

    public function get_missing_listings(Request $request) {

        $listings_select = [
            'Agent_ID',
            'City',
            'FullStreetAddress',
            'last_emailed_date',
            'ListAgentEmail',
            'ListAgentFirstName',
            'ListAgentLastName',
            'ListAgentPreferredPhone',
            'ListingId',
            'ListingKey',
            'MLSListDate',
            'MlsStatus',
            'PostalCode',
            'StateOrProvince'
        ];

        $ListingIds = Listings::select('ListingId')
        -> whereNotNull('ListingId')
        -> where('ListingId', '!=', '')
        -> where('MLSListDate', '>', date('Y-m-d', strtotime('-3 month')))
        -> pluck('ListingId');

        $properties = CompanyListings::select($listings_select)
        -> whereIn('ListOfficeMlsId', config('bright_office_codes'))
        -> whereNotIn('ListingId', $ListingIds)
        -> where('MLSListDate', '>', date('Y-m-d', strtotime('-3 month')))
        -> with(['agent:id,full_name,email,cell_phone', 'notes', 'notes.user'])
        -> get();


        return view('doc_management/compliance/get_missing_listings_html', compact('properties'));

    }

    public function get_missing_contracts(Request $request) {

        $contracts_select = [
            'Agent_ID',
            'BuyerAgentEmail',
            'BuyerAgentFirstName',
            'BuyerAgentLastName',
            'BuyerAgentPreferredPhone',
            'City',
            'CloseDate',
            'last_emailed_date',
            'FullStreetAddress',
            'ListPictureURL',
            'ListingId',
            'ListingKey',
            'MlsStatus',
            'PostalCode',
            'PurchaseContractDate',
            'StateOrProvince'
        ];

        $ListingIds = Contracts::select('ListingId')
        -> whereNotNull('ListingId')
        -> where('ListingId', '!=', '')
        -> where('ContractDate', '>', date('Y-m-d', strtotime('-6 month')))
        -> pluck('ListingId');

        $properties = CompanyListings::select($contracts_select)
        -> whereIn('BuyerOfficeMlsId', config('bright_office_codes'))
        -> whereNotIn('ListingId', $ListingIds)
        -> where('PurchaseContractDate', '>', date('Y-m-d', strtotime('-6 month')))
        -> with(['agent:id,full_name,email,cell_phone', 'notes', 'notes.user'])
        -> get();

        return view('doc_management/compliance/get_missing_contracts_html', compact('properties'));

    }

    public function get_missing_contracts_our_listing(Request $request) {

        $listings_select = [
            'Agent_ID',
            'City',
            'FullStreetAddress',
            'last_emailed_date',
            'ListAgentEmail',
            'ListAgentFirstName',
            'ListAgentLastName',
            'ListAgentPreferredPhone',
            'ListingId',
            'ListingKey',
            'MLSListDate',
            'MlsStatus',
            'PostalCode',
            'PurchaseContractDate',
            'StateOrProvince'
        ];

        $ListingIds = Contracts::select('ListingId')
        -> whereNotNull('ListingId')
        -> where('ListingId', '!=', '')
        -> where('ContractDate', '>', date('Y-m-d', strtotime('-6 month')))
        -> pluck('ListingId');

        $properties = CompanyListings::select($listings_select)
        -> whereIn('ListOfficeMlsId', config('bright_office_codes'))
        -> whereNotIn('ListingId', $ListingIds)
        -> whereIn('MlsStatus', ['PENDING', 'ACTIVE UNDER CONTRACT'])
        -> with(['agent:id,full_name,email,cell_phone', 'notes', 'notes.user'])
        -> get();

        return view('doc_management/compliance/get_missing_contracts_our_listing_html', compact('properties'));

    }

    public function get_transaction_notes(Request $request) {

		$notes = CompanyListingsNotes::where('ListingKey', $request -> ListingKey)
        -> orderBy('created_at', 'desc') -> get();

        return view('doc_management/compliance/get_transaction_notes_html', compact('notes'));
    }

    public function save_add_transaction_notes(Request $request) {

		$add_notes = new CompanyListingsNotes();
        $add_notes -> ListingKey = $request -> ListingKey;
        $add_notes -> notes = $request -> notes;
        $add_notes -> user_id = auth() -> user() -> id;
        $add_notes -> save();

        return response() -> json(['status' => 'success']);
    }

    public function delete_transaction_note(Request $request) {

		$note = CompanyListingsNotes::find($request -> note_id) -> delete();

        return response() -> json(['status' => 'success']);
    }

    public function email_agents_missing_transactions(Request $request) {

        $listing_keys = explode(',', $request -> listing_keys);
        $type = $request -> type;
        $subject = $request -> subject;
        $message = $request -> message;

        $documents_type = $type == 'listing' ? 'Listing Agreement' : 'Sales Contract';

        $from_name = auth() -> user() -> name;
        $from_address = auth() -> user() -> email;

        $email['from'] = ['email' => $from_address, 'name' => $from_name];

        $listings_select = [
            'Agent_ID',
            'City',
            'FullStreetAddress',
            'ListAgentEmail',
            'ListAgentFirstName',
            'ListAgentLastName',
            'ListAgentPreferredPhone',
            'ListingId',
            'ListingKey',
            'MLSListDate',
            'MlsStatus',
            'PostalCode',
            'StateOrProvince'
        ];

        $properties = CompanyListings::select($listings_select)
        -> whereIn('ListingKey', $listing_keys)
        -> with(['agent:id,first_name,full_name,email,cell_phone'])
        -> get();

        foreach ($properties as $property) {

            $agent = $property -> agent;
            if($agent) {
                $to_name = $agent -> full_name;
                $to_address = $agent -> email;
                $message = preg_replace('/%%FirstName%%/', $agent -> first_name, $message);
            } else {
                $to_name = $property -> ListAgentFirstName.' '.$property -> ListAgentLastName;
                $to_address = $property -> ListAgentEmail;
                $message = preg_replace('/%%FirstName%%/', $property -> ListAgentFirstName, $message);
            }

            if(config('app.env') == 'local') {
                $to_address = 'miketaylor0101@gmail.com';
            }

            $property_address = $property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
            $subject = preg_replace('/%%PropertyAddress%%/', $property_address, $subject);
            $message = preg_replace('/%%PropertyAddress%%/', $property_address, $message);
            $subject = preg_replace('/%%DocumentsType%%/', $documents_type, $subject);
            $message = preg_replace('/%%DocumentsType%%/', $documents_type, $message);

            $email['subject'] = $subject;
            $email['message'] = $message;

            $new_mail = new DefaultEmail($email);

            //return ($new_mail) -> render();

            Mail::to([['name' => $to_name, 'email' => $to_address]])
                -> queue($new_mail);

            // update earnest last emailed date
            $property -> last_emailed_date = date('Y-m-d');
            $property -> save();

        }

        return response() -> json(['status' => 'success']);

    }



}
