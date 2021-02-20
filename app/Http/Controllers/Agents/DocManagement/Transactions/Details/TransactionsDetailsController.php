<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions\Details;

use File;

use Config;
use App\User;

use App\Mail\DefaultEmail;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use thiagoalessio\TesseractOCR\TesseractOCR;

use App\Http\Controllers\Controller;

use App\Models\CRM\CRMContacts;

use App\Models\BrightMLS\AgentRoster;

use App\Models\Commission\Commission;
use App\Models\Commission\CommissionChecksIn;
use App\Models\Commission\CommissionChecksOut;
use App\Models\Commission\CommissionBreakdowns;
use App\Models\Commission\CommissionBreakdownsDeductions;
use App\Models\Commission\CommissionChecksInQueue;
use App\Models\Commission\CommissionNotes;
use App\Models\Commission\CommissionIncomeDeductions;
use App\Models\Commission\CommissionCommissionDeductions;

use App\Models\Employees\AgentsNotes;
use App\Models\Employees\AgentsTeams;
use App\Models\Employees\Agents;

use App\Models\Resources\LocationData;
use App\Models\Admin\Resources\ResourceItemsAdmin;
use App\Models\DocManagement\Resources\ResourceItems;

use App\Mail\DocManagement\Emails\Documents;

use App\Models\DocManagement\Earnest\Earnest;
use App\Models\DocManagement\Earnest\EarnestNotes;
use App\Models\DocManagement\Earnest\EarnestChecks;

use App\Models\DocManagement\Create\Fields\Fields;
use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Create\Upload\UploadPages;
use App\Models\DocManagement\Create\Fields\CommonFields;
use App\Models\DocManagement\Create\Upload\UploadImages;

use App\Models\DocManagement\Checklists\Checklists;
use App\Models\DocManagement\Checklists\ChecklistsItems;

use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Transactions\Members\TransactionCoordinators;

use App\Models\DocManagement\Transactions\Data\ListingsData;

use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Referrals\Referrals;

use App\Models\DocManagement\Transactions\Documents\InProcess;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsImages;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsEmailed;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;


use App\Models\DocManagement\Transactions\EditFiles\UserFields;
use App\Models\DocManagement\Transactions\EditFiles\UserFieldsInputs;

use App\Models\DocManagement\Create\Fields\CommonFieldsSubGroups;

use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadPages;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;

use App\Models\DocManagement\Transactions\Checklists\TransactionChecklists;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsNotes;

use Eversign\Client;

use App\Models\Esign\EsignEnvelopes;
use App\Models\Esign\EsignCallbacks;
use App\Models\Esign\EsignFields;
use App\Models\Esign\EsignSigners;
use App\Models\Esign\EsignDocuments;
use App\Models\Esign\EsignDocumentsImages;
use App\Models\Esign\EsignTemplates;


use App\Jobs\AddFieldAndInputs;


class TransactionsDetailsController extends Controller {


    // Transaction Details
    public function transaction_details(Request $request) {

        $transaction_type = $request -> transaction_type;
        $id = $request -> id;

        $Listing_ID = 0;
        $Contract_ID = 0;
        $Referral_ID = 0;
        $contracts = [];

        if($transaction_type == 'listing') {
            $property = Listings::find($id);
            $field = 'Listing_ID';
            $Listing_ID = $id;
            // if not all required details submitted require them
            if($property -> ExpirationDate == '' || $property -> ExpirationDate == '0000-00-00') {
                return redirect('/agents/doc_management/transactions/add/transaction_required_details_listing/'.$id.'/listing');
            }
            $active_status_id = ResourceItems::GetResourceID('Active', 'contract_status');
            $contracts = Contracts::where('Listing_ID', $Listing_ID) -> where('Status', $active_status_id) -> pluck('Contract_ID');

            if(count($contracts) > 0) {
                $Contract_ID = $contracts[0];
            }

            $member_type_id = ResourceItems::SellerResourceId();

        } else if($transaction_type == 'contract') {
            $property = Contracts::find($id);
            $field = 'Contract_ID';
            $Contract_ID = $id;
            $Listing_ID = $property -> Listing_ID;
            // if not all required details submitted require them
            if($property -> SaleRent != 'rental') {
                if($property -> ContractDate == '' || $property -> ContractDate == '0000-00-00') {
                    return redirect('/agents/doc_management/transactions/add/transaction_required_details_contract/'.$id.'/contract');
                }
            }

            $member_type_id = ResourceItems::BuyerResourceId();

        } else if($transaction_type == 'referral') {
            $property = Referrals::find($id);
            $field = 'Referral_ID';
            $Referral_ID = $id;
        }


        $agents = Agents::select('id', 'first_name', 'last_name', 'llc_name', 'email', 'cell_phone', 'company') -> where('active', 'yes') -> orderBy('last_name') -> get();
        $Agent_ID = $property -> Agent_ID;

        $agent_details = Agents::where('id', $Agent_ID) -> first();

        // check if earnest and title questions are complete before allowing adding docs to the checklist
        $questions_confirmed = 'yes';

        if($transaction_type == 'contract' && $property -> SaleRent != 'rental') {

            if($property -> EarnestAmount == '' || $property -> EarnestHeldBy == '') {
                $questions_confirmed = 'no';
            }

            if($property -> UsingHeritage == '' || ($property -> UsingHeritage == 'no' && $property -> TitleCompany == '')) {
                $questions_confirmed = 'no';
            }

        }
        $for_sale = $property -> SaleRent == 'sale' || $property -> SaleRent == 'both' ? true : false;

        if(($property -> Contract_ID > 0 && $property -> Listing_ID > 0) || count($contracts) > 0) {
            $folders = TransactionDocumentsFolders::where(function ($query) use ($Listing_ID, $Contract_ID) {
                $query -> where('Contract_ID', $Contract_ID) -> orWhere('Listing_ID', $Listing_ID);
            })
            -> orderBy('folder_order') -> get();
        } else {
            $folders = TransactionDocumentsFolders::where($field, $id) -> orderBy('folder_order') -> get();
        }

        $transaction_checklist = TransactionChecklists::where($field, $id) -> first();
        $checklist_id = $transaction_checklist -> id;
        $original_checklist_id = $transaction_checklist -> checklist_id;

        $transaction_checklist_hoa_condo = $transaction_checklist -> hoa_condo;
        $transaction_checklist_year_built = $transaction_checklist -> year_built;

        $checklist = Checklists::where('id', $original_checklist_id) -> first();

        $checklist_items = TransactionChecklistItems::where('checklist_id', $checklist_id) -> get();
        $checklist_items_required = $checklist_items -> where('checklist_item_required', 'yes') -> sortBy('checklist_item_order');
        $checklist_items_if_applicable = $checklist_items -> where('checklist_item_required', 'no') -> sortBy('checklist_item_order');

        $available_files = new Upload();

        $resource_items = new ResourceItems();
        $form_groups = $resource_items -> where('resource_type', 'form_groups') -> where('resource_association', 'yes') -> orderBy('resource_order') -> get();
        $form_categories = $resource_items -> where('resource_type', 'form_categories') -> orderBy('resource_order') -> get();

        $files = new Upload();

        $members = null;

        if($transaction_type != 'referral') {
            $member_type_id = Members::GetMemberTypeID('Buyer');

            if($Listing_ID > 0) {
                $member_type_id = Members::GetMemberTypeID('Seller');
            }

            $members = Members::where($field, $id) -> where('member_type_id', $member_type_id) -> get();
        }

        $contacts = CRMContacts::where('Agent_ID', $Agent_ID) -> get();

        $rejected_reasons = ResourceItemsAdmin::where('resource_type', 'rejected_reason') -> orderBy('resource_order') -> get();

        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = $resource_items -> where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();

        $states = LocationData::AllStates();

        return view('/agents/doc_management/transactions/details/transaction_details', compact('Listing_ID', 'Contract_ID', 'Referral_ID', 'property', 'transaction_type', 'questions_confirmed', 'agents', 'agent_details', 'for_sale', 'checklist', 'checklist_id', 'folders', 'checklist_items_required', 'checklist_items_if_applicable', 'available_files', 'resource_items', 'form_groups', 'form_categories', 'files', 'members', 'contacts', 'rejected_reasons', 'property_types', 'property_sub_types', 'transaction_checklist_hoa_condo', 'transaction_checklist_year_built', 'states'));

    }

    // Transaction Details Header
    public function transaction_details_header(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;

        $property = Listings::GetPropertyDetails($transaction_type, [$Listing_ID, $Contract_ID, $Referral_ID]);

        $listing_expiration_date = null;
        if($transaction_type == 'contract') {
            if($property -> Listing_ID > 0) {
                $listing = Listings::find($Listing_ID);
                $listing_expiration_date = $listing -> ExpirationDate;
            }
        }

        $resource_items = new ResourceItems();

        if($transaction_type != 'referral') {
            $members = Members::where('Contract_ID', $Contract_ID) -> get();
            if($transaction_type == 'listing') {
                $members = Members::where('Listing_ID', $Listing_ID) -> get();
            }
            //$buyers = $members -> where('member_type_id', $resource_items -> BuyerResourceId());
            //$sellers = $members -> where('member_type_id', $resource_items -> SellerResourceId());

            $buyers = collect($property -> BuyerOneFirstName.' '.$property -> BuyerOneLastName);
            if($property -> BuyerTwoFirstName != '') {
                $buyers -> push($property -> BuyerTwoFirstName.' '.$property -> BuyerTwoLastName);
            }
            $sellers = collect($property -> SellerOneFirstName.' '.$property -> SellerOneLastName);
            if($property -> SellerTwoFirstName != '') {
                $sellers -> push($property -> SellerTwoFirstName.' '.$property -> SellerTwoLastName);
            }

            // get active contracts
            /* $status = ResourceItems::GetResourceID('Active', 'contract_status');
            $active_contracts_count = Contracts::where('Listing_ID', $Listing_ID) -> where('Status', $status) -> count(); */

        } else {
            $buyers = null;
            $sellers = null;
        }

        $upload = new Upload();

        $listing_accepted = false;
        if($Listing_ID > 0) {
            $docs_submitted = Upload::DocsSubmitted($Listing_ID, '');
            if($docs_submitted['listing_accepted']) {
                $listing_accepted = true;
            }
        }

        //$statuses = $resource_items -> where('resource_type', 'listing_status') -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/details/transaction_details_header', compact('transaction_type', 'property', 'buyers', 'sellers', 'resource_items', 'listing_expiration_date', 'upload', 'Contract_ID', 'listing_accepted'));
    }


    // TABS


    // Details Tab

    public function get_details(Request $request) {


        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;


        $list_agent = '';

        $resource_items = new ResourceItems();

        $listing_closed = false;
        $contract_closed = false;
        if($transaction_type == 'listing') {

            $property = Listings::find($Listing_ID);
            if(in_array($property -> Status, $resource_items -> GetClosedAndCanceledListingStatuses() -> toArray())) {
                $listing_closed = true;
            }

        } else if($transaction_type == 'contract') {

            $property = Contracts::find($Contract_ID);

            if(in_array($property -> Status, $resource_items -> GetClosedAndCanceledContractStatuses() -> toArray())) {
                $contract_closed = true;
            }

            $list_agent = $property -> ListAgentFirstName.' '.$property -> ListAgentLastName;

            if($property -> Listing_ID > 0) {
                $listing = Listings::find($property -> Listing_ID);
                $list_agent = $listing -> ListAgentFirstName.' '.$listing -> ListAgentLastName;
            }

        } else if($transaction_type == 'referral') {
            $property = Referrals::find($Referral_ID);
        }


        $for_sale = $property -> SaleRent == 'sale' || $property -> SaleRent == 'both' ? true : false;

        $agents = Agents::where('active', 'yes') -> orderBy('last_name') -> get();
        $teams = AgentsTeams::where('active', 'yes') -> orderBy('team_name') -> get();
        $street_suffixes = config('global.vars.street_suffixes');
        $street_dir_suffixes = config('global.vars.street_dir_suffixes');
        $states_active = config('global.vars.active_states');
        $states = LocationData::AllStates();

        $property_state = $property -> StateOrProvince;
        $counties = LocationData::CountiesByState($property_state);
        $trans_coords = TransactionCoordinators::where('active', 'yes') -> orderBy('last_name') -> get();

        $has_listing = false;

        if($transaction_type == 'contract' && $property -> Listing_ID > 0) {
            $has_listing = true;
        }

        $details_type = ucwords($transaction_type);
        if($transaction_type == 'contract' && $for_sale == false) {
            $details_type = 'Lease';
        }

        return view('/agents/doc_management/transactions/details/data/get_details', compact('transaction_type', 'property', 'contract_closed', 'listing_closed', 'for_sale', 'list_agent', 'agents', 'teams', 'street_suffixes', 'street_dir_suffixes', 'states_active', 'states', 'counties', 'trans_coords', 'has_listing', 'details_type'));
    }

    public function mls_search(Request $request) {

        // search database first
        $select_columns_db = explode(',', config('global.vars.select_columns_bright'));
        $mls_search_details = ListingsData::select($select_columns_db) -> where('ListingId', $request -> ListingId) -> first();

        // if not found search bright mls
        if(!$mls_search_details) {
            $mls_search_details = bright_mls_search($request -> ListingId);
            $mls_search_details = (object)$mls_search_details;
        }

        // only if mls search produced results
        if(isset($mls_search_details -> ListingId)) {

            return response() -> json([
                'status' => 'ok',
                'county_match' => 'yes',
                'address' => $mls_search_details -> FullStreetAddress,
                'city' => $mls_search_details -> City,
                'state' => $mls_search_details -> StateOrProvince,
                'zip' => $mls_search_details -> PostalCode,
                'picture_url' => $mls_search_details -> ListPictureURL,
                'list_company' => $mls_search_details -> ListOfficeName,
            ]);
        }

        return response() -> json([
            'status' => 'not found',
        ]);
    }

    public function save_mls_search(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $MLS_ID = $request -> ListingId;
        $transaction_type = $request -> transaction_type;

        $represent = ($Listing_ID > 0 ? 'seller' : 'buyer');

        $property = Listings::GetPropertyDetails($transaction_type, [$Listing_ID, $Contract_ID, null]);

        $mls_search_details = bright_mls_search($MLS_ID);
        $mls_search_details = (object)$mls_search_details;

        $resource_items = new ResourceItems();

        $checklist = TransactionChecklists::where('Agent_ID', $property -> Agent_ID);

        if($transaction_type == 'listing') {
            $checklist = $checklist -> where('Listing_ID', $Listing_ID) -> first();
        } else {
            $checklist = $checklist -> where('Contract_ID', $Contract_ID) -> first();
        }

        $checklist_id = $checklist -> id;

        // set values
        $property_type_val = $mls_search_details -> PropertyType;
        $sale_rent = '';

        if($property_type_val) {

            if(stristr($property_type_val, 'lease')) {
                $sale_rent = 'rental';
                $property_type_val = str_replace(' Lease', '', $property_type_val);
            } else {
                $sale_rent = 'sale';
            }

        }

        $property_type_id = $resource_items -> GetResourceID($property_type_val, 'checklist_property_types');

        $property_sub_type = $mls_search_details -> SaleType;

        if($property_sub_type) {
            $end = strpos($property_sub_type, ',');

            if(!$end) {
                $end = strlen($property_sub_type);
            }

            $property_sub_type = trim(substr($property_sub_type, 0, $end));

            if(preg_match('/(hud|reo)/i', $property_sub_type)) {
                $property_sub_type = 'REO/Bank/HUD Owned';
            } else if(preg_match('/foreclosure/i', $property_sub_type)) {
                $property_sub_type = 'Foreclosure';
            } else if(preg_match('/auction/i', $property_sub_type)) {
                $property_sub_type = 'Auction';
            } else if(preg_match('/(short|third)/i', $property_sub_type)) {
                $property_sub_type = 'Short Sale';
            } else if(preg_match('/standard/i', $property_sub_type)) {
                $property_sub_type = 'Standard';
            } else {
                $property_sub_type = '';
            }

            // if no results check new construction
            if($property_sub_type == '') {
                if($mls_search_details -> NewConstructionYN == 'Y') {
                    $property_sub_type = 'New Construction';
                }

            }

        }

        $property_sub_type_id = $resource_items -> GetResourceID($property_sub_type, 'checklist_property_sub_types');

        $hoa_condo = 'none';
        $condo = $mls_search_details -> CondoYN ?? null;
        if($condo && $condo == 'Y') {
            $hoa_condo = 'condo';
        }

        $hoa = $mls_search_details -> AssociationYN ?? null;
        if($hoa && $hoa == 'Y') {
            if($mls_search_details -> AssociationFee > 0) {
                $hoa_condo = 'hoa';
            }

        }

        if($mls_search_details -> StateOrProvince == 'MD') {
            $location_id = $resource_items -> GetResourceID($mls_search_details -> County, 'checklist_locations');
        } else {
            $location_id = $resource_items -> GetResourceID($mls_search_details -> StateOrProvince, 'checklist_locations');
        }

        $year_built = $mls_search_details -> YearBuilt;

        TransactionChecklists::CreateTransactionChecklist($checklist_id, $Listing_ID, $Contract_ID, '', $property -> Agent_ID, $represent, $transaction_type, $property_type_id, $property_sub_type_id, $sale_rent, $mls_search_details -> StateOrProvince, $location_id, $hoa_condo, $year_built);

        $property -> ListingId = $request -> ListingId;

        // get cols and vals for mls search
        foreach ($mls_search_details as $col => $val) {

            // if property col matches then update it if it doesn't match original value
            if(isset($property -> $col)) {
                if($property -> $col != $val && $val != '') {
                    // if a name field only replace if blank
                    if(in_array($property -> $col, config('global.vars.select_columns_bright_agents'))) {
                        if($val == '') {
                            $property -> $col = $val;
                        }
                    } else {
                        if($col == 'PropertyType') {
                            $property -> $col = $property_type_id;
                        } else if($col == 'PropertySubType') {
                            $property -> $col = $property_sub_type_id;
                        } else if($col == 'County') {
                            $property -> $col = $location_id;
                        } else if($col == 'HoaCondoFees') {
                            $property -> $col = $hoa_condo;
                        } else {
                            $property -> $col = $val;
                        }
                    }
                }
            }
        }

        $property -> MLS_Verified = 'yes';
        $property -> save();

        return response() -> json([
            'status' => 'ok',
        ]);

    }

    public function save_details(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;
        $has_listing = false;

        if($transaction_type == 'listing') {
            $property = Listings::find($Listing_ID);
        } else if($transaction_type == 'contract') {
            $property = Contracts::find($Contract_ID);

            if($property -> Listing_ID > 0) {
                $has_listing = true;
                $property_listing = Listings::find($property -> Listing_ID);
            }

        } else if($transaction_type == 'referral') {
            $property = Referrals::find($Referral_ID);
        }

        if($transaction_type != 'referral') {

            // mls needs to be verified. if not MLS_Verified needs to be set to no
            $property -> MLS_Verified = 'no';

            // listing can be verified but not contract unless listing verified
            if($has_listing) {
                $property_listing -> MLS_Verified = 'no';
            }
            // verify listing
            if($request -> ListingId && bright_mls_search($request -> ListingId)) {
                $property -> MLS_Verified = 'yes';
                // verify contract now listing has been verified
                if($has_listing) {
                    $property_listing -> MLS_Verified = 'yes';
                }

            }

        }

        if($request -> StreetNumber  != '') {

            $FullStreetAddress = $request -> StreetNumber.' '.$request -> StreetName.' '.$request -> StreetSuffix;

            if($request -> StreetDirSuffix) {
                $FullStreetAddress .= ' '.$request -> StreetDirSuffix;
            }

            if($request -> UnitNumber) {
                $FullStreetAddress .= ' '.$request -> UnitNumber;
            }

            $request -> merge(['FullStreetAddress' => $FullStreetAddress]);
        }

        $resource_items = new ResourceItems();
        $new_status = null;
        if($transaction_type == 'listing') {
            // set status if list date or expire date has changed - only for properties that have not closed
            // compare old to new
            if($property -> MLSListDate != $request -> MLSListDate || $property -> ExpirationDate != $request -> ExpirationDate) {
                if($request -> MLSListDate <= date('Y-m-d') && $request -> ExpirationDate >= date('Y-m-d')) {
                    $new_status = $resource_items -> GetResourceID('Active', 'listing_status');
                } else {
                    // set to pre listing if list date before today
                    if($request -> MLSListDate > date('Y-m-d')) {
                        $new_status = $resource_items -> GetResourceID('Pre-Listing', 'listing_status');
                    }
                    // set to expired or active
                    if($request -> ExpirationDate < date('Y-m-d')) {
                        $new_status = $resource_items -> GetResourceID('Expired', 'listing_status');
                    }
                }
            }
        } else if($transaction_type == 'contract') {
            // set status if settle date has changed???

        }
        if($new_status) {
            $property -> Status = $new_status;
        }

        foreach ($request -> all() as $col => $val) {

            $ignore_cols = ['Listing_ID', 'Contract_ID', 'Referral_ID', 'transaction_type'];
            if(!in_array($col, $ignore_cols) && !stristr($col, '_submit')) {

                if(preg_match('/\$/', $val)) {
                    $val = preg_replace('/[\$,]+/', '', $val);
                }
                $property -> $col = $val;

                if($has_listing) {
                    if(!in_array($col, Contracts::ContractColumnsNotInListings())) {
                        $property_listing -> $col = $val;
                    }
                }

            }

        }

        $property -> save();
        if($has_listing) {
            $property_listing -> save();
        }

        return response() -> json([
            'success' => 'ok',
        ]);
    }

    public function save_required_fields(Request $request) {

        $Contract_ID = $request -> Contract_ID;
        $property = Contracts::find($Contract_ID);
        $property -> UsingHeritage = $request -> required_fields_using_heritage;
        $property -> TitleCompany = $request -> required_fields_title_company;
        $property -> EarnestAmount = preg_replace('/[\$,]+/', '', $request -> required_fields_earnest_amount);
        $property -> EarnestHeldBy = $request -> required_fields_earnest_held_by;
        $property -> save();

        return true;

    }

    // End Details Tab


    // Members Tab

    public function get_members(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;

        $members = Members::where('Listing_ID', $Listing_ID) -> get();
        $transaction_type = 'listing';

        if($Contract_ID > 0) {
            $members = Members::where('Contract_ID', $Contract_ID) -> get();
            $transaction_type = 'contract';
        }

        $resource_items = new ResourceItems();

        $checklist_types = ['listing', 'both'];

        if($transaction_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } else if($transaction_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $property = Listings::GetPropertyDetails($transaction_type, [$Listing_ID, $Contract_ID, $Referral_ID]);
        $for_sale = $property -> SaleRent == 'sale' || $property -> SaleRent == 'both' ? true : false;

        $contact_types = $resource_items -> where('resource_type', 'contact_type') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        $states = LocationData::AllStates();

        return view('/agents/doc_management/transactions/details/data/get_members', compact('members', 'contact_types', 'resource_items', 'states','for_sale'));

    }

    public function add_member_html(Request $request) {
        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;

        $property = Listings::GetPropertyDetails($transaction_type, [$Listing_ID, $Contract_ID, $Referral_ID]);
        $for_sale = $property -> SaleRent == 'sale' || $property -> SaleRent == 'both' ? true : false;

        $checklist_types = ['listing', 'both'];

        if($transaction_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } else if($transaction_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $contact_types = ResourceItems::where('resource_type', 'contact_type') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        $states = LocationData::AllStates();

        return view('/agents/doc_management/transactions/details/data/add_member_html', compact('for_sale', 'contact_types', 'states'));

    }

    public function delete_member(Request $request) {

        if($member = Members::find($request -> id) -> delete()) {

            if($request -> transaction_type == 'listing') {
                $this -> update_transaction_members($request -> Listing_ID, 'listing');
            } else {
                $this -> update_transaction_members($request -> Contract_ID, 'contract');
            }

            return response() -> json([
                'status' => 'ok',
            ]);

        }

    }

    public function save_member(Request $request) {

        $new_member = null;
        if($request -> id && $request -> id != 'undefined') {
            $member = Members::find($request -> id);
        } else {
            $member = new Members();
            $new_member = 'yes';
        }

        if(!$new_member) {
            // update emails in esign drafts
            if($member -> email != $request -> email) {
                $signer = EsignSigners::where('signer_email', $member -> email) -> update(['signer_email' => $request -> email]);
            }
        }

        $data = $request -> all();

        foreach ($data as $col => $val) {
            if($col != 'id') {
                $member -> $col = $val ?? null;
            }
        }

        $member -> save();

        if($request -> transaction_type == 'listing') {
            $this -> update_transaction_members($request -> Listing_ID, 'listing');
        } else {
            $this -> update_transaction_members($request -> Contract_ID, 'contract');
        }

        return response() -> json([
            'status' => 'ok',
        ]);
    }

    public function update_transaction_members($id, $type) {

        $field = 'Listing_ID';

        if($type == 'contract') {
            $field = 'Contract_ID';
        }

        if($type == 'contract') {
            $property = Contracts::find($id);
        } else {
            $property = Listings::find($id);
        }

        $sellers = Members::where($field, $id) -> where('member_type_id', ResourceItems::SellerResourceId()) -> get();

        $c = 0;
        $seller_fullnames = [];

        // clear second seller and buyers from property incase one was removed
        $property -> SellerTwoFirstName = '';
        $property -> SellerTwoLastName = '';
        $property -> SellerTwoFullName = '';
        $property -> SellerTwoEmail = '';
        $property -> SellerTwoCellPhone = '';
        $property -> SellerTwoFullStreetAddress = '';
        $property -> SellerTwoCity = '';
        $property -> SellerTwoStateOrProvince = '';
        $property -> SellerTwoPostalCode = '';
        $property -> SellerTwoFullAddress = '';

        $property -> BuyerOneFirstName = '';
        $property -> BuyerOneLastName = '';
        $property -> BuyerOneFullName = '';
        $property -> BuyerOneEmail = '';
        $property -> BuyerOneCellPhone = '';
        $property -> BuyerOneFullStreetAddress = '';
        $property -> BuyerOneCity = '';
        $property -> BuyerOneStateOrProvince = '';
        $property -> BuyerOnePostalCode = '';
        $property -> BuyerOneFullAddress = '';

        $property -> BuyerTwoFirstName = '';
        $property -> BuyerTwoLastName = '';
        $property -> BuyerTwoFullName = '';
        $property -> BuyerTwoEmail = '';
        $property -> BuyerTwoCellPhone = '';
        $property -> BuyerTwoFullStreetAddress = '';
        $property -> BuyerTwoCity = '';
        $property -> BuyerTwoStateOrProvince = '';
        $property -> BuyerTwoPostalCode = '';
        $property -> BuyerTwoFullAddress = '';


        foreach($sellers as $seller) {
            if($c == 0) {

                $seller_fullname = $seller -> first_name.' '.$seller -> last_name;
                $seller_fullnames[] = $seller_fullname;

                $property -> SellerOneFirstName = $seller -> first_name;
                $property -> SellerOneLastName = $seller -> last_name;
                $property -> SellerOneFullName = $seller_fullname;
                $property -> SellerOneEmail = $seller -> email;
                $property -> SellerOneCellPhone = $seller -> cell_phone;
                $property -> SellerOneFullStreetAddress = $seller -> address_home_street;
                $property -> SellerOneCity = $seller -> address_home_city;
                $property -> SellerOneStateOrProvince = $seller -> address_home_state;
                $property -> SellerOnePostalCode = $seller -> address_home_zip;
                $property -> SellerOneFullAddress = $seller -> address_home_street.' '.$seller -> address_home_city.', '.$seller -> address_home_state.' '.$seller -> address_home_zip;

            } else if($c == 1) {

                $seller_fullname = $seller -> first_name.' '.$seller -> last_name;
                $seller_fullnames[] = $seller_fullname;

                $property -> SellerTwoFirstName = $seller -> first_name;
                $property -> SellerTwoLastName = $seller -> last_name;
                $property -> SellerTwoFullName = $seller_fullname;
                $property -> SellerTwoEmail = $seller -> email;
                $property -> SellerTwoCellPhone = $seller -> cell_phone;
                $property -> SellerTwoFullStreetAddress = $seller -> address_home_street;
                $property -> SellerTwoCity = $seller -> address_home_city;
                $property -> SellerTwoStateOrProvince = $seller -> address_home_state;
                $property -> SellerTwoPostalCode = $seller -> address_home_zip;
                $property -> SellerTwoFullAddress = $seller -> address_home_street.' '.$seller -> address_home_city.', '.$seller -> address_home_state.' '.$seller -> address_home_zip;

            }
            $c += 1;
        }

        $property -> SellerBothFullName = implode(', ', $seller_fullnames);


        $buyers = Members::where($field, $id) -> where('member_type_id', ResourceItems::BuyerResourceId()) -> get();

        $c = 0;
        $buyer_fullnames = [];
        foreach($buyers as $buyer) {
            if($c == 0) {

                $buyer_fullname = $buyer -> first_name.' '.$buyer -> last_name;
                $buyer_fullnames[] = $buyer_fullname;

                $property -> BuyerOneFirstName = $buyer -> first_name;
                $property -> BuyerOneLastName = $buyer -> last_name;
                $property -> BuyerOneFullName = $buyer_fullname;
                $property -> BuyerOneEmail = $buyer -> email;
                $property -> BuyerOneCellPhone = $buyer -> cell_phone;
                $property -> BuyerOneFullStreetAddress = $buyer -> address_home_street;
                $property -> BuyerOneCity = $buyer -> address_home_city;
                $property -> BuyerOneStateOrProvince = $buyer -> address_home_state;
                $property -> BuyerOnePostalCode = $buyer -> address_home_zip;
                $property -> BuyerOneFullAddress = $buyer -> address_home_street.' '.$buyer -> address_home_city.', '.$buyer -> address_home_state.' '.$buyer -> address_home_zip;

            } else if($c == 1) {

                $buyer_fullname = $buyer -> first_name.' '.$buyer -> last_name;
                $buyer_fullnames[] = $buyer_fullname;

                $property -> BuyerTwoFirstName = $buyer -> first_name;
                $property -> BuyerTwoLastName = $buyer -> last_name;
                $property -> BuyerTwoFullName = $buyer_fullname;
                $property -> BuyerTwoEmail = $buyer -> email;
                $property -> BuyerTwoCellPhone = $buyer -> cell_phone;
                $property -> BuyerTwoFullStreetAddress = $buyer -> address_home_street;
                $property -> BuyerTwoCity = $buyer -> address_home_city;
                $property -> BuyerTwoStateOrProvince = $buyer -> address_home_state;
                $property -> BuyerTwoPostalCode = $buyer -> address_home_zip;
                $property -> BuyerTwoFullAddress = $buyer -> address_home_street.' '.$buyer -> address_home_city.', '.$buyer -> address_home_state.' '.$buyer -> address_home_zip;
            }
            $c += 1;
        }

        $property -> BuyerBothFullName = implode(', ', $buyer_fullnames);


        $buyer_agent = Members::where($field, $id) -> where('member_type_id', ResourceItems::BuyerAgentResourceId()) -> first();

        if($buyer_agent) {
            $property -> BuyerAgentFirstName = $buyer_agent -> first_name;
            $property -> BuyerAgentLastName = $buyer_agent -> last_name;
            $property -> BuyerAgentFullName = $buyer_agent -> first_name.' '.$buyer_agent -> last_name;
            $property -> BuyerAgentEmail = $buyer_agent -> email;
            $property -> BuyerAgentPreferredPhone = $buyer_agent -> cell_phone;
            $property -> BuyerOfficeName = $buyer_agent -> company;
            $property -> BuyerOfficeFullStreetAddress = $buyer_agent -> address_office_street;
            $property -> BuyerOfficeCity = $buyer_agent -> address_office_city;
            $property -> BuyerOfficeStateOrProvince = $buyer_agent -> address_office_state;
            $property -> BuyerOfficePostalCode = $buyer_agent -> address_office_zip;
            $property -> BuyerOfficeFullAddress = $buyer_agent -> address_office_street.' '.$buyer_agent -> address_office_city.', '.$buyer_agent -> address_office_state.' '.$buyer_agent -> address_office_zip;
        }

        $list_agent = Members::where($field, $id) -> where('member_type_id', ResourceItems::ListingAgentResourceId()) -> first();

        if($list_agent) {
            $property -> ListAgentFirstName = $list_agent -> first_name;
            $property -> ListAgentLastName = $list_agent -> last_name;
            $property -> ListAgentFullName = $list_agent -> first_name.' '.$list_agent -> last_name;
            $property -> ListAgentEmail = $list_agent -> email;
            $property -> ListAgentPreferredPhone = $list_agent -> cell_phone;
            $property -> ListOfficeName = $list_agent -> company;
            $property -> ListOfficeFullStreetAddress = $list_agent -> address_office_street;
            $property -> ListOfficeCity = $list_agent -> address_office_city;
            $property -> ListOfficeStateOrProvince = $list_agent -> address_office_state;
            $property -> ListOfficePostalCode = $list_agent -> address_office_zip;
            $property -> ListOfficeFullAddress = $list_agent -> address_office_street.' '.$list_agent -> address_office_city.', '.$list_agent -> address_office_state.' '.$list_agent -> address_office_zip;
        }

        $property -> save();

    }

    // End Members Tab


    // Documents Tab

    public function get_documents(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;
        $transaction_type = $request -> transaction_type;

        $contracts = [];
        if($transaction_type == 'listing') {

            $property = Listings::find($Listing_ID);
            $field = 'Listing_ID';
            $id = $Listing_ID;
            $member_type_id = ResourceItems::SellerResourceId();
            $active_status_id = ResourceItems::GetResourceID('Active', 'contract_status');
            $contracts = Contracts::where('Listing_ID', $Listing_ID) -> where('Status', $active_status_id) -> pluck('Contract_ID');

            if(count($contracts) > 0) {
                $Contract_ID = $contracts[0];
            }

        } else if($transaction_type == 'contract') {

            $property = Contracts::find($Contract_ID);
            $field = 'Contract_ID';
            $id = $Contract_ID;
            $member_type_id = ResourceItems::BuyerResourceId();

        } else if($transaction_type == 'referral') {
            $property = Referrals::find($Referral_ID);
            $field = 'Referral_ID';
            $id = $Referral_ID;
        }



        // if our listing and contract include listing folders with contract
        if(($property -> Contract_ID > 0 && $property -> Listing_ID > 0) || count($contracts) > 0) {

            $folders = TransactionDocumentsFolders::where(function ($query) use ($Listing_ID, $Contract_ID) {
                $query -> where('Contract_ID', $Contract_ID) -> orWhere('Listing_ID', $Listing_ID);
            })
            -> orderBy('folder_order') -> get();

            $documents = TransactionDocuments::where(function ($query) use ($Listing_ID, $Contract_ID) {
                $query -> where('Contract_ID', $Contract_ID) -> orWhere('Listing_ID', $Listing_ID);
            })
            -> orderBy('doc_order', 'ASC') -> orderBy('created_at', 'DESC') -> get();

        } else {

            $folders = TransactionDocumentsFolders::where($field, $id) -> orderBy('folder_order') -> get();
            $documents = TransactionDocuments::where($field, $id)
                -> orderBy('doc_order', 'ASC')
                -> orderBy('created_at', 'DESC')
                -> get();

        }

        $transaction_checklist = TransactionChecklists::where($field, $id) -> first();
        $checklist_id = $transaction_checklist -> id;

        $available_files = new Upload();

        $property_email = $property -> PropertyEmail;
        $for_sale = $property -> SaleRent == 'sale' || $property -> SaleRent == 'both' ? true : false;

        return view('/agents/doc_management/transactions/details/data/get_documents', compact('transaction_type', 'property', 'Agent_ID', 'Listing_ID', 'Contract_ID', 'checklist_id', 'documents', 'folders', 'available_files', 'property_email', 'for_sale'));
    }

    public function in_process_esign(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;

        $field = 'Listing_ID';
        $id = $Listing_ID;
        if($transaction_type == 'contract') {
            $field = 'Contract_ID';
            $id = $Contract_ID;
        } else if($transaction_type == 'referral') {
            $field = 'Referral_ID';
            $id = $Referral_ID;
        }

        $documents = TransactionDocuments::where($field, $id)
            -> with('esign_document')
            -> orderBy('doc_order', 'ASC')
            -> orderBy('created_at', 'DESC')
            -> get();

        $esign_documents['sent'] = [];
        $esign_documents['completed'] = [];

        foreach($documents as $document) {

            $esign_document = $document -> esign_document -> last();

            if($esign_document) {
                $envelope = $esign_document -> envelope;
                if($envelope) {
                    if($envelope -> status == 'Sent') {
                        $esign_documents['sent'][] = $esign_document -> transaction_document_id;
                    } else if($envelope -> status == 'Completed') {
                        $esign_documents['completed'][] = $esign_document -> transaction_document_id;
                    }
                }
            }
        }

        return response() -> json(['esign_documents' => $esign_documents]);

    }

    public function in_process(Request $request) {

        $document_ids = explode(',', $request -> document_ids);

        $in_process = [];
        $not_in_process =[];

        if(count($document_ids) > 0) {

            foreach($document_ids as $document_id) {
                $in_process_check = InProcess::where('document_id', $document_id) -> get();
                if(count($in_process_check) > 0) {
                    $in_process[] = $document_id;
                } else {
                    $not_in_process[] = $document_id;
                }
            }

        }

        return response() -> json([
            'in_process' => $in_process,
            'not_in_process' => $not_in_process
            ]);

    }

    public function add_folder(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;
        $folder_name = $request -> folder;

        if($transaction_type == 'listing') {
            $order = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID);
        } else if($transaction_type == 'contract') {
            $order = TransactionDocumentsFolders::where('Contract_ID', $Contract_ID);
        } else if($transaction_type == 'referral') {
            $order = TransactionDocumentsFolders::where('Referral_ID', $Referral_ID);
        }

        $order = $order -> where('Agent_ID', $Agent_ID) -> where('folder_name', '!=', 'Trash') -> max('order');

        $order += 1;
        $folder = new TransactionDocumentsFolders();
        $folder -> folder_name = $folder_name;
        $folder -> folder_order = $order;
        $folder -> Listing_ID = $Listing_ID ?? 0;
        $folder -> Contract_ID = $Contract_ID ?? 0;
        $folder -> Referral_ID = $Referral_ID ?? 0;
        $folder -> Agent_ID = $Agent_ID;
        $folder -> save();
    }

    public function delete_folder(Request $request) {

        $folder_id = $request -> folder_id;
        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;

        if($transaction_type == 'listing') {
            $trash_folder = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID) -> where('folder_name', 'Trash') -> first();
        } else if($transaction_type == 'contract') {
            $trash_folder = TransactionDocumentsFolders::where('Contract_ID', $Contract_ID) -> where('folder_name', 'Trash') -> first();
        } else if($transaction_type == 'referral') {
            $trash_folder = TransactionDocumentsFolders::where('Referral_ID', $Referral_ID) -> where('folder_name', 'Trash') -> first();
        }

        $move_documents_to_trash = TransactionDocuments::where('folder', $folder_id) -> update(['folder' => $trash_folder -> id]);
        $delete_folder = TransactionDocumentsFolders::where('id', $folder_id) -> delete();
    }

    public function get_emailed_documents(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;

        $emailed_documents = TransactionDocumentsEmailed::where('transaction_type', $transaction_type)
            -> where(function($q) use ($transaction_type, $Listing_ID, $Contract_ID, $Referral_ID) {
                if($transaction_type == 'listing') {
                    $q -> where('Listing_ID', $Listing_ID);
                } else if($transaction_type == 'contract') {
                    $q -> where('Contract_ID', $Contract_ID);
                } else if($transaction_type == 'referral') {
                    $q -> where('Referral_ID', $Referral_ID);
                }
            })
            -> where('Agent_ID', $Agent_ID)
            -> where('active', 'yes')
            -> where('email_status', 'success')
            -> get();

        if(count($emailed_documents) == 0) {

            $emailed_documents = null;

        } else {

            // add file size for loading
            foreach($emailed_documents as $emailed_document) {
                $emailed_document -> file_size = filesize(Storage::disk('public') -> path(str_replace('/storage/', '', $emailed_document -> file_location)));
            }
            $emailed_documents = $emailed_documents -> toJson();

        }

        return $emailed_documents;

    }

    public function add_emailed_documents(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;
        $folder = $request -> folder;

        $document_ids = explode(',', $request -> document_ids);

        foreach($document_ids as $document_id) {

            $emailed_document = TransactionDocumentsEmailed::find($document_id);
            $emailed_document -> update(['active' => 'no']);

            $request = new \Illuminate\Http\Request();
            $request -> setMethod('POST');

            $file = Storage::disk('public') -> path(str_replace('/storage/', '', $emailed_document -> file_location));
            $file_name = File::basename($file);
            $file = new UploadedFile($file, $file_name);
            $request -> files -> set('file', $file);

            $request -> request -> add([
                'Agent_ID' => $Agent_ID,
                'Listing_ID' => $Listing_ID ?? 0,
                'Contract_ID' => $Contract_ID ?? 0,
                'Referral_ID' => $Referral_ID ?? 0,
                'transaction_type' => $transaction_type,
                'folder' => $folder
            ]);

            $this -> upload_documents($request);

        }

        return response() -> json(['status' => 'success']);

    }

    public function delete_emailed_document(Request $request) {

        $document_id = $request -> document_id;
        $emailed_document = TransactionDocumentsEmailed::find($document_id);
        $emailed_document -> update(['active' => 'no']);

        return response() -> json(['status' => 'success']);

    }

    public function duplicate_document(Request $request) {

        $document_id = $request -> document_id;
        $file_type = $request -> file_type;
        // get document details
        $document = TransactionDocuments::where('id', $document_id) -> first();

        $orig_upload_id = $document -> file_id;
        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $document -> Agent_ID;

        // copy to documents
        $document_copy = $document -> replicate();
        $document_copy -> save();
        $new_document_id = $document_copy -> id;

        // copy to documents images
        $document_images = TransactionDocumentsImages::where('document_id', $document_id) -> get();

        foreach ($document_images as $document_image) {
            $document_images_copy = $document_image -> replicate();
            $document_images_copy -> document_id = $new_document_id;
            $document_images_copy -> save();
        }

        $upload = TransactionUpload::where('file_id', $orig_upload_id) -> first();

        // create new upload
        $upload_copy = $upload -> replicate();
        $upload_copy -> Transaction_Docs_ID = $new_document_id;
        $upload_copy -> file_name_display = $upload -> file_name_display;
        $upload_copy -> Agent_ID = $Agent_ID;
        $upload_copy -> Listing_ID = $Listing_ID;
        $upload_copy -> Contract_ID = $Contract_ID;
        $upload_copy -> Referral_ID = $Referral_ID;
        $upload_copy -> save();
        $new_upload_id = $upload_copy -> file_id;

        if($transaction_type == 'contract') {
            $path = 'contracts/'.$Contract_ID;
        } else if($transaction_type == 'listing') {
            $path = 'listings/'.$Listing_ID;
        } else if($transaction_type == 'referral') {
            $path = 'referrals/'.$Referral_ID;
        }

        $orig_uploads_path = 'doc_management/transactions/'.$path.'/'.$orig_upload_id.'_'.$file_type;
        $new_uploads_path = 'doc_management/transactions/'.$path.'/'.$new_upload_id.'_'.$file_type;

        // copy original file
        File::copyDirectory(Storage::disk('public') -> path($orig_uploads_path), Storage::disk('public') -> path($new_uploads_path));

        exec('chmod 0777 '.Storage::disk('public') -> path('doc_management/transactions/'.$path));

        // add file_location to upload

        $upload_copy -> file_location = '/storage/'.$new_uploads_path.'/'.$upload -> file_name;
        $upload_copy -> save();

        // add file location to doc images
        $document_images = TransactionDocumentsImages::where('document_id', $new_document_id) -> get();

        foreach ($document_images as $document_image) {
            $new_file_location = str_replace($orig_upload_id.'_'.$file_type, $new_upload_id.'_'.$file_type, $document_image -> file_location);
            $document_image -> file_location = $new_file_location;
            $document_image -> save();
        }

        // add other details to docs
        $document_copy -> file_location = '/storage/'.$new_uploads_path.'/'.$upload -> file_name;
        $document_copy -> file_location_converted = '/storage/'.$new_uploads_path.'/converted/'.$upload -> file_name;
        $document_copy -> file_name_display = $upload -> file_name_display.'-COPY';
        $document_copy -> file_id = $new_upload_id;
        //$document_copy -> order = $document_copy -> order + 1;
        $document_copy -> assigned = 'no';
        $document_copy -> save();

        $new_document_id = $document_copy -> id;

        // update uploads with new doc id
        $upload_copy -> Transaction_Docs_ID = $new_document_id;
        $upload_copy -> save();

        // copy all pages, images, fields and field values
        $data_sets = [TransactionUploadImages::where('file_id', $orig_upload_id) -> get(), TransactionUploadPages::where('file_id', $orig_upload_id) -> get()];

        foreach ($data_sets as $data_set) {

            foreach ($data_set as $row) {
                $copy = $row -> replicate();
                $copy -> file_id = $new_upload_id;
                $path = str_replace('/'.$orig_upload_id.'/', '/'.$new_upload_id.'_'.$file_type.'/', $row -> file_location);
                $copy -> file_location = $path;
                $copy -> save();
            }

        }

        $user_fields = UserFields::where('file_id', $orig_upload_id) -> get();

        foreach ($user_fields as $user_field) {
            $copy = $user_field -> replicate();
            $copy -> file_id = $new_upload_id;
            $copy -> save();
            $new_user_field_id = $copy -> id;

            $user_fields_inputs = UserFieldsInputs::where('transaction_field_id', $user_field -> id) -> get();

            foreach ($user_fields_inputs as $user_fields_input) {
                $copy = $user_fields_input -> replicate();
                $copy -> file_id = $new_upload_id;
                $copy -> transaction_field_id = $new_user_field_id;
                $copy -> save();
            }
        }



        // add input values
        /* $field_input_values = UserFieldsValues::where('file_id', $orig_upload_id) -> get();

        foreach ($field_input_values as $field_input_value) {
            $copy = $field_input_value -> replicate();
            $copy -> file_id = $new_upload_id;
            $copy -> file_type = $file_type;
            $copy -> Agent_ID = $Agent_ID;
            $copy -> Listing_ID = $Listing_ID;
            $copy -> Contract_ID = $Contract_ID;
            $copy -> Referral_ID = $Referral_ID;
            $copy -> save();
        } */

    }

    public function email_get_documents(Request $request) {

        $docs_type = $request -> docs_type;

        $filenames = [];
        $file_locations = [];

        if($docs_type != '') {

            $file = $this -> merge_documents($request);

            // when multiple docs are emailed
            if($docs_type == 'merged') {
                $file_locations[] = str_replace('/storage/', '', $file['file_location']);
                $filenames[] = $file['filename'];
            } else if($docs_type == 'single') {
                foreach ($file['single_documents'] as $doc) {
                    $file_locations[] = str_replace('/storage/', '', $doc -> file_location_converted);
                    $filenames[] = $doc -> file_name_display;
                }

            }

        } else {
            // when a single doc is emailed
            $doc = TransactionDocuments::where('id', $request -> document_ids) -> first();
            $file_locations[] = str_replace('/storage/', '', $doc -> file_location_converted);
            $filenames[] = $doc -> file_name_display;

        }

        return compact('file_locations', 'filenames');

    }

    public function get_split_document_html(Request $request) {

        $transaction_type = $request -> transaction_type;
        $checklist_id = $request -> checklist_id;
        $document_id = $request -> document_id;
        $document = TransactionDocuments::where('id', $document_id) -> first();
        $file_id = $document -> file_id;
        $file_type = $request -> file_type;
        $file_name = $request -> file_name;

        $document_images = TransactionUploadImages::where('file_id', $file_id) -> orderBy('page_number') -> get();

        $checklist_items_model = new ChecklistsItems();
        $transaction_checklist_items_modal = new TransactionChecklistItems();
        $checklist_items = $transaction_checklist_items_modal -> where('checklist_id', $checklist_id) -> get();

        $transaction_checklist_item_documents = TransactionChecklistItemsDocs::where('checklist_id', $checklist_id) -> get();

        $checklist_types = ['listing', 'both'];

        if($transaction_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } else if($transaction_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/details/data/get_split_document_html', compact('document_id', 'file_id', 'file_type', 'file_name', 'document', 'document_images', 'checklist_items', 'checklist_groups', 'transaction_checklist_item_documents', 'checklist_items_model', 'transaction_checklist_items_modal'));
    }

    public function merge_documents(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $folder_id = $request -> folder_id;
        $type = $request -> type;
        $docs_type = $request -> docs_type;

        $property = Listings::GetPropertyDetails($transaction_type, [$Listing_ID, $Contract_ID, $Referral_ID]);

        // create filename for merged docs
        $filename = sanitize($property -> FullStreetAddress).'_'.date('YmdHis').'.pdf';

        $document_ids = explode(',', $request -> document_ids);
        $documents = [];

        foreach ($document_ids as $document_id) {

            if($type == 'filled') {
                $documents[] = TransactionDocuments::where('id', $document_id) -> pluck('file_location_converted') -> first();
                $single_documents[] = TransactionDocuments::select('file_location_converted', 'file_name_display') -> where('id', $document_id) -> first();
            } else if($type == 'blank') {
                $documents[] = TransactionDocuments::where('id', $document_id) -> pluck('file_location') -> first();
            }

        }

        $docs_array = array_map(array($this, 'get_path'), $documents);
        $docs = implode(' ', $docs_array);

        $tmp = Storage::disk('public') -> path('tmp');
        exec('pdftk '.$docs.' cat output '.$tmp.'/'.$filename);

        $file_location = '/storage/tmp/'.$filename;
        return compact('file_location', 'filename', 'single_documents');

    }

    public function move_documents_to_folder(Request $request) {

        $folder_id = $request -> folder_id;
        $document_ids = explode(',', $request -> document_ids);
        $update_folder = TransactionDocuments::whereIn('id', $document_ids) -> update(['folder' => $folder_id]);

    }

    public function move_documents_to_trash(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;

        if($transaction_type == 'listing') {
            $trash_folder = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID);
        } else if($transaction_type == 'contract') {
            $trash_folder = TransactionDocumentsFolders::where('Contract_ID', $Contract_ID);
        } else if($transaction_type == 'referral') {
            $trash_folder = TransactionDocumentsFolders::where('Referral_ID', $Referral_ID);
        }

        $trash_folder = $trash_folder -> where('folder_name', 'Trash') -> first();

        $document_ids = explode(',', $request -> document_ids);
        $update_folder = TransactionDocuments::whereIn('id', $document_ids) -> update(['folder' => $trash_folder -> id]);
    }

    public function reorder_documents(Request $request) {
        $data = json_decode($request['data'], true);
        $data = $data['document'];

        foreach ($data as $item) {
            $document_id = $item['document_id'];
            $folder = $item['folder_id'];
            $document_order = $item['document_index'];
            $reorder = TransactionDocuments::where('id', $document_id) -> first();
            $reorder -> doc_order = $document_order;
            $reorder -> folder = $folder;
            $reorder -> save();
        }

        return true;

    }

    public function save_add_template_documents(Request $request) {
        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;
        $folder = $request -> folder;

        $files = json_decode($request['files'], true);

        $checklist_item_docs_model = new TransactionChecklistItemsDocs();

        foreach ($files as $file) {

            $file_id = $file['file_id'];
            $add_documents = new TransactionDocuments();
            $add_documents -> Agent_ID = $Agent_ID;

            if($transaction_type == 'contract') {
                $add_documents -> Contract_ID = $Contract_ID;
            } else if($transaction_type == 'listing') {
                $add_documents -> Listing_ID = $Listing_ID;
            } else if($transaction_type == 'referral') {
                $add_documents -> Referral_ID = $Referral_ID;
            }

            $add_documents -> folder = $folder;
            $add_documents -> doc_order = $file['order'];
            $add_documents -> orig_file_id = $file_id;
            $add_documents -> template_id = $file['template_id'];
            $add_documents -> file_type = 'system';
            $add_documents -> file_name = $file['file_name'];
            $add_documents -> file_name_display = $file['file_name_display'];
            $add_documents -> pages_total = $file['pages_total'];
            $add_documents -> file_location = $file['file_location'];
            $add_documents -> page_width = $file['page_width'];
            $add_documents -> page_height = $file['page_height'];
            $add_documents -> page_size = $file['page_size'];
            $add_documents -> transaction_type = $transaction_type;
            $add_documents -> save();

            $new_document_id = $add_documents -> id;

            $upload = Upload::where('file_id', $file_id) -> first();

            // create new upload
            $upload_copy = $upload -> replicate();
            $upload_copy -> orig_file_id = $file_id;
            $upload_copy -> file_type = 'system';
            $upload_copy -> Transaction_Docs_ID = $new_document_id;
            $upload_copy -> file_name_display = $upload -> file_name_display;
            $upload_copy -> Agent_ID = $Agent_ID;
            $upload_copy -> Listing_ID = $Listing_ID;
            $upload_copy -> Contract_ID = $Contract_ID;
            $upload_copy -> Referral_ID = $Referral_ID;
            $upload_copy -> page_width = $file['page_width'];
            $upload_copy -> page_height = $file['page_height'];
            $upload_copy -> page_size = $file['page_size'];
            $upload_new = $upload_copy -> toArray();
            $upload_new = TransactionUpload::create($upload_new);

            $new_file_id = $upload_new -> file_id;

            // update file_id in docs
            $add_documents -> file_id = $new_file_id;
            $add_documents -> save();

            $base_path = base_path();
            $storage_path = $base_path.'/storage/app/public/';

            if($transaction_type == 'listing') {
                $path = 'listings/'.$Listing_ID;
            } else if($transaction_type == 'contract') {
                $path = 'contracts/'.$Contract_ID;
            } else {
                $path = 'referrals/'.$Referral_ID;
            }

            $files_path = 'doc_management/transactions/'.$path.'/'.$new_file_id;

            $copy_from = $storage_path.'doc_management/uploads/'.$file_id.'/*';
            $copy_to = $storage_path . $files_path.'_system';
            Storage::disk('public') -> makeDirectory($files_path.'_system/converted');
            Storage::disk('public') -> makeDirectory($files_path.'_system/converted_images');
            Storage::disk('public') -> makeDirectory($files_path.'_system/layers');
            Storage::disk('public') -> makeDirectory($files_path.'_system/combined');

            exec('chmod 0777 '.Storage::disk('public') -> path('doc_management/transactions/'.$path));

            $copy = exec('cp -rp '.$copy_from.' '.$copy_to);
            $copy_converted = exec('cp '.$storage_path . $files_path.'_system/'.$file['file_name'].' '.$copy_to.'/converted/'.$file['file_name']);

            $filename = $file['file_name'];
            $image_filename = str_replace('.pdf', '.jpg', $file['file_name']);
            $source = $copy_to.'/converted/'.$filename;
            $destination = $copy_to.'/converted_images';
            $checklist_item_docs_model -> convert_doc_to_images($source, $destination, $image_filename, $new_document_id);

            $add_documents -> file_location = '/storage/'.$files_path.'_system/'.$filename;
            $add_documents -> file_location_converted = '/storage/'.$files_path.'_system/converted/'.$filename;
            $add_documents -> save();

            $upload_images = UploadImages::where('file_id', $file_id) -> get();
            $upload_pages = UploadPages::where('file_id', $file_id) -> get();

            foreach ($upload_images as $upload_image) {
                $copy = $upload_image -> replicate();
                $copy -> file_id = $new_file_id;
                $new_path = str_replace('/uploads/'.$file_id.'/', '/transactions/'.$path.'/'.$new_file_id.'_system/', $upload_image -> file_location);
                $copy -> file_location = $new_path;
                $copy -> Agent_ID = $Agent_ID;
                $copy -> Listing_ID = $Listing_ID;
                $copy -> Contract_ID = $Contract_ID;
                $copy -> Referral_ID = $Referral_ID;
                $new = $copy -> toArray();
                TransactionUploadImages::create($new);
            }

            foreach ($upload_pages as $upload_page) {
                $copy = $upload_page -> replicate();
                $copy -> file_id = $new_file_id;
                $new_path = str_replace('/uploads/'.$file_id.'/', '/transactions/'.$path.'/'.$new_file_id.'_user/', $upload_page -> file_location);
                $copy -> file_location = $new_path;
                $copy -> Agent_ID = $Agent_ID;
                $copy -> Listing_ID = $Listing_ID;
                $copy -> Contract_ID = $Contract_ID;
                $copy -> Referral_ID = $Referral_ID;
                $new = $copy -> toArray();
                TransactionUploadPages::create($new);
            }

            AddFieldAndInputs::dispatch($file_id, $new_file_id, $Agent_ID, $Listing_ID, $Contract_ID, $Referral_ID, $transaction_type, 'system');


            /* $fields = Fields::where('file_id', $file_id) -> with('common_field') -> get();

            foreach ($fields as $field) {

                AddFieldAndInputs::dispatch($property, $field, $new_file_id, $Agent_ID, $Listing_ID, $Contract_ID, $Referral_ID, $transaction_type, 'system');

                //$this -> add_field_and_inputs($property, $field, $new_file_id, $Agent_ID, $Listing_ID, $Contract_ID, $Referral_ID, $transaction_type, 'system');

            } */

        }

    }

    public function add_field_and_inputs($property, $field, $new_file_id, $Agent_ID, $Listing_ID, $Contract_ID, $Referral_ID, $transaction_type, $file_type) {

        $field_type = $field -> field_type;
        $field_category = $field -> field_category;

        $field_inputs = 'no';
        if($field_type == 'address' || ($field_type == 'name' && preg_match('/(Renter|Owner)/', $field -> field_name))) {
            $field_inputs = 'yes';
        }

        if($field_type == '') {
            $field_type = $field_category;
        }

        $new_field = new UserFields();
        $new_field -> file_id = $new_file_id;

        $new_field -> common_field_id = $field -> common_field_id;
        $new_field -> create_field_id = $field -> field_id;
        $new_field -> group_id = $field -> group_id;
        $new_field -> page = $field -> page;
        $new_field -> field_category = $field_category;
        $new_field -> field_type = $field_type;
        //$new_field -> field_created_by = 'system'; this is the default value
        $new_field -> field_name = $field -> field_name;
        $new_field -> field_name_display = $field -> field_name_display;
        $new_field -> field_name_type = $field -> field_name_type;
        $new_field -> number_type = $field -> number_type;
        $new_field -> field_sub_group_id = $field -> field_sub_group_id;
        $new_field -> top_perc = $field -> top_perc;
        $new_field -> left_perc = $field -> left_perc;
        $new_field -> width_perc = $field -> width_perc;
        $new_field -> height_perc = $field -> height_perc;

        $new_field -> Agent_ID = $Agent_ID;
        $new_field -> Listing_ID = $Listing_ID;
        $new_field -> Contract_ID = $Contract_ID;
        $new_field -> Referral_ID = $Referral_ID;
        $new_field -> transaction_type = $transaction_type;
        $new_field -> file_type = $file_type;
        $new_field -> field_inputs = $field_inputs;

        $new_field -> save();

        $new_field_id = $new_field -> id;


        $for_sale = $property -> SaleRent == 'sale' || $property -> SaleRent == 'both' ? 'yes' : 'no';

        // add inputs
        // if $field_inputs == 'yes' there will be 2 or 4/5 inputs, otherwise just one
        if($field_inputs == 'yes') {

            $sub_group_title = CommonFieldsSubGroups::GetSubGroupTitle($new_field -> field_sub_group_id);
            if($sub_group_title == '') {
                $sub_group_title = 'Property';
            }

            if($new_field -> field_type == 'name') {

                if(preg_match('/Buyer/', $sub_group_title)) {
                    $name_type = $for_sale == 'yes' ? 'Buyer' : 'Renter';
                    $input_name_one_display = $name_type.' One Name';
                    $input_name_one_db_column = 'BuyerOneFullName';
                    $input_name_two_display = $name_type.' Two Name';
                    $input_name_two_db_column = 'BuyerTwoFullName';
                } else if(preg_match('/Seller/', $sub_group_title)) {
                    $name_type = $for_sale == 'yes' ? 'Seller' : 'Owner';
                    $input_name_one_display = $name_type.' One Name';
                    $input_name_one_db_column = 'SellerOneFullName';
                    $input_name_two_display = $name_type.' Two Name';
                    $input_name_two_db_column = 'SellerTwoFullName';
                }

                $input_one = new UserFieldsInputs();
                $input_one -> file_id = $new_field -> file_id;
                $input_one -> group_id = $new_field -> group_id;
                $input_one -> file_type = $new_field -> file_type;
                $input_one -> field_type = $new_field -> field_type;
                $input_one -> transaction_field_id = $new_field -> id;
                $input_one -> input_name_display = $input_name_one_display;
                $input_one -> input_db_column = $input_name_one_db_column;
                $input_one -> Agent_ID = $new_field -> Agent_ID;
                $input_one -> Listing_ID = $new_field -> Listing_ID;
                $input_one -> Contract_ID = $new_field -> Contract_ID;
                $input_one -> Referral_ID = $new_field -> Referral_ID;
                $input_one -> transaction_type = $new_field -> transaction_type;
                $input_one -> save();

                $input_two = $input_one -> replicate();
                $input_two -> input_name_display = $input_name_two_display;
                $input_two -> input_db_column = $input_name_two_db_column;
                $input_two -> save();

            } else if($new_field -> field_type == 'address') {

                // using Renter and Owner to find Buyer or Renter/Seller or Owner because 'Buyer' matches Buyer Agent
                if(preg_match('/Renter/', $sub_group_title)) {

                    $name_type = $for_sale == 'yes' ? 'Buyer' : 'Renter';
                    // get name type to match db columns
                    if(preg_match('/One/', $sub_group_title)) {
                        $db_type = 'BuyerOne';
                    } else if(preg_match('/Two/', $sub_group_title)) {
                        $db_type = 'BuyerTwo';
                    } else if(preg_match('/Both/', $sub_group_title)) {
                        $db_type = 'BuyerOne';
                        $db_name = 'Buyer';
                    }

                } else if(preg_match('/Owner/', $sub_group_title)) {

                    $name_type = $for_sale == 'yes' ? 'Seller' : 'Owner';

                    // get name type to match db columns
                    if(preg_match('/One/', $sub_group_title)) {
                        $db_type = 'SellerOne';
                    } else if(preg_match('/Two/', $sub_group_title)) {
                        $db_type = 'SellerTwo';
                    } else if(preg_match('/Both/', $sub_group_title)) {
                        $db_type = 'SellerOne';
                        $db_name = 'Seller';
                    }

                } else if(preg_match('/Office/', $sub_group_title)) {
                    $name_type = 'List Agent Office';
                    $db_type = 'ListOffice';
                    if(preg_match('/Buyer/', $sub_group_title)) {
                        $name_type = 'Buyer Agent Office';
                        $db_type = 'BuyerOffice';
                    }
                } else {
                    $name_type = $sub_group_title;
                    $db_type = str_replace('Property', '', $sub_group_title);
                }

                $input_address_one_display = $name_type.' Street Address';
                $input_address_one_db_column = $db_type.'FullStreetAddress';
                $input_address_two_display = $name_type.' City';
                $input_address_two_db_column = $db_type.'City';
                $input_address_three_display = $name_type.' State';
                $input_address_three_db_column = $db_type.'StateOrProvince';
                $input_address_four_display = $name_type.' Zip';
                $input_address_four_db_column = $db_type.'PostalCode';
                if($sub_group_title == 'Property') {
                    $input_address_five_display = 'Property County';
                    $input_address_five_db_column = $db_type.'County';
                }

                $input_one = new UserFieldsInputs();
                $input_one -> file_id = $new_field -> file_id;
                $input_one -> group_id = $new_field -> group_id;
                $input_one -> file_type = $new_field -> file_type;
                $input_one -> field_type = $new_field -> field_type;
                $input_one -> transaction_field_id = $new_field -> id;
                $input_one -> input_name_display = $input_address_one_display;
                $input_one -> input_db_column = $input_address_one_db_column;
                $input_one -> Agent_ID = $new_field -> Agent_ID;
                $input_one -> Listing_ID = $new_field -> Listing_ID;
                $input_one -> Contract_ID = $new_field -> Contract_ID;
                $input_one -> Referral_ID = $new_field -> Referral_ID;
                $input_one -> transaction_type = $new_field -> transaction_type;
                $input_one -> save();

                $input_two = $input_one -> replicate();
                $input_two -> input_name_display = $input_address_two_display;
                $input_two -> input_db_column = $input_address_two_db_column;
                $input_two -> save();

                $input_three = $input_one -> replicate();
                $input_three -> input_name_display = $input_address_three_display;
                $input_three -> input_db_column = $input_address_three_db_column;
                $input_three -> save();

                $input_four = $input_one -> replicate();
                $input_four -> input_name_display = $input_address_four_display;
                $input_four -> input_db_column = $input_address_four_db_column;
                $input_four -> save();

                if($sub_group_title == 'Property') {
                    $input_five = $input_one -> replicate();
                    $input_five -> input_name_display = $input_address_five_display;
                    $input_five -> input_db_column = $input_address_five_db_column;
                    $input_five -> save();
                }

            }

        } else {

            $common_field = CommonFields::find($new_field -> common_field_id);
            $input_db_column = $common_field ? $common_field -> db_column_name : '';

            $input = new UserFieldsInputs();
            $input -> file_id = $new_field -> file_id;
            $input -> group_id = $new_field -> group_id;
            $input -> file_type = $new_field -> file_type;
            $input -> field_type = $new_field -> field_type;
            $input -> number_type = $new_field -> number_type;
            $input -> transaction_field_id = $new_field -> id;
            $input -> input_name_display = $new_field -> field_name_display;
            $input -> input_db_column = $input_db_column;
            $input -> Agent_ID = $new_field -> Agent_ID;
            $input -> Listing_ID = $new_field -> Listing_ID;
            $input -> Contract_ID = $new_field -> Contract_ID;
            $input -> Referral_ID = $new_field -> Referral_ID;
            $input -> transaction_type = $new_field -> transaction_type;
            $input -> save();

        }

        // add values for common fields
        $inputs = UserFieldsInputs::where('transaction_field_id', $new_field -> id)
            -> whereNotNull('input_db_column')
            -> get();

        foreach($inputs as $input) {
            $column = $input -> input_db_column;
            $value = $property -> $column;
            $input -> input_value = $value;
            $input -> save();
        }

        return response() -> json(['status' => 'success']);

    }

    public function save_assign_documents_to_checklist(Request $request) {

        $checklist_items = json_decode($request['checklist_items']);

        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;
        $release_submitted = false;

        foreach ($checklist_items as $checklist_item) {

            $checklist_id = $checklist_item -> checklist_id;
            $checklist_item_id = $checklist_item -> checklist_item_id;
            $document_id = $checklist_item -> document_id;

            $checklist_item_details = TransactionChecklistItems::where('id', $checklist_item_id) -> first();
            $checklist_form_id = $checklist_item_details -> checklist_form_id;

            $add_checklist_item_doc = new TransactionChecklistItemsDocs();
            $add_checklist_item_doc -> document_id = $document_id;
            $add_checklist_item_doc -> checklist_id = $checklist_id;
            $add_checklist_item_doc -> checklist_item_id = $checklist_item_id;
            $add_checklist_item_doc -> Agent_ID = $Agent_ID;

            if($transaction_type == 'listing') {
                $add_checklist_item_doc -> Listing_ID = $Listing_ID;
            } else if($transaction_type == 'contract') {
                $add_checklist_item_doc -> Contract_ID = $Contract_ID;
            } else if($transaction_type == 'referral') {
                $add_checklist_item_doc -> Referral_ID = $Referral_ID;
            }

            $add_checklist_item_doc -> save();

            $update_docs = TransactionDocuments::where('id', $document_id) -> update(['assigned' => 'yes', 'checklist_item_id' => $checklist_item_id]);
            $update_checklist_item = TransactionChecklistItems::where('id', $checklist_item_id) -> update(['checklist_item_status' => 'not_reviewed']);

            // if release is submitted
            if($transaction_type == 'contract') {

                if(Upload::IsRelease($checklist_form_id)) {

                    $contract = Contracts::find($Contract_ID);
                    $contract -> Status = ResourceItems::GetResourceID('Cancel Pending', 'contract_status');
                    $contract -> save();

                    $release_submitted = true;

                }

            }

        }

        if($release_submitted == true) {
            // TODO: notify delia

            return response() -> json([
                'release_submitted' => 'yes'
            ]);
        }

    }

    public function save_rename_document(Request $request) {

        $new_name = $request -> new_name;
        $document_id = $request -> document_id;
        $document = TransactionDocuments::where('id', $document_id) -> first();

        $file_name = sanitize(str_replace('.pdf', '', $new_name)).'.pdf';
        $file_name_display = str_replace('.pdf', '', $new_name).'.pdf';
        $file_location = str_replace($document -> file_name, $file_name, $document -> file_location);
        $file_location_converted = str_replace($document -> file_name, $file_name, $document -> file_location_converted);

        File::move($this -> get_path($document -> file_location), $this -> get_path($file_location));
        File::move($this -> get_path($document -> file_location_converted), $this -> get_path($file_location_converted));

        $transaction_upload = TransactionUpload::where('Transaction_Docs_ID', $document_id)
            -> update([
                'file_name_display' => $new_name,
                'file_name_display' => $file_name_display,
                'file_location' => $file_location,
                'file_name' => $file_name,
            ]);

        $transaction_document = TransactionDocuments::where('id', $document_id) -> update(['file_name_display' => $new_name]);

        $document -> file_name = $file_name;
        $document -> file_name_display = $file_name_display;
        $document -> file_location = $file_location;
        $document -> file_location_converted = $file_location_converted;
        $document -> save();

        return true;
    }

    public function save_split_document(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;

        $folder_id = $request -> folder_id;
        $document_name = $request -> document_name;
        if(preg_match('/^[0-9]*$/', $document_name) && $document_name > 0) {
            $document_name = Upload::GetFormName($document_name);
        }

        $document_name = preg_replace('/\.pdf/', '', $document_name);

        $image_ids = explode(',', $request -> image_ids);
        $pages_total = count($image_ids);
        $file_type = $request -> file_type;
        $file_id = $request -> file_id;
        $checklist_item_id = $request -> checklist_item_id;
        $checklist_id = $request -> checklist_id;

        $document_images = TransactionUploadImages::whereIn('id', $image_ids) -> get();

        $document_image_files = [];
        $document_page_files = [];
        $page_numbers = [];

        foreach ($document_images as $document_image) {

            $doc_file_id = $document_image -> file_id;
            $doc_page_number = $document_image -> page_number;
            $page_numbers[] = $doc_page_number;

            $pages = [];
            $images = [];

            $document_page = TransactionUploadPages::where('file_id', $doc_file_id) -> where('page_number', $doc_page_number) -> first();
            $pages = ['file_id' => $document_page -> file_id, 'file_location' => $document_page -> file_location];
            $images = ['file_id' => $document_image -> file_id, 'file_location' => $document_image -> file_location, 'page_number' => $doc_page_number];

            array_push($document_page_files, $pages);
            array_push($document_image_files, $images);

        }

        // if manually saving to documents
        if($document_name) {
            $file_name = sanitize($document_name).'.pdf';
            $file_name_display = $document_name.'.pdf';

        // if adding to checklist item
        // assign to checklist item
        } else {
            $checklist_item = TransactionChecklistItems::where('id', $checklist_item_id) -> first();
            $checklist_form_id = $checklist_item -> checklist_form_id;
            $file_name_display = Upload::GetFormName($checklist_form_id);
            $file_name = sanitize($file_name_display).'.pdf';
        }

        // add to docs_transaction_docs
        $add_document = new TransactionDocuments();
        $add_document -> file_type = 'user';
        $add_document -> Agent_ID = $Agent_ID;
        $add_document -> Listing_ID = $Listing_ID;
        $add_document -> Contract_ID = $Contract_ID;
        $add_document -> Referral_ID = $Referral_ID;
        $add_document -> transaction_type = $transaction_type;
        $add_document -> folder = $folder_id;
        $add_document -> file_name = $file_name;
        $add_document -> file_name_display = $file_name_display;
        $add_document -> pages_total = $pages_total;
        $add_document -> save();
        $Transaction_Docs_ID = $add_document -> id;

        // add to transaction uploads
        $upload = new TransactionUpload();
        $upload -> Transaction_Docs_ID = $Transaction_Docs_ID;
        $upload -> Agent_ID = $Agent_ID;
        $upload -> Listing_ID = $Listing_ID;
        $upload -> Contract_ID = $Contract_ID;
        $upload -> Referral_ID = $Referral_ID;
        $upload -> file_name = $file_name;
        $upload -> file_name_display = $file_name_display;
        $upload -> pages_total = $pages_total;
        $upload -> save();
        $new_file_id = $upload -> file_id;

        $add_document -> file_id = $new_file_id;
        $add_document -> save();

        if($transaction_type == 'contract') {
            $path = 'contracts/'.$Contract_ID;
        } else if($transaction_type == 'listing') {
            $path = 'listings/'.$Listing_ID;
        } else if($transaction_type == 'referral') {
            $path = 'referral/'.$Referral_ID;
        }

        $files_path = 'doc_management/transactions/'.$path.'/'.$new_file_id.'_user';

        Storage::disk('public') -> makeDirectory($files_path.'/images');
        Storage::disk('public') -> makeDirectory($files_path.'/pages');

        // copy images and pages and create merged file
        // copy images
        $page_number = 1;

        foreach ($document_image_files as $image_file) {
            $image_file_name = basename($image_file['file_location']);
            $old_file_loc = Storage::disk('public') -> path('doc_management/transactions/'.$path.'/'.$document_image_files[0]['file_id'].'_'.$file_type.'/images/'.$image_file_name);
            $new_file_loc = Storage::disk('public') -> path($files_path.'/images/'.$image_file_name);
            exec('cp '.$old_file_loc.' '.$new_file_loc);

            $upload_images = new TransactionUploadImages();
            $upload_images -> file_id = $new_file_id;
            $upload_images -> Agent_ID = $Agent_ID;
            $upload_images -> Listing_ID = $Listing_ID;
            $upload_images -> Contract_ID = $Contract_ID;
            $upload_images -> Referral_ID = $Referral_ID;
            $upload_images -> file_name = $file_name;
            $upload_images -> file_location = '/storage/'.$files_path.'_user/images/'.$image_file_name;
            $upload_images -> pages_total = count($document_image_files);
            $upload_images -> page_number = $page_number;
            $upload_images -> save();

            // copy from docs_transaction_fields ** update new page for each
            $add_user_fields = UserFields::where('file_id', $image_file['file_id']) -> where('page', $image_file['page_number']) -> get();
            $field_ids = [];

            foreach ($add_user_fields as $add_user_field) {
                // TODO: field_id probably not right
                $field_ids[] = $add_user_field -> common_field_id;
                $add_user_fields_copy = $add_user_field -> replicate();
                $add_user_fields_copy -> page = $page_number;
                $add_user_fields_copy -> file_type = 'user';
                $add_user_fields_copy -> file_id = $new_file_id;
                $add_user_fields_copy -> save();
            }

            $user_fields_inputs = UserFieldsInputs::where('file_id', $image_file['file_id']) -> get();

            foreach ($user_fields_inputs as $user_fields_input) {
                $add_user_fields_input_copy = $user_fields_input -> replicate();
                $add_user_fields_input_copy -> file_id = $new_file_id;
                $add_user_fields_input_copy -> file_type = 'user';
                $add_user_fields_input_copy -> save();
            }

            // copy from docs_transaction_fields_inputs_values
            /* $add_user_field_values = UserFieldsValues::whereIn('input_id', $field_ids) -> get();

            foreach ($add_user_field_values as $add_user_field_value) {
                $add_user_field_values_copy = $add_user_field_value -> replicate();
                $add_user_field_values_copy -> file_type = 'user';
                $add_user_field_values_copy -> file_id = $new_file_id;
                $add_user_field_values_copy -> save();
            } */

            $page_number += 1;

        }

        // copy pages
        $page_number = 1;

        foreach ($document_page_files as $page_file) {
            $page_file_name = basename($page_file['file_location']);
            $old_file_loc = Storage::disk('public') -> path('doc_management/transactions/'.$path.'/'.$document_page_files[0]['file_id'].'_'.$file_type.'/pages/'.$page_file_name);
            $new_file_loc = Storage::disk('public') -> path($files_path.'/pages/'.$page_file_name);
            exec('cp '.$old_file_loc.' '.$new_file_loc);

            $upload_pages = new TransactionUploadPages();
            $upload_pages -> file_id = $new_file_id;
            $upload_pages -> Agent_ID = $Agent_ID;
            $upload_pages -> Listing_ID = $Listing_ID;
            $upload_pages -> Contract_ID = $Contract_ID;
            $upload_pages -> Referral_ID = $Referral_ID;
            $upload_pages -> file_name = $file_name;
            $upload_pages -> file_location = '/storage/'.$files_path.'/pages/'.$page_file_name;
            $upload_pages -> pages_total = count($document_page_files);
            $upload_pages -> page_number = $page_number;
            $upload_pages -> save();
            $page_number += 1;
        }

        //merge pages into main file and move to converted
        $main_file_location = $files_path.'/'.$file_name;
        $converted_file_location = $files_path.'/converted/'.$file_name;

        $base_path = base_path();
        exec('mkdir '.$base_path.'/storage/app/public/'.$files_path.'/converted');
        exec('mkdir '.$base_path.'/storage/app/public/'.$files_path.'/converted_images');

        // merge all pages and add to main directory and converted directory
        $pages = Storage::disk('public') -> path($files_path.'/pages');
        exec('pdftk '.$pages.'/*.pdf cat output '.$base_path.'/storage/app/public/'.$main_file_location);

        //exec('cd '.$base_path.'/storage/app/public/ && cp '.$main_file_location.' '.$converted_file_location);
        // get split pages, merge and add to converted
        $old_converted_location = Storage::disk('public') -> path('doc_management/transactions/'.$path.'/'.$file_id.'_'.$file_type.'/converted');
        $new_converted_location = Storage::disk('public') -> path($files_path.'/converted');
        $new_converted_images_location = Storage::disk('public') -> path($files_path.'/converted_images');

        exec('pdftk '.$old_converted_location.'/*.pdf cat '.implode(' ', $page_numbers).' output '.$new_converted_location.'/'.$file_name);

        $checklist_item_docs_model = new TransactionChecklistItemsDocs();
        $image_filename = str_replace('.pdf', '.jpg', $file_name);
        $source = $new_converted_location.'/'.$file_name;
        $destination = $new_converted_images_location;
        $checklist_item_docs_model -> convert_doc_to_images($source, $destination, $image_filename, $Transaction_Docs_ID);

        // update file locations in docs_transaction and docs uploads
        $add_document -> file_location = '/storage/'.$main_file_location;
        $add_document -> file_location_converted = '/storage/'.$converted_file_location;
        $add_document -> save();

        $upload -> file_location = '/storage/'.$main_file_location;
        $upload -> save();

        // add to checklist
        if($checklist_id > 0) {
            $document_id = $Transaction_Docs_ID;

            $add_checklist_item_doc = new TransactionChecklistItemsDocs();
            $add_checklist_item_doc -> document_id = $document_id;
            $add_checklist_item_doc -> checklist_id = $checklist_id;
            $add_checklist_item_doc -> checklist_item_id = $checklist_item_id;
            $add_checklist_item_doc -> Agent_ID = $Agent_ID;
            if($transaction_type == 'listing') {
                $add_checklist_item_doc -> Listing_ID = $Listing_ID;
            } else if($transaction_type == 'contract') {
                $add_checklist_item_doc -> Contract_ID = $Contract_ID;
            } else if($transaction_type == 'referral') {
                $add_checklist_item_doc -> Referral_ID = $Referral_ID;
            }

            $add_checklist_item_doc -> save();

            $update_docs = TransactionDocuments::where('id', $document_id) -> update(['assigned' => 'yes', 'checklist_item_id' => $checklist_item_id]);
            $update_checklist_item = TransactionChecklistItems::where('id', $checklist_item_id) -> update(['checklist_item_status' => 'not_reviewed']);

        }

    }

    public function upload_documents(Request $request) {

        $file = $request -> file('file');
        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;
        $folder = $request -> folder;

        if($file) {

            $ext = $file -> getClientOriginalExtension();
            $file_name = $file -> getClientOriginalName();


            $file_name_remove_numbers = preg_replace('/[0-9-_\s\.]+\.'.$ext.'/', '.'.$ext, $file_name);
            $file_name_remove_numbers = preg_replace('/^[0-9-_\s\.]+/', '', $file_name_remove_numbers);
            $file_name_display = preg_replace('/-/', ' ', $file_name_remove_numbers);
            $file_name_no_ext = str_replace('.'.$ext, '', $file_name_remove_numbers);
            $clean_file_name = sanitize($file_name_no_ext);
            $new_file_name = $clean_file_name.'_'.date('YmdHis').'.'.$ext;

            // convert to pdf if image
            if($ext != 'pdf') {
                $new_file_name = date('YmdHis').'_'.$clean_file_name.'.pdf';
                $file_name_display = $file_name_no_ext.'.pdf';
                $create_images = exec('convert -quality 80 -density 200 -page letter '.$file.' /tmp/'.$new_file_name, $output, $return);
                $file = '/tmp/'.$new_file_name;
            }

            $pages_total = exec('pdftk '.$file.' dump_data | sed -n \'s/^NumberOfPages:\s//p\'');

            $page_width = get_width_height($file)['width'];
            $page_height = get_width_height($file)['height'];

            $page_size = '';
            if($page_width == 612 && $page_height == 792) {
                $page_size = 'letter';
            } else if($page_width == 595 && $page_height == 842) {
                $page_size = 'a4';
            }


            // add to Documents
            $add_documents = new TransactionDocuments();
            $add_documents -> file_type = 'user';
            $add_documents -> Agent_ID = $Agent_ID;
            $add_documents -> Listing_ID = $Listing_ID;
            $add_documents -> Contract_ID = $Contract_ID;
            $add_documents -> Referral_ID = $Referral_ID;
            $add_documents -> transaction_type = $transaction_type;
            $add_documents -> folder = $folder;
            $add_documents -> file_name = $new_file_name;
            $add_documents -> file_name_display = $file_name_display;
            $add_documents -> pages_total = $pages_total;
            $add_documents -> page_width = $page_width;
            $add_documents -> page_height = $page_height;
            $add_documents -> page_size = $page_size;
            $add_documents -> doc_order = 0;
            $add_documents -> save();
            $Transaction_Docs_ID = $add_documents -> id;

            // add original file to uploads
            $upload = new TransactionUpload();
            $upload -> Transaction_Docs_ID = $Transaction_Docs_ID;
            $upload -> Agent_ID = $Agent_ID;
            $upload -> Listing_ID = $Listing_ID;
            $upload -> Contract_ID = $Contract_ID;
            $upload -> Referral_ID = $Referral_ID;
            $upload -> file_name = $new_file_name;
            $upload -> file_name_display = $file_name_display;
            $upload -> file_type = 'user';
            $upload -> pages_total = $pages_total;
            $upload -> page_width = $page_width;
            $upload -> page_height = $page_height;
            $upload -> page_size = $page_size;
            $upload -> save();
            $file_id = $upload -> file_id;

            $add_documents -> file_id = $file_id;
            $add_documents -> save();

            $base_path = base_path();
            $storage_path = $base_path.'/storage/app/public';

            $path = 'contracts/'.$Contract_ID;

            if($transaction_type == 'listing') {
                $path = 'listings/'.$Listing_ID;
            } else if($transaction_type == 'referral') {
                $path = 'referrals/'.$Referral_ID;
            }

            $storage_dir = 'doc_management/transactions/'.$path.'/'.$file_id.'_user';
            $storage_public_path = '/storage/'.$storage_dir;
            $file_location = $storage_public_path.'/'.$new_file_name;

            if(!Storage::disk('public') -> put($storage_dir.'/'.$new_file_name, file_get_contents($file))) {
                $fail = json_encode(['fail' => 'File Not Uploaded']);
                return ($fail);
            }

            $storage_full_path = $storage_path.'/doc_management/transactions/'.$path.'/'.$file_id.'_user';
            chmod($storage_full_path.'/'.$new_file_name, 0775);


            // if size is not exactly letter but is close convert to letter or a4
            if($page_size == '') {
                $this -> convert_pdf_to_standard_size($page_width, $page_height, $storage_full_path, $new_file_name, $upload);
            }

            exec('chmod 0777 '.Storage::disk('public') -> path('doc_management/transactions/'.$path));

            Storage::disk('public') -> makeDirectory($storage_dir.'/converted');


            $file_in = Storage::disk('public') -> path($storage_dir.'/'.$new_file_name);
            $file_out = Storage::disk('public') -> path($storage_dir.'/temp_'.$new_file_name);

            // flatten
            exec('pdftk '.$file_in.' output '.$file_out.' flatten');
            exec('rm '.$file_in.' && mv '.$file_out.' '.$file_in);
            // compress
            /* exec('convert -compress Zip -density 150x150 '.$file_in.' '.$file_out);
            exec('rm '.$file_in.' && mv '.$file_out.' '.$file_in); */

            // add to converted folder
            exec('cp '.Storage::disk('public') -> path($storage_dir.'/'.$new_file_name).' '.Storage::disk('public') -> path($storage_dir.'/converted/'.$new_file_name));

            if(!Storage::disk('public') -> exists($storage_dir.'/converted_images')) {
                Storage::disk('public') -> makeDirectory($storage_dir.'/converted_images');
            }
            $checklist_item_docs_model = new TransactionChecklistItemsDocs();
            $source = $storage_path.'/'.$storage_dir.'/converted/'.$new_file_name;
            $image_file_name = str_replace('.pdf', '.jpg', $new_file_name);
            $destination = $storage_path.'/'.$storage_dir.'/converted_images';

            $checklist_item_docs_model -> convert_doc_to_images($source, $destination, $image_file_name, $Transaction_Docs_ID);



            // update directory path in database
            $upload -> file_location = $file_location;
            $upload -> save();

            // create directories
            $storage_dir_pages = $storage_dir.'/pages';
            Storage::disk('public') -> makeDirectory($storage_dir_pages);
            $storage_dir_images = $storage_dir.'/images';
            Storage::disk('public') -> makeDirectory($storage_dir_images);

            // split pdf into pages and images
            $input_file = $storage_full_path.'/'.$new_file_name;
            $output_files = $storage_path.'/'.$storage_dir_pages.'/page_%02d.pdf';
            $new_image_name = str_replace($ext, 'jpg', $new_file_name);
            //$output_images = $storage_path.'/'.$storage_dir_images.'/'.$new_image_name;
            $output_images = $storage_path.'/'.$storage_dir_images.'/page_%02d.jpg';

            // add individual pages to pages directory
            $create_pages = exec('pdftk '.$input_file.' burst output '.$output_files.' flatten', $output, $return);
            // remove data file
            exec('rm '.$storage_path.'/'.$storage_dir_pages.'/doc_data.txt');

            // add individual images to images directory
            $create_images = exec('convert -density 200 -quality 80 '.$input_file.' -background white -alpha remove -strip '.$output_images, $output, $return);

            // get all image files images_storage_path to use as file location
            $saved_images_directory = Storage::files('public/'.$storage_dir.'/images');
            $images_public_path = $storage_public_path.'/images';

            foreach ($saved_images_directory as $saved_image) {
                // get just file_name
                $images_file_name = basename($saved_image);
                /* $page_number = preg_match('/([0-9]+)\.jpg/', $images_file_name, $matches);
                $page_number = count($matches) > 1 ? $matches[1] + 1 : 1; */
                $page_number = preg_match('/page_([0-9]+)\.jpg/', $images_file_name, $matches);
                $match = $matches[1];
                if(substr($match, 0, 1 == 0)) {
                    $match = substr($match, 1);
                }
                $page_number = count($matches) > 1 ? $match + 1 : 1;

                // add images to database
                $upload_images = new TransactionUploadImages();
                $upload_images -> file_id = $file_id;
                $upload_images -> Agent_ID = $Agent_ID;
                $upload_images -> Listing_ID = $Listing_ID;
                $upload_images -> Contract_ID = $Contract_ID;
                $upload_images -> Referral_ID = $Referral_ID;
                $upload_images -> file_name = $images_file_name;
                $upload_images -> file_location = $images_public_path.'/'.$images_file_name;
                $upload_images -> pages_total = $pages_total;
                $upload_images -> page_number = $page_number;
                $upload_images -> save();

            }

            $saved_pages_directory = Storage::files('public/'.$storage_dir.'/pages');
            $pages_public_path = $storage_public_path.'/pages';

            $page_number = 1;

            foreach ($saved_pages_directory as $saved_page) {
                $pages_file_name = basename($saved_page);
                $upload_pages = new TransactionUploadPages();
                $upload_pages -> Agent_ID = $Agent_ID;
                $upload_pages -> Listing_ID = $Listing_ID;
                $upload_pages -> Contract_ID = $Contract_ID;
                $upload_pages -> Referral_ID = $Referral_ID;
                $upload_pages -> file_id = $file_id;
                $upload_pages -> file_name = $pages_file_name;
                $upload_pages -> file_location = $pages_public_path.'/'.$pages_file_name;
                $upload_pages -> pages_total = $pages_total;
                $upload_pages -> page_number = $page_number;
                $upload_pages -> save();

                $page_number += 1;

            }

            $add_documents -> file_location = $file_location;
            $add_documents -> file_location_converted = $storage_public_path.'/converted/'.$new_file_name;
            $add_documents -> save();

        }

    }

    public function convert_pdf_to_standard_size($page_width, $page_height, $folder, $file_name, $upload) {

        // letter
        // 612 +- 10 [592 622] | 792 +- 15 [787 807]
        if(($page_height > 787 && $page_height < 807 && $page_width > 592 && $page_width < 622) ||
        ($page_height <= 787 && $page_width <= 592)) {
            $orig_file_loc = $folder.'/'.$file_name;
            $temp_file_loc = $folder.'/temp_'.$file_name;

            exec('gs \
            -o '.$temp_file_loc.' \
            -sDEVICE=pdfwrite \
            -sPAPERSIZE=letter \
            -dFIXEDMEDIA \
            -dPDFFitPage \
            -dCompatibilityLevel=1.4 \
            '.$orig_file_loc);

            exec('rm '.$orig_file_loc.' && mv '.$temp_file_loc.' '.$orig_file_loc);


            $page_width = get_width_height($orig_file_loc)['width'];
            $page_height = get_width_height($orig_file_loc)['height'];

            $upload -> page_width = $page_width;
            $upload -> page_height = $page_height;
            $upload -> page_size = 'letter';
            $upload -> save();


        // a4
        // 595 +- 15 [580 610] | 842 +- 15 [827 857]
        } else if($page_height > 827 && $page_height < 857 && $page_width > 580 && $page_width < 610) {
            $orig_file_loc = Storage::disk('public') -> path($storage_dir.'/'.$file_name);
            $temp_file_loc = Storage::disk('public') -> path($storage_dir.'/temp_'.$file_name);

            exec('gs \
            -o '.$temp_file_loc.' \
            -sDEVICE=pdfwrite \
            -sPAPERSIZE=a4 \
            -dFIXEDMEDIA \
            -dPDFFitPage \
            -dCompatibilityLevel=1.4 \
            '.$orig_file_loc);

            exec('rm '.$orig_file_loc.' && mv '.$temp_file_loc.' '.$orig_file_loc);


            $page_width = get_width_height($orig_file_loc)['width'];
            $page_height = get_width_height($orig_file_loc)['height'];

            $upload -> page_width = $page_width;
            $upload -> page_height = $page_height;
            $upload -> page_size = 'letter';
            $upload -> save();

        }



    }


    // End Documents Tab

    // Esign Tab

    public function get_esign(Request $request) {

        return view('/agents/doc_management/transactions/details/data/get_esign');

    }

    public function get_in_process(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;

        $envelopes = EsignEnvelopes::where('transaction_type', $transaction_type)
            -> where('Listing_ID', $Listing_ID)
            -> where('Contract_ID', $Contract_ID)
            -> where('Referral_ID', $Referral_ID)
            -> whereIn('status', ['Sent', 'Signed'])
            -> with('signers')
            -> with('callbacks')
            -> orderBy('created_at', 'desc') -> get();

        return view('/esign/get_in_process_html', compact('envelopes'));

    }

    public function get_completed(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;

        $envelopes = EsignEnvelopes::where('transaction_type', $transaction_type)
            -> where('Listing_ID', $Listing_ID)
            -> where('Contract_ID', $Contract_ID)
            -> where('Referral_ID', $Referral_ID)
            -> where('status', 'Completed')
            -> with('signers')
            -> get();

        return view('/esign/get_completed_html', compact('envelopes'));

    }

    public function get_drafts(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;

        $drafts = EsignEnvelopes::where('transaction_type', $transaction_type)
            -> where('Listing_ID', $Listing_ID)
            -> where('Contract_ID', $Contract_ID)
            -> where('Referral_ID', $Referral_ID)
            -> where('is_draft', 'yes')
            -> with('signers')
            -> get();

        return view('/esign/get_drafts_html', compact('drafts'));

    }

    public function get_deleted_drafts(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;

        $deleted_drafts = EsignEnvelopes::onlyTrashed()
            -> where('transaction_type', $transaction_type)
            -> where('Listing_ID', $Listing_ID)
            -> where('Contract_ID', $Contract_ID)
            -> where('Referral_ID', $Referral_ID)
            -> where('is_draft', 'yes')
            -> with('signers')
            -> get();

        return view('/esign/get_deleted_drafts_html', compact('deleted_drafts'));

    }

    public function get_cancelled(Request $request) {

        $transaction_type = $request -> transaction_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;

        $envelopes = EsignEnvelopes::where('transaction_type', $transaction_type)
            -> where('Listing_ID', $Listing_ID)
            -> where('Contract_ID', $Contract_ID)
            -> where('Referral_ID', $Referral_ID)
            -> whereIn('status', ['Cancelled', 'Expired'])
            -> with('signers')
            -> get();

        return view('/esign/get_cancelled_html', compact('envelopes'));

    }

    public function cancel_envelope(Request $request) {

        $envelope_id = $request -> envelope_id;
        $envelope = EsignEnvelopes::find($envelope_id);

        $client = new Client(config('esign.eversign.key'), config('esign.eversign.business_id'));
        $document = $client -> getDocumentByHash($envelope -> document_hash);
        $client -> cancelDocument($document);

    }

    public function resend_envelope(Request $request) {

        $signer_id = $request -> signer_id;
        $envelope_id = $request -> envelope_id;
        $envelope = EsignEnvelopes::find($envelope_id);

        $client = new Client(config('esign.eversign.key'), config('esign.eversign.business_id'));
        $document = $client -> getDocumentByHash($envelope -> document_hash);
        $signers = $document -> getSigners();
        $signer = null;
        foreach($signers as $signer) {
            if($signer -> getStatus() == 'waiting_for_signature') {
                $client -> sendReminderForDocument($document, $signer);
            }
        }

    }

    // End Esign Tab


    // Checklist Tab

    public function get_checklist(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;
        $transaction_type = $request -> transaction_type;

        if($transaction_type == 'listing') {
            $property = Listings::where('Listing_ID', $Listing_ID) -> first();
            $field = 'Listing_ID';
            $id = $Listing_ID;
        } else if($transaction_type == 'contract') {
            $property = Contracts::where('Contract_ID', $Contract_ID) -> first();
            $field = 'Contract_ID';
            $id = $Contract_ID;
        } else if($transaction_type == 'referral') {
            $property = Referrals::where('Referral_ID', $Referral_ID) -> first();
            $field = 'Referral_ID';
            $id = $Referral_ID;
        }

        $checklist_items_model = new ChecklistsItems();
        $transaction_checklist_items_model = new TransactionChecklistItems();
        $transaction_checklist_item_docs_model = new TransactionChecklistItemsDocs();
        $transaction_checklist_item_notes_model = new TransactionChecklistItemsNotes();
        $users = User::get();

        /* $agent = Agents::find($Agent_ID); */

        $transaction_checklist = TransactionChecklists::where($field, $id) -> first();
        $transaction_checklist_id = $transaction_checklist -> id;
        $original_checklist_id = $transaction_checklist -> checklist_id;


        $checklist = Checklists::where('id', $original_checklist_id) -> first();

        $checklist_types = ['listing', 'both'];

        if($checklist -> checklist_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } else if($checklist -> checklist_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $transaction_checklist_items = $transaction_checklist_items_model -> where('checklist_id', $transaction_checklist_id) -> orderBy('checklist_item_order') -> get();

        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        $trash_folder = TransactionDocumentsFolders::where($field, $id) -> where('folder_name', 'Trash') -> first();
        // if the contract was released just use the folder from the listing
        if(!$trash_folder && $field == 'Contract_ID') {
            $trash_folder = TransactionDocumentsFolders::where('Listing_ID', $property -> Listing_ID) -> where('folder_name', 'Trash') -> first();
        }
        $documents_model = new TransactionDocuments();
        $documents_checklist = $documents_model -> where($field, $id) -> where('folder', '!=', $trash_folder -> id) -> where('assigned', 'no') -> orderBy('doc_order', 'ASC') -> orderBy('created_at', 'DESC') -> get();


        $resource_items = new ResourceItems();

        $for_sale = $property -> SaleRent == 'sale' || $property -> SaleRent == 'both' ? true : false;

        $checklist_type = ucwords($transaction_type);
        if($transaction_type == 'contract' && $for_sale == false) {
            $checklist_type = 'Lease';
        }

        return view('/agents/doc_management/transactions/details/data/get_checklist', compact('property', 'Listing_ID', 'Contract_ID', 'transaction_type', 'checklist_items_model', 'transaction_checklist', 'transaction_checklist_id', 'transaction_checklist_items', 'transaction_checklist_item_docs_model', 'transaction_checklist_item_notes_model', 'transaction_checklist_items_model', 'checklist_groups', 'documents_model', 'users', 'documents_checklist', 'resource_items', 'for_sale', 'checklist_type'));
    }

    public function get_add_document_to_checklist_documents_html(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;
        $transaction_type = $request -> transaction_type;

        if($transaction_type == 'listing') {
            $property = Listings::where('Listing_ID', $Listing_ID) -> first();
            $field = 'Listing_ID';
            $id = $Listing_ID;
        } else if($transaction_type == 'contract') {
            $property = Contracts::where('Contract_ID', $Contract_ID) -> first();
            $field = 'Contract_ID';
            $id = $Contract_ID;
        } else if($transaction_type == 'referral') {
            $property = Referrals::where('Referral_ID', $Referral_ID) -> first();
            $field = 'Referral_ID';
            $id = $Referral_ID;
        }

        $folders = TransactionDocumentsFolders::where($field, $id) -> where('folder_name', '!=', 'Trash') -> orderBy('folder_order') -> get();

        $trash_folder = TransactionDocumentsFolders::where($field, $id) -> where('folder_name', 'Trash') -> first();
        // if the contract was released just use the folder from the listing
        if(!$trash_folder && $field == 'Contract_ID') {
            $trash_folder = TransactionDocumentsFolders::where('Listing_ID', $property -> Listing_ID) -> where('folder_name', 'Trash') -> first();
        }

        $documents_model = new TransactionDocuments();
        $documents_available = $documents_model -> where($field, $id) -> where('folder', '!=', $trash_folder -> id) -> where('assigned', 'no') -> orderBy('doc_order', 'ASC') -> orderBy('created_at', 'DESC') -> get();

        return view('/agents/doc_management/transactions/details/data/get_add_document_to_checklist_documents_html', compact('documents_available', 'folders'));

    }

    public function release_address_submitted(Request $request) {

        $checklist_item_ids = $request -> checklist_item_ids;
        if(!is_array($checklist_item_ids)) {
            if(stristr($checklist_item_ids, ',')) {
                $checklist_item_ids = explode(',', $checklist_item_ids);
            } else {
                $checklist_item_ids = [$checklist_item_ids];
            }
        }

        foreach($checklist_item_ids as $checklist_item_id) {

            $checklist_item = TransactionChecklistItems::where('id', $checklist_item_id) -> first();
            $checklist_form_id = $checklist_item -> checklist_form_id;

            // check if doc was a release doc
            if(Upload::IsRelease($checklist_form_id)) {
                // if a release check to see if we are holding earnest and if address submitted
                $earnest = Earnest::where('Contract_ID', $checklist_item -> Contract_ID) -> first();
                $earnest_id = $earnest -> id;
                if($earnest) {
                    if($earnest -> release_to_street == '') {
                        return response() -> json(['status' => 'no_address', 'earnest_id' => $earnest_id]);
                    }
                }
            }

        }

        return response() -> json(['status' => 'ok']);

    }

    public function add_release_address(Request $request) {

        $earnest_id = $request -> earnest_id;
        $release_to_street = $request -> release_to_street;
        $release_to_city = $request -> release_to_city;
        $release_to_state = $request -> release_to_state;
        $release_to_zip = $request -> release_to_zip;

        $update_earnest = Earnest::find($earnest_id) -> update(['release_to_street' => $release_to_street, 'release_to_city' => $release_to_city, 'release_to_state' => $release_to_state, 'release_to_zip' => $release_to_zip]);

        return true;

    }

    public function add_document_to_checklist_item(Request $request) {

        $document_id = $request -> document_id;
        $checklist_id = $request -> checklist_id;
        $checklist_item_id = $request -> checklist_item_id;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;
        $transaction_type = $request -> transaction_type;

        $checklist_item = TransactionChecklistItems::where('id', $checklist_item_id) -> first();
        $checklist_form_id = $checklist_item -> checklist_form_id;

        // if release is submitted make sure contract was submitted first. Otherwise reject it
        if($transaction_type == 'contract') {

            $docs_submitted = Upload::DocsSubmitted('', $Contract_ID);
            // if this is a release
            if(Upload::IsRelease($checklist_form_id)) {
                // if contract not submitted
                if($docs_submitted['contract_submitted'] === false) {
                    return response() -> json([
                        'release_rejected' => 'yes'
                    ]);
                }
            }

        }

        // add doc
        $add_checklist_item_doc = new TransactionChecklistItemsDocs();
        $add_checklist_item_doc -> document_id = $document_id;
        $add_checklist_item_doc -> checklist_id = $checklist_id;
        $add_checklist_item_doc -> checklist_item_id = $checklist_item_id;
        $add_checklist_item_doc -> Agent_ID = $Agent_ID;
        // set id
        if($transaction_type == 'listing') {
            $add_checklist_item_doc -> Listing_ID = $Listing_ID;
        } else if($transaction_type == 'contract') {
            $add_checklist_item_doc -> Contract_ID = $Contract_ID;
        } else if($transaction_type == 'referral') {
            $add_checklist_item_doc -> Referral_ID = $Referral_ID;
        }
        // save add doc
        $add_checklist_item_doc -> save();

        // set doc assigned and checklist item not reviewed
        $update_docs = TransactionDocuments::where('id', $document_id) -> update(['assigned' => 'yes', 'checklist_item_id' => $checklist_item_id]);
        $checklist_item -> update(['checklist_item_status' => 'not_reviewed']);


        if($transaction_type == 'contract') {

            if(Upload::IsContract($checklist_form_id)) {

                return response() -> json([
                    'contract_submitted' => 'yes'
                ]);

            }
            if(Upload::IsRelease($checklist_form_id)) {

                $contract = Contracts::find($Contract_ID);
                $contract -> Status = ResourceItems::GetResourceID('Cancel Pending', 'contract_status');
                $contract -> save();

                // TODO: notify delia

                return response() -> json([
                    'release_submitted' => 'yes'
                ]);

            }

        }

    }

    public function add_document_to_checklist_item_html(Request $request) {

        $transaction_type = $request -> transaction_type;
        $checklist_id = $request -> checklist_id;
        $document_ids = $request -> document_ids;

        $checklist_items_model = new ChecklistsItems();
        $transaction_checklist_items_modal = new TransactionChecklistItems();
        $upload = new Upload();

        $checklist_items = $transaction_checklist_items_modal -> where('checklist_id', $checklist_id) -> orderBy('checklist_item_order') -> get();
        $transaction_checklist_item_documents = TransactionChecklistItemsDocs::where('checklist_id', $checklist_id) -> get();
        $transaction_documents_model = new TransactionDocuments();
        $documents = $transaction_documents_model -> whereIn('id', $document_ids) -> orderBy('doc_order', 'ASC') -> orderBy('created_at', 'DESC') -> get();

        $checklist_types = ['listing', 'both'];

        if($transaction_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } else if($transaction_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/details/data/add_document_to_checklist_item_html', compact('checklist_id', 'documents', 'transaction_checklist_item_documents', 'checklist_items_model', 'transaction_checklist_items_modal', 'upload', 'transaction_documents_model', 'checklist_items', 'checklist_groups'));
    }

    public function add_notes_to_checklist_item(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;

        $add_notes = new TransactionChecklistItemsNotes();
        $add_notes -> checklist_id = $request -> checklist_id;
        $add_notes -> checklist_item_id = $request -> checklist_item_id;
        $add_notes -> checklist_item_doc_id = $request -> checklist_item_doc_id ?? null;

        //if($transaction_type == 'listing') {
            $add_notes -> Listing_ID = $Listing_ID;
        //} else if($transaction_type == 'contract') {
            $add_notes -> Contract_ID = $Contract_ID;
        //} else if($transaction_type == 'referral') {
            $add_notes -> Referral_ID = $Referral_ID;
        //}

        $Agent_ID = 0;

        if(stristr(auth() -> user() -> group, 'agent')) {
            $Agent_ID = $request -> Agent_ID;
        }

        $add_notes -> Agent_ID = $Agent_ID;
        $add_notes -> note_user_id = auth() -> user() -> id;
        $add_notes -> note_status = 'unread';
        $add_notes -> notes = $request -> notes;
        $add_notes -> save();
    }

    public function change_checklist(Request $request) {

        $checklist_id = $request -> checklist_id;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $transaction_type = $request -> transaction_type;
        $Agent_ID = $request -> Agent_ID;

        $transaction_checklist = TransactionChecklists::where('id', $checklist_id) -> first();
        $original_checklist_id = $transaction_checklist -> checklist_id;

        $checklist = Checklists::where('id', $original_checklist_id) -> first();
        $checklist_state = $checklist -> checklist_state;
        $checklist_location_id = $checklist -> checklist_location_id;
        $checklist_sale_rent = $transaction_checklist -> sale_rent;
        $checklist_hoa_condo = $transaction_checklist -> hoa_condo;
        $checklist_year_built = $transaction_checklist -> year_built;

        $checklist_property_type_id = ResourceItems::GetResourceID($request -> property_type, 'checklist_property_types');
        $checklist_property_sub_type_id = ResourceItems::GetResourceID($request -> property_sub_type, 'checklist_property_sub_types');

        $checklist_represent = 'seller';
        $checklist_type = 'listing';

        if($transaction_type == 'contract') {
            $checklist_represent = 'buyer';
            $checklist_type = 'contract';
        }

        TransactionChecklists::CreateTransactionChecklist($checklist_id, $Listing_ID, $Contract_ID, '', $Agent_ID, $checklist_represent, $checklist_type, $checklist_property_type_id, $checklist_property_sub_type_id, $checklist_sale_rent, $checklist_state, $checklist_location_id, $checklist_hoa_condo, $checklist_year_built);

        return true;
    }

    public function get_email_checklist_html(Request $request) {

        $checklist_id = $request -> checklist_id;
        $transaction_type = $request -> transaction_type;

        $checklist_items_model = new ChecklistsItems();
        $transaction_checklist_items_model = new TransactionChecklistItems();

        $transaction_checklist_items = TransactionChecklistItems::where('checklist_id', $checklist_id) -> orderBy('checklist_item_order') -> get();

        $transaction_checklist_item_notes = new TransactionChecklistItemsNotes();

        $checklist_types = ['listing', 'both'];
        if($transaction_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } else if($transaction_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        return view('/agents/doc_management/transactions/details/data/get_email_checklist_html', compact('transaction_checklist_items', 'checklist_groups', 'checklist_items_model', 'transaction_checklist_items_model', 'transaction_checklist_item_notes'));

    }

    public function mark_note_read(Request $request) {
        $mark_read = TransactionChecklistItemsNotes::where('id', $request -> note_id) -> update(['note_status' => 'read']);
    }

    public function mark_required(Request $request) {
        $checklist_item_id = $request -> checklist_item_id;
        $required = $request -> required;

        $mark_required = TransactionChecklistItems::find($checklist_item_id) -> update(['checklist_item_required' => $required]);

        return true;
    }

    public function remove_checklist_item(Request $request) {

        // remove from items, item_docs and item_notes. then mark all transaction_docs unassigned
        $checklist_item_id = $request -> checklist_item_id;
        $delete_item = TransactionChecklistItems::where('id', $checklist_item_id) -> delete();
        $delete_item_notes = TransactionChecklistItemsNotes::where('checklist_item_id', $checklist_item_id) -> delete();

        $delete_item_docs = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item_id);
        $delete_item_doc_ids = $delete_item_docs -> pluck('document_id');

        $unassign = TransactionDocuments::whereIn('id', $delete_item_doc_ids) -> update(['assigned' => 'no', 'checklist_item_id' => null]);

        $delete_item_docs -> delete();

        return true;

    }

    public function remove_document_from_checklist_item(Request $request) {

        $document_id = $request -> document_id;
        $transaction_type = $request -> transaction_type;
        $Contract_ID = $request -> Contract_ID;

        $checklist_item_doc = TransactionChecklistItemsDocs::where('document_id', $document_id) -> first();
        $checklist_item = TransactionChecklistItems::find($checklist_item_doc -> checklist_item_id);
        $checklist_form_id = $checklist_item -> checklist_form_id;
        $checklist_item_doc -> delete();
        $update_docs = TransactionDocuments::where('id', $document_id) -> update(['assigned' => 'no', 'checklist_item_id' => '0']);

        if($transaction_type == 'contract') {

            $docs_submitted = Upload::DocsSubmitted('', $Contract_ID);
            // if this is a release
            if(Upload::IsRelease($checklist_form_id)) {
                // if contract not submitted
                if($docs_submitted['release_submitted'] === false) {
                    // set contract status to active if no release uploaded
                    $contract = Contracts::find($Contract_ID) -> update(['status' => ResourceItems::GetResourceID('Active', 'contract_status')]);
                }
            }

        }

    }

    public function save_add_checklist_item(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $Agent_ID = $request -> Agent_ID;
        $checklist_id = $request -> checklist_id;
        $checklist_form_id = $request -> checklist_form_id;
        $add_checklist_item_name = $request -> add_checklist_item_name;
        $add_checklist_item_group_id = $request -> add_checklist_item_group_id;

        // get checklist item order
        $checklist_item_order = TransactionChecklistItems::where('checklist_id', $checklist_id) -> where('checklist_item_group_id', $add_checklist_item_group_id) -> max('checklist_item_order');
        $checklist_item_order += 1;

        $new_checklist_item = new TransactionChecklistItems();

        $new_checklist_item -> checklist_id = $checklist_id;
        $new_checklist_item -> Listing_ID = $Listing_ID;
        $new_checklist_item -> Contract_ID = $Contract_ID;
        $new_checklist_item -> Referral_ID = $Referral_ID;
        $new_checklist_item -> Agent_ID = $Agent_ID;
        $new_checklist_item -> checklist_form_id = $checklist_form_id;
        $new_checklist_item -> checklist_item_added_name = $add_checklist_item_name;
        $new_checklist_item -> checklist_item_required = 'yes';
        $new_checklist_item -> checklist_item_group_id = $add_checklist_item_group_id;
        $new_checklist_item -> checklist_item_order = $checklist_item_order;

        $new_checklist_item -> save();

    }

    public function set_checklist_item_review_status(Request $request) {

        $Agent_ID = $request -> Agent_ID ?? 0;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;

        $checklist_item_id = $request -> checklist_item_id;
        $action = $request -> action;
        $note = $request -> note ?? null;
        $release = 'no';
        $release_status = '';
        $listing = 'no';
        $contract = 'no';
        $referral = 'no';
        $property = Listings::GetPropertyDetails($transaction_type, [$Listing_ID, $Contract_ID, $Referral_ID]);
        $lease = $property -> SaleRent == 'sale' || $property -> SaleRent == 'both' ? 'no' : 'yes';

        if($note) {
            $note = '<div><span class="text-danger"><i class="fad fa-exclamation-circle mr-2"></i> Checklist Item Rejected</span><br>'.$note.'</div>';
        }

        $checklist_item = TransactionChecklistItems::find($checklist_item_id);
        $checklist_id = $checklist_item -> checklist_id;

        // update docs status
        $doc_status = 'viewed';

        if($action == 'accepted') {

            if($transaction_type == 'listing') {
                $listing = 'yes';
            } else if($transaction_type == 'contract') {

                $contract = 'yes';

                if(Upload::IsRelease($checklist_item -> checklist_form_id)) {

                    $request -> request -> add(['Contract_ID' => $Contract_ID, 'contract_submitted' => 'yes']);
                    $this -> cancel_contract($request);
                    $release = 'yes';
                    $release_status = 'accepted';

                }

            } else if($transaction_type == 'referral') {
                $referral = 'yes';
            }

        } else if($action == 'not_reviewed') {

            if($transaction_type == 'contract') {
                if(Upload::IsRelease($checklist_item -> checklist_form_id)) {
                    // make sure another contract has not been submitted before undoing cancel
                    $contract = Contracts::find($Contract_ID);
                    if($contract -> Listing_ID > 0) {
                        $active_contracts_count = Contracts::where('Listing_ID', $contract -> Listing_ID) -> count();
                        if($active_contracts_count == 1) {
                            $docs_submitted = Upload::DocsSubmitted('', $Contract_ID);
                            if($docs_submitted['release_submitted'] == true) {
                                $status = 'Cancel Pending';
                            } else {
                                $status = 'Active';
                            }
                            $contract -> Status = ResourceItems::GetResourceID($status, 'contract_status');
                            $contract -> save();
                            $release = 'yes';
                            $release_status = 'not_reviewed';
                        } else {
                            return response() -> json([
                                'result' => 'error',
                                'reason' => 'under_contract'
                            ]);
                        }
                    }
                }
            }

            $doc_status = 'pending';

        } else if($action == 'rejected') {

            // add rejection reason to notes
            $add_notes = new TransactionChecklistItemsNotes();
            $Agent_ID = 0;

            if(stristr(auth() -> user() -> group, 'agent')) {
                $Agent_ID = $request -> Agent_ID;
            }

            $add_notes -> Agent_ID = $Agent_ID;

            if($transaction_type == 'listing') {
                $add_notes -> Listing_ID = $Listing_ID;
            } else if($transaction_type == 'contract') {
                $add_notes -> Contract_ID = $Contract_ID;
            } else if($transaction_type == 'referral') {
                $add_notes -> Referral_ID = $Referral_ID;
            }

            $add_notes -> checklist_item_id = $checklist_item_id;
            $add_notes -> notes = $note;
            $add_notes -> note_user_id = auth() -> user() -> id;
            $add_notes -> save();

        }

        $docs = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item_id) -> update(['doc_status' => $doc_status]);

        $checklist_item -> update(['checklist_item_status' => $action]);

        // check if complete after updating checklist item status
        $complete = 'no';
        if($action == 'accepted') {
            $complete = TransactionChecklistItems::ChecklistComplete($checklist_id) ? 'yes' : 'no';
            if($complete == 'yes') {
                // make closing docs required
                TransactionChecklistItems::MakeClosingDocsRequired($checklist_id);
            }
        }

        return response() -> json([
            'result' => 'success',
            'release' => $release,
            'release_status' => $release_status,
            'listing' => $listing,
            'contract' => $contract,
            'lease' => $lease,
            'referral' => $referral,
            'complete' => $complete
        ]);

    }

    // End Checklist Tab


    // Contracts tab

    public function get_contracts(Request $request) {
        $Listing_ID = $request -> Listing_ID ?? 0;
        $contracts = Contracts::where('Listing_ID', $Listing_ID) -> orderBy('Contract_ID', 'DESC') -> get();
        $resource_items = new ResourceItems();
        $property = Listings::find($Listing_ID);
        $for_sale = $property -> SaleRent == 'sale' || $property -> SaleRent == 'both' ? true : false;

        return view('/agents/doc_management/transactions/details/data/get_contracts', compact('contracts', 'resource_items', 'for_sale'));
    }

    // End Contracts Tab


    // Commission Tab

    public function get_commission(Request $request) {

        $Commission_ID = $request -> Commission_ID;
        $commission = Commission::find($Commission_ID);

        $agent_details = Agents::find($commission -> Agent_ID);

        if($commission -> Contract_ID > 0) {

            $property = Contracts::find($commission -> Contract_ID);
            $rep_both_sides = $property -> Listing_ID > 0 ? 'yes' : null;
            $for_sale = $property -> SaleRent == 'sale' || $property -> SaleRent == 'both' ? 'yes' : null;
            $type = 'sale';

        } else if($commission -> Referral_ID > 0) {
            $property = Referrals::find($commission -> Referral_ID);
            $rep_both_sides = null;
            $for_sale = null;
            $type = 'referral';
        }

        $commission_percentages = Agents::select('commission_percent') -> groupBy('commission_percent') -> pluck('commission_percent');
        $agents = Agents::select('id', 'first_name', 'last_name', 'llc_name') -> where('active', 'yes') -> orderBy('last_name') -> get();

        return view('/agents/doc_management/transactions/details/data/get_commission', compact('commission', 'agent_details', 'property', 'rep_both_sides', 'for_sale', 'commission_percentages', 'agents', 'type'));
    }

    public function save_commission(Request $request) {

        $commission_fields = $request -> all();
        $Commission_ID = $request -> Commission_ID;
        $commission = Commission::find($Commission_ID);
        $Agent_ID = $commission -> Agent_ID;

        foreach($commission_fields as $key => $val) {
            if($key != 'Commission_ID') {
                $commission -> $key = $val;
            }
        }
        $commission -> save();

        // if a contract update the fields in transaction_docs_contracts
        if($commission -> Contract_ID > 0) {
            $close_price = preg_replace('/[\$,]+/', '', $request -> close_price);
            $contract = Contracts::find($commission -> Contract_ID) -> update(['CloseDate' => $request -> close_date, 'ClosePrice' => $close_price, 'UsingHeritage' => $request -> using_heritage, 'TitleCompany' => $request -> title_company]);
        }

        // if a commission other - update the check's agent, address and client name if changed
        if($commission -> commission_type == 'other') {
            $agent = Agents::find($Agent_ID);
            $agent_name = $agent -> first_name.' '.$agent -> last_name;
            $update_checks = CommissionChecksIn::where('Commission_ID', $Commission_ID) -> update(['Agent_ID' => $Agent_ID, 'agent_name' => $agent_name, 'client_name' => $request -> other_client_name, 'street' => $request -> other_street, 'city' => $request -> other_city, 'state' => $request -> other_state, 'zip' => $request -> other_zip]);
        }

        return response() -> json(['result' => 'success']);
    }

    public function get_commission_notes(Request $request) {

        $Commission_ID = $request -> Commission_ID;
        $commission_notes = CommissionNotes::where('Commission_ID', $Commission_ID) -> orderBy('created_at', 'DESC') -> get();
        $users = new User();

        return view('/agents/doc_management/transactions/details/data/get_commission_notes_html', compact( 'commission_notes', 'users'));
    }

    public function add_commission_notes(Request $request) {
        $notes = new CommissionNotes();
        $notes -> Commission_ID = $request -> Commission_ID;
        $notes -> user_id = auth() -> user() -> id;
        $notes -> notes = $request -> notes;
        $notes -> save();

        return response() -> json(['response' => 'success']);
    }

    public function get_agent_details(Request $request) {

        $Agent_ID = $request -> Agent_ID;

        $agent_details = Agents::find($Agent_ID);
        $agent_notes = AgentsNotes::where('Agent_ID', $Agent_ID) -> get();
        $teams = new AgentsTeams();

        return view('agents/doc_management/transactions/details/data/get_agent_details_html', compact('agent_details', 'agent_notes', 'teams'));

    }

    public function get_agent_commission(Request $request) {

        $Contract_ID = $request -> Contract_ID;
        $Referral_ID = $request -> Referral_ID;
        $Agent_ID = $request -> Agent_ID;

        $holding_earnest = null;
        $earnest_amount = null;
        $for_sale = null;
        $is_rental = null;
        $is_referral = 'yes';
        $is_referral_company = null;
        $from_rental_listing = null;
        $referral_company_deduction = null;

        $agent = Agents::find($Agent_ID);

        if(stristr($agent -> company, 'referral')) {
            $is_referral_company = 'yes';
        }

        if($Contract_ID > 0) {

            $property = Contracts::find($Contract_ID);

            if($property -> EarnestHeldBy == 'us') {
                $holding_earnest = 'yes';
                $earnest_amount = $property -> EarnestAmount;
            }
            if($property -> SaleRent != 'rental') {
                $for_sale = 'yes';
            } else {
                $is_rental = 'yes';
                if($property -> Listing_ID > 0) {
                    $from_rental_listing = 'yes';
                }
            }

            $is_referral = null;

            $breakdown = CommissionBreakdowns::where('Contract_ID', $Contract_ID) -> first();

        } else {

            $property = Referrals::find($Referral_ID);

            $breakdown = CommissionBreakdowns::where('Referral_ID', $Referral_ID) -> first();

        }

        $deductions = CommissionBreakdownsDeductions::where('commission_breakdown_id', $breakdown -> id) -> get();

        $agent_commission_deduction_percent = 0;

        if($agent -> commission_percent == '85') {
            $agent_commission_deduction_percent = 15;
        }


        if($is_referral_company == 'yes') {
            $admin_fee = '0';
            $referral_company_deduction = number_format(($property -> AgentCommission * .15), 2);
        } else {
            if($is_referral) {
                $admin_fee = '89';
            }  else {
                if($for_sale == 'yes') {
                    $admin_fee = $agent -> admin_fee;
                } else {
                    $admin_fee = $agent -> admin_fee_rentals;
                }
            }
        }

        $states = LocationData::AllStates();

        return view('/agents/doc_management/transactions/details/data/get_agent_commission', compact('breakdown', 'deductions', 'agent', 'property', 'holding_earnest', 'earnest_amount', 'for_sale', 'is_rental', 'admin_fee', 'is_referral', 'is_referral_company', 'from_rental_listing', 'referral_company_deduction', 'agent_commission_deduction_percent', 'states'));

    }

    public function save_commission_agent(Request $request) {

        $data = $request -> all();

        $breakdown = CommissionBreakdowns::where('Commission_ID', $request -> Commission_ID) -> first();
        foreach ($data as $key => $value) {

            if($key != 'deduction_description' && $key != 'deduction_amount') {
                if(preg_match('/\$/', $value)) {
                    $value = preg_replace('/[\$,]+/', '', $value);
                }
                $breakdown -> $key = $value;
            }

        }

        $breakdown -> save();
        $commission_breakdown_id = $breakdown -> id;

        $deduction_descriptions = $request -> deduction_description;
        $deduction_amounts = $request -> deduction_amount;

        // remove current deductions
        $delete_deductions = CommissionBreakdownsDeductions::where('commission_breakdown_id', $commission_breakdown_id) -> delete();

        if(count($deduction_descriptions) > 1) {

            $c = 0;
            foreach($deduction_descriptions as $deduction_description) {
                if($deduction_description) {
                    $add_deduction = new CommissionBreakdownsDeductions();
                    $add_deduction -> commission_breakdown_id = $commission_breakdown_id;
                    $add_deduction -> description = $deduction_description;
                    $add_deduction -> amount = preg_replace('/[\$,]+/', '', $deduction_amounts[$c]);
                    $add_deduction -> save();
                }
                $c += 1;
            }

        }

        return response() -> json(['status' => 'success']);

    }



    // Checks

    public function get_check_details(Request $request) {

        $check = $request -> file('check_in_upload') ?? $request -> file('check_out_upload') ?? $request -> file('add_earnest_check_upload');

        $new_file_name = str_replace('.pdf', '', $check -> getClientOriginalName());
        $new_file_name = date('YmdHis').'_'.sanitize($new_file_name).'.png';
        exec('convert -density 200 -quality 80 '.$check.'[0] -flatten -fuzz 1% -trim +repage '.Storage::disk('public') -> path('tmp/'.$new_file_name));

        $text = (new TesseractOCR(Storage::disk('public') -> path('tmp/'.$new_file_name)))
            -> run();

        $text = iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $text);
        $check_location = '/storage/tmp/'.$new_file_name;

        // get date
        $check_date_preg = preg_match('/\b[0-9]{1,2}[-|\/]{1}[0-9]{1,2}[-|\/]{1}([0-9]{4}|[0-9]{2})\b/', $text, $check_date_matches);
        $check_date = $check_date_matches[0] ?? null;
        if($check_date) {
            // set date format
            $divider = stristr($check_date, '-') ? '-' : '/';
            $date_parts = explode($divider, $check_date);
            $month = $date_parts[0];
            $day = $date_parts[1];
            $year = $date_parts[2];
            if(strlen($month) == 1) {
                $month = '0'.$month;
            }
            if(strlen($day) == 1) {
                $day = '0'.$day;
            }
            if(strlen($year) == 2) {
                $year = '20'.$year;
            }
            $check_date = $year.'-'.$month.'-'.$day;
        }

        // get check number
        // test if our checks that contain "Check #"
        $check_number_preg = preg_match('/\b\Check\s#\:\s([0-9]{4,})\b/', $text, $check_number_matches);
        $check_number = null;
        if(isset($check_number_matches[1])) {
            $check_number = $check_number_matches[1];
        } else {
            // if not one of our checks get number (4 or more numbers and no -)
            $check_number_preg = preg_match('/\b[0-9]{4,}(?!-)\b/', $text, $check_number_matches);
            $check_number = $check_number_matches[0] ?? null;
        }

        // get check amount
        $check_amount_preg = preg_match('/\b[0-9,]+\.[0-9]{2}\b/', $text, $check_amount_matches);
        $check_amount = $check_amount_matches[0] ?? null;

        // Outgoing checks

        // get pay to the order of
        $check_pay_to_preg = preg_match('/ORDER\sOF\s[_]*([a-zA-Z0-9\.\,\-\s]+)/', $text, $check_pay_to_matches);
        $check_pay_to = null;
        if($check_pay_to_matches) {
            $check_pay_to = trim(preg_replace('/\s\bg\b/', '', $check_pay_to_matches[1])) ?? null;

            if(substr($check_pay_to, -1) == '.') {
                $check_pay_to = substr($check_pay_to, 0, -1);
            }
        }

        $check_pay_to_agent_id = null;
        $agent_search = Agents::where('full_name', $check_pay_to) -> orWhere('llc_name', 'like', '%'.substr($check_pay_to, 0, 15).'%') -> get();
        if(count($agent_search) > 1) {
            $agent_search = Agents::where('full_name', $check_pay_to) -> orWhere('llc_name', $check_pay_to) -> get();
        }
        if(count($agent_search) == 1) {
            $check_pay_to_agent_id = $agent_search -> first() -> id;
        }

        //dd($check_date, $check_number, $check_amount, $text);

        return response() -> json([
            'check_date' => $check_date,
            'check_number' => $check_number,
            'check_amount' => $check_amount,
            'check_location' => $check_location,
            'check_pay_to' => $check_pay_to,
            'check_pay_to_agent_id' => $check_pay_to_agent_id
        ]);


    }

    public function get_checks_in(Request $request) {
        $checks_in = CommissionChecksIn::where('Commission_ID', $request -> Commission_ID) -> orderBy('active', 'DESC') -> orderBy('created_at', 'DESC') -> get();

        return view('/agents/doc_management/transactions/details/data/get_checks_in_html', compact('checks_in'));
    }

    public function save_add_check_in(Request $request) {

        $Commission_ID = $request -> Commission_ID ?? null;
        $file = $request -> file('check_in_upload');
        $page = $request -> page; // details or commission
        $type = $request -> check_in_type; // commission or other

        $ext = $file -> getClientOriginalExtension();
        $file_name = $file -> getClientOriginalName();

        $file_name_no_ext = str_replace('.'.$ext, '', $file_name);
        $clean_file_name = sanitize($file_name_no_ext);
        $new_file_name = $clean_file_name.'.'.$ext;

        $agent_name = null;
        if($request -> check_in_agent_id != '') {
            $agent = Agents::find($request -> check_in_agent_id);
            $agent_name = $agent -> first_name.' '.$agent -> last_name;
        }


        // create upload folder storage/commission/checks_in/commission_id/ or queue
        $path = $page == 'details' ? 'checks_in/'.$Commission_ID : 'checks_in_queue/'.date('YmdHis');
        if(!Storage::disk('public') -> exists('commission/'.$path)) {
            Storage::disk('public') -> makeDirectory('commission/'.$path);
        }
        // move file to folder
        if(!Storage::disk('public') -> put('commission/'.$path.'/'.$new_file_name, file_get_contents($file))) {
            $fail = json_encode(['fail' => 'File Not Uploaded']);
            return ($fail);
        }
        $file_location = '/storage/commission/'.$path.'/'.$new_file_name;

        $new_image_name = str_replace('.pdf', '.png', $new_file_name);
        $image_location = '/storage/commission/'.$path.'/'.$new_image_name;

        // convert to image
        exec('convert -density 200 -quality 80 '.Storage::disk('public') -> path('commission/'.$path.'/'.$new_file_name).'[0] '.Storage::disk('public') -> path('commission/'.$path.'/'.$new_image_name));

        if($page == 'details') {

            $add_check = new CommissionChecksIn();
            $add_check -> check_type = 'commission';

        } else {

            if($type == 'commission') {

                $add_check = new CommissionChecksInQueue();

            } else {

                if(!$Commission_ID) {
                    // if bpo or other add to commission first
                    $commission = new Commission();
                    $commission -> commission_type = 'other';
                    $commission -> Agent_ID = $request -> check_in_agent_id;
                    $commission -> other_street = $request -> check_in_street;
                    $commission -> other_city = $request -> check_in_city;
                    $commission -> other_state = $request -> check_in_state;
                    $commission -> other_zip = $request -> check_in_zip;
                    $commission -> other_client_name = $request -> check_in_client_name;
                    $commission -> total_left = preg_replace('/[\$,]+/', '', $request -> check_in_amount);
                    $commission -> save();
                    $Commission_ID = $commission -> id;
                }

                $add_check = new CommissionChecksIn();
                $add_check -> Commission_ID = $Commission_ID;
                $add_check -> check_type = 'other';
                $add_check -> client_name = $request -> check_in_client_name;

            }

            $add_check -> street = $request -> check_in_street;
            $add_check -> city = $request -> check_in_city;
            $add_check -> state = $request -> check_in_state;
            $add_check -> zip = $request -> check_in_zip;
            $add_check -> Agent_ID = $request -> check_in_agent_id;
            $add_check -> agent_name = $agent_name ?? null;

        }

        $add_check -> Commission_ID = $Commission_ID;
        $add_check -> file_location = $file_location;
        $add_check -> image_location = $image_location;
        $add_check -> check_date = $request -> check_in_date;
        $add_check -> check_amount = preg_replace('/[\$,]+/', '', $request -> check_in_amount);
        $add_check -> check_number = $request -> check_in_number;
        $add_check -> date_received = $request -> check_in_date_received;
        $add_check -> date_deposited = $request -> check_in_date_deposited;
        $add_check -> save();

        return response() -> json(['status' => 'success']);

    }

    public function save_edit_check_in(Request $request) {

        $check_id = $request -> edit_check_in_id;

        $check = CommissionChecksIn::find($check_id);

        $check -> check_date = $request -> edit_check_in_date;
        $check -> check_amount = preg_replace('/[\$,]+/', '', $request -> edit_check_in_amount);
        $check -> check_number = $request -> edit_check_in_number;
        $check -> date_received = $request -> edit_check_in_date_received;
        $check -> date_deposited = $request -> edit_check_in_date_deposited;
        $check -> save();

        return response() -> json(['success' => true]);

    }

    public function save_delete_check_in(Request $request) {

        if($request -> type == 'sale') {
            $check = CommissionChecksInQueue::find($request -> check_id) -> update(['active' => 'no']);
        } else if($request -> type == 'other') {
            $check = CommissionChecksIn::find($request -> check_id) -> update(['active' => 'no']);
        }

        return response() -> json(['response' => 'success']);

    }

    public function undo_delete_check_in(Request $request) {

        $type = $request -> type ?? null;
        if($type && $type == 'sale') {
            $check = CommissionChecksInQueue::find($request -> check_id) -> update(['active' => 'yes']);
        } else {
            $check = CommissionChecksIn::find($request -> check_id) -> update(['active' => 'yes']);
        }

        return response() -> json(['response' => 'success']);

    }

    public function get_checks_out(Request $request) {
        $checks_out = CommissionChecksOut::where('Commission_ID', $request -> Commission_ID) -> orderBy('active', 'DESC') -> orderBy('created_at', 'DESC') -> get();

        return view('/agents/doc_management/transactions/details/data/get_checks_out_html', compact('checks_out'));
    }

    public function save_add_check_out(Request $request) {

        $Commission_ID = $request -> Commission_ID;
        $file = $request -> file('check_out_upload');

        $ext = $file -> getClientOriginalExtension();
        $file_name = $file -> getClientOriginalName();

        $file_name_no_ext = str_replace('.'.$ext, '', $file_name);
        $clean_file_name = sanitize($file_name_no_ext);
        $new_file_name = $clean_file_name.'.'.$ext;

        // create upload folder storage/commission/checks_out/commission_id/
        if(!Storage::disk('public') -> exists('commission/checks_out/'.$Commission_ID)) {
            Storage::disk('public') -> makeDirectory('commission/checks_out/'.$Commission_ID);
        }
        // move file to folder
        if(!Storage::disk('public') -> put('commission/checks_out/'.$Commission_ID.'/'.$new_file_name, file_get_contents($file))) {
            $fail = json_encode(['fail' => 'File Not Uploaded']);
            return ($fail);
        }
        $file_location = '/storage/commission/checks_out/'.$Commission_ID.'/'.$new_file_name;

        $new_image_name = str_replace('.pdf', '.png', $new_file_name);
        $image_location = '/storage/commission/checks_out/'.$Commission_ID.'/'.$new_image_name;

        // convert to image
        exec('convert -density 200 -quality 80 '.Storage::disk('public') -> path('commission/checks_out/'.$Commission_ID.'/'.$new_file_name).'[0] '.Storage::disk('public') -> path('commission/checks_out/'.$Commission_ID.'/'.$new_image_name));

        $add_check = new CommissionChecksOut();
        $add_check -> Commission_ID = $Commission_ID;
        $add_check -> file_location = $file_location;
        $add_check -> image_location = $image_location;
        $add_check -> check_date = $request -> check_out_date;
        $add_check -> check_amount = preg_replace('/[\$,]+/', '', $request -> check_out_amount);
        $add_check -> check_number = $request -> check_out_number;
        $add_check -> check_recipient_agent_id = $request -> check_out_agent_id;
        $add_check -> check_recipient = $request -> check_out_recipient;
        $add_check -> check_delivery_method = $request -> check_out_delivery_method;
        $add_check -> check_date_ready = $request -> check_out_date_ready;
        $add_check -> check_mail_to_street = $request -> check_out_mail_to_street;
        $add_check -> check_mail_to_city = $request -> check_out_mail_to_city;
        $add_check -> check_mail_to_state = $request -> check_out_mail_to_state;
        $add_check -> check_mail_to_zip = $request -> check_out_mail_to_zip;
        $add_check -> save();
    }

    public function save_edit_check_out(Request $request) {

        $check_id = $request -> edit_check_out_id;

        $check = CommissionChecksOut::find($check_id);

        $check -> check_date = $request -> edit_check_out_date;
        $check -> check_amount = preg_replace('/[\$,]+/', '', $request -> edit_check_out_amount);
        $check -> check_number = $request -> edit_check_out_number;
        $check -> check_recipient = $request -> edit_check_out_recipient;
        $check -> check_recipient_agent_id = $request -> edit_check_out_agent_id;
        $check -> check_delivery_method = $request -> edit_check_out_delivery_method;
        $check -> check_date_ready = $request -> edit_check_out_date_ready;
        $check -> check_mail_to_street = $request -> edit_check_out_mail_to_street;
        $check -> check_mail_to_city = $request -> edit_check_out_mail_to_city;
        $check -> check_mail_to_state = $request -> edit_check_out_mail_to_state;
        $check -> check_mail_to_zip = $request -> edit_check_out_mail_to_zip;
        $check -> save();

        return response() -> json(['success' => true]);

    }

    public function save_delete_check_out(Request $request) {

        $check = CommissionChecksOut::find($request -> check_id) -> update(['active' => 'no']);

        return response() -> json(['response' => 'success']);

    }

    public function undo_delete_check_out(Request $request) {

        $check = CommissionChecksOut::find($request -> check_id) -> update(['active' => 'yes']);

        return response() -> json(['response' => 'success']);

    }

    public function get_checks_in_queue(Request $request) {

        $Agent_ID = $request -> Agent_ID;
        $checks_in_queue = CommissionChecksInQueue::where('Agent_ID', $Agent_ID) -> where('active', 'yes') -> where('exported', 'no') -> get();

        return view('agents/doc_management/transactions/details/data/get_checks_in_queue_html', compact('checks_in_queue'));
    }

    public function re_queue_check(Request $request) {

        $check_id = $request -> check_id;
        $check_in = CommissionChecksIn::find($check_id);

        // update exported to no in queue
        $check_in_queue = CommissionChecksInQueue::find($check_in -> queue_id) -> update(['exported' => 'no']);

        // delete files from checks in
        Storage::disk('public') -> delete([str_replace('/storage/', '', $check_in -> file_location), str_replace('/storage/', '', $check_in -> image_location)]);

        // delete from checks in - actually delete, not make inactive
        $check_in -> delete();

        return response() -> json(['status' => 'success']);

    }

    public function import_check_in(Request $request) {

        $check_id = $request -> check_id;
        $Commission_ID = $request -> Commission_ID;

        $check_in_queue = CommissionChecksInQueue::find($check_id);
        $check_in_queue -> exported = 'yes';
        $check_in_queue -> save();

        $add_check = new CommissionChecksIn();

        // copy files from commission/checks_in_queue/987897897989 to commission/checks_in/9
        $old_file_location = str_replace('/storage/', '', $check_in_queue -> file_location);
        $old_image_location = str_replace('/storage/', '', $check_in_queue -> image_location);

        $path = 'commission/checks_in/'.$Commission_ID;
        $new_file_location = $path.'/'.basename($check_in_queue -> file_location);
        $new_image_location = $path.'/'.basename($check_in_queue -> image_location);

        if(!Storage::disk('public') -> exists($path)) {
            Storage::disk('public') -> makeDirectory($path);
        }

        Storage::disk('public') -> copy($old_file_location, $new_file_location);
        Storage::disk('public') -> copy($old_image_location, $new_image_location);


        $add_check -> Commission_ID = $Commission_ID;
        $add_check -> file_location = '/storage/'.$new_file_location;
        $add_check -> image_location = '/storage/'.$new_image_location;
        $add_check -> check_type = 'commission';
        $add_check -> queue_id = $check_in_queue -> id;
        $add_check -> check_date = $check_in_queue -> check_date;
        $add_check -> check_amount = $check_in_queue -> check_amount;
        $add_check -> check_number = $check_in_queue -> check_number;
        $add_check -> date_received = $check_in_queue -> date_received;
        $add_check -> date_deposited = $check_in_queue -> date_deposited;
        $add_check -> street = $check_in_queue -> street;
        $add_check -> city = $check_in_queue -> city;
        $add_check -> state = $check_in_queue -> state;
        $add_check -> zip = $check_in_queue -> zip;
        $add_check -> Agent_ID = $check_in_queue -> Agent_ID;
        $add_check -> agent_name = $check_in_queue -> agent_name;

        $add_check -> save();

        return response() -> json(['status' => 'success']);
    }

    // Income Deductions

    public function get_income_deductions(Request $request) {

        $Commission_ID = $request -> Commission_ID;
        $deductions = CommissionIncomeDeductions::where('Commission_ID', $Commission_ID) -> orderBy('created_at', 'DESC') -> get();

        return compact('deductions');

    }

    public function delete_income_deduction(Request $request) {
        $deduction_id = $request -> deduction_id;
        $delete = CommissionIncomeDeductions::find($deduction_id) -> delete();

        return response() -> json(['success' => true]);
    }

    public function save_add_income_deduction(Request $request) {

        $deduction = new CommissionIncomeDeductions();
        $deduction -> Commission_ID = $request -> Commission_ID;
        $deduction -> amount = preg_replace('/[\$,]+/', '', $request -> amount);
        $deduction -> description = $request -> description;
        $deduction -> save();

        return response() -> json(['success' => true]);
    }

    // Commission Deductions

    public function get_commission_deductions(Request $request) {

        $Commission_ID = $request -> Commission_ID;
        $deductions = CommissionCommissionDeductions::where('Commission_ID', $Commission_ID) -> orderBy('created_at', 'DESC') -> get();

        return compact('deductions');

    }

    public function delete_commission_deduction(Request $request) {
        $deduction_id = $request -> deduction_id;
        $delete = CommissionCommissionDeductions::find($deduction_id) -> delete();

        return response() -> json(['success' => true]);
    }

    public function save_add_commission_deduction(Request $request) {

        $deduction = new CommissionCommissionDeductions();
        $deduction -> Commission_ID = $request -> Commission_ID;
        $deduction -> amount = preg_replace('/[\$,]+/', '', $request -> amount);
        $deduction -> description = $request -> description;
        $deduction -> save();

        return response() -> json(['success' => true]);
    }

    // End Commission Tab

    // Earnest Tab

    public function get_earnest(Request $request) {

        $Contract_ID = $request -> Contract_ID;

        $earnest = Earnest::where('Contract_ID', $Contract_ID) -> first();
        $property = Contracts::find($Contract_ID);
        $agent = Agents::find($property -> Agent_ID);

        $earnest_held_by = $earnest -> held_by != '' ? $earnest -> held_by : $property ->  EarnestHeldBy;

        // earnest accounts
        $earnest_accounts = ResourceItems::where('resource_type', 'earnest_accounts') -> orderBy('resource_order') -> get();

        $suggested_earnest_account = $earnest -> earnest_account_id;
        if($suggested_earnest_account == '') {

            $state = $property -> StateOrProvince;
            $company = $agent -> company;

            if($state == 'MD') {
                $suggested_earnest_account = $earnest_accounts -> where('resource_state', $state) -> where('resource_name', $company) -> first() -> resource_id;
            } else {
                $suggested_earnest_account = $earnest_accounts -> where('resource_state', $state) -> first() -> resource_id;
            }

        }


        return view('/agents/doc_management/transactions/details/data/get_earnest', compact('earnest', 'earnest_held_by', 'earnest_accounts', 'suggested_earnest_account'));
    }

    public function get_earnest_checks(Request $request) {

        $check_type = $request -> check_type;
        $Earnest_ID = $request -> Earnest_ID;

        $checks = EarnestChecks::where('Earnest_ID', $Earnest_ID) -> where('check_type', $check_type) -> get();

        return view('/agents/doc_management/transactions/details/data/get_earnest_checks_html', compact('checks', 'check_type'));

    }

    public function save_earnest(Request $request) {

        // update earnest
        $earnest = Earnest::find($request -> Earnest_ID);
        $earnest -> update(['held_by' => $request -> earnest_held_by, 'earnest_account_id' => $request -> earnest_account_id]);

        // update property
        $property = Contracts::find($earnest -> Contract_ID) -> update(['EarnestHeldBy' => $request -> earnest_held_by]);

        return response() -> json(['status' => 'success']);

    }

    public function save_earnest_amounts(Request $request) {

        // update earnest amounts
        $earnest = Earnest::find($request -> Earnest_ID);
        /* $amount_total = $request -> amount_total;
        $amount_received = $request -> amount_received;
        $amount_released = $request -> amount_released; */

        $checks = $earnest -> checks() -> get();


        $amount_received = 0;
        $amount_released = 0;

        foreach($checks as $check) {

            if($check -> active == 'yes') {

                if($check -> check_status == 'cleared') {
                    if($check -> check_type == 'in') {
                        $amount_received += $check -> check_amount;
                    } else if($check -> check_type == 'out') {
                        $amount_released += $check -> check_amount;
                    }
                }

            }

        }

        $amount_total = $amount_received - $amount_released;

        // get status
        $status = 'pending';
        if($amount_received > 0) {
            if($amount_received > $amount_released) {
                $status = 'active';
            } else if($amount_received == $amount_released) {
                $status = 'released';
            }
        }

        $earnest -> update(['status' => $status, 'amount_total' => $amount_total, 'amount_received' => $amount_received, 'amount_released' => $amount_released]);

        // update property
        if($request -> amount_received > 0) {
            $property = Contracts::find($earnest -> Contract_ID) -> update(['EarnestAmount' => $request -> amount_received]);
        }

        return response() -> json(['status' => 'success']);

    }

    public function save_add_earnest_check(Request $request) {

        $Earnest_ID = $request -> Earnest_ID;
        $Agent_ID = $request -> Agent_ID;
        $Contract_ID = $request -> Contract_ID;
        $check_type = $request -> add_earnest_check_type;
        $check_name = $request -> add_earnest_check_name;
        $payable_to = $request -> add_earnest_check_payable_to;
        $check_date = $request -> add_earnest_check_date;
        $check_number = $request -> add_earnest_check_number;
        $check_amount = preg_replace('/[\$,]+/', '', $request -> add_earnest_check_amount);
        $date_deposited = $request -> add_earnest_check_date_deposited;
        $mail_to_address = $request -> add_earnest_check_mail_to_address;
        $date_sent = $request -> add_earnest_check_date_sent;

        $file = $request -> file('add_earnest_check_upload');
        $ext = $file -> getClientOriginalExtension();
        $file_name = $file -> getClientOriginalName();
        $file_name_no_ext = str_replace('.'.$ext, '', $file_name);
        $clean_file_name = sanitize($file_name_no_ext);
        $new_file_name = $clean_file_name.'.'.$ext;

        // create upload folder storage/earnest/checks_in/earnest_id/ or queue
        $path = 'earnest/checks_in/'.$Earnest_ID;
        if(!Storage::disk('public') -> exists($path)) {
            Storage::disk('public') -> makeDirectory($path);
        }
        // move file to folder
        if(!Storage::disk('public') -> put($path.'/'.$new_file_name, file_get_contents($file))) {
            $fail = json_encode(['fail' => 'File Not Uploaded']);
            return ($fail);
        }
        $file_location = '/storage/'.$path.'/'.$new_file_name;

        $new_image_name = str_replace('.pdf', '.png', $new_file_name);
        $image_location = '/storage/'.$path.'/'.$new_image_name;

        // convert to image
        exec('convert -density 200 -quality 80 '.Storage::disk('public') -> path($path.'/'.$new_file_name).'[0] -flatten -fuzz 1% -trim +repage '.Storage::disk('public') -> path($path.'/'.$new_image_name));

        $add_earnest = new EarnestChecks();
        $add_earnest -> Earnest_ID = $Earnest_ID;
        $add_earnest -> Agent_ID = $Agent_ID;
        $add_earnest -> Contract_ID = $Contract_ID;
        $add_earnest ->  check_type = $check_type;
        $add_earnest -> check_name = $check_name;
        $add_earnest -> payable_to = $payable_to;
        $add_earnest -> check_date = $check_date;
        $add_earnest -> check_number = $check_number;
        $add_earnest -> check_amount = $check_amount;
        $add_earnest -> date_deposited = $date_deposited;
        $add_earnest -> mail_to_address = $mail_to_address;
        $add_earnest -> date_sent = $date_sent;
        $add_earnest -> file_location = $file_location;
        $add_earnest -> image_location = $image_location;
        $add_earnest -> save();

        return response() -> json(['status' => 'success']);

    }

    public function save_edit_earnest_check(Request $request) {

        $check_id = $request -> edit_earnest_check_id;
        $check_name = $request -> edit_earnest_check_name;
        $payable_to = $request -> edit_earnest_payable_to;
        $check_date = $request -> edit_earnest_check_date;
        $check_number = $request -> edit_earnest_check_number;
        $check_amount = preg_replace('/[\$,]+/', '', $request -> edit_earnest_check_amount);
        $date_deposited = $request -> edit_earnest_date_deposited;
        $mail_to_address = $request -> edit_earnest_mail_to_address;
        $date_sent = $request -> edit_earnest_date_sent;

        $edit_earnest = EarnestChecks::find($check_id);
        $edit_earnest -> check_name = $check_name;
        $edit_earnest -> payable_to = $payable_to;
        $edit_earnest -> check_date = $check_date;
        $edit_earnest -> check_number = $check_number;
        $edit_earnest -> check_amount = $check_amount;
        $edit_earnest -> date_deposited = $date_deposited;
        $edit_earnest -> mail_to_address = $mail_to_address;
        $edit_earnest -> date_sent = $date_sent;
        $edit_earnest -> save();

        return response() -> json(['status' => 'success']);

    }

    public function clear_bounce_earnest_check(Request $request) {

        $check_id = $request -> check_id;
        $status = $request -> status;

        $date_cleared = '';
        if($status == '') {
            $status = 'pending';
        } else if($status == 'cleared') {
            $date_cleared = date('Y-m-d');
        }

        $update_check = EarnestChecks::find($check_id) -> update(['check_status' => $status, 'date_cleared' => $date_cleared]);

        return response() -> json(['status' => 'success']);

    }

    public function delete_earnest_check(Request $request) {

        $delete_check = EarnestChecks::find($request -> check_id) -> update(['active' => 'no']);

        return response() -> json(['response' => 'success']);

    }

    public function undo_delete_earnest_check(Request $request) {

        $undo_delete_check = EarnestChecks::find($request -> check_id) -> update(['active' => 'yes']);

        return response() -> json(['response' => 'success']);

    }

    // End Earnest Tab


    /////////////// END TABS //////////////

    public function update_contract_status(Request $request) {
        $Contract_ID = $request -> Contract_ID;
        $status = $request -> status;
        $status = ResourceItems::GetResourceID($status, 'contract_status');
        $contract = Contracts::find($Contract_ID) -> update(['Status' => $status]);
    }

    // accept contract
    public function accept_contract(Request $request) {

        $buyer_one_first = $request -> buyer_one_first;
        $buyer_one_last = $request -> buyer_one_last;
        $buyer_two_first = $request -> buyer_two_first;
        $buyer_two_last = $request -> buyer_two_last;

        $agent_first = $request -> agent_first;
        $agent_last = $request -> agent_last;
        $agent_email = $request -> agent_email;
        $agent_phone = $request -> agent_phone;
        $agent_mls_id = $request -> agent_mls_id;
        $agent_company = $request -> agent_company;
        $agent_street = $request -> agent_street;
        $agent_city = $request -> agent_city;
        $agent_state = $request -> agent_state;
        $agent_zip = $request -> agent_zip;

        $OtherAgent_ID = $request -> OtherAgent_ID;
        $BuyerRepresentedBy = $request -> BuyerRepresentedBy;
        $Listing_ID = $request -> Listing_ID;
        $listing = Listings::find($Listing_ID);

        $Agent_ID = $listing -> Agent_ID;

        // update listing
        $listing -> BuyerAgentFirstName = $agent_first;
        $listing -> BuyerAgentLastName = $agent_last;
        $listing -> BuyerAgentEmail = $agent_email;
        $listing -> BuyerAgentPreferredPhone = $agent_phone;
        $listing -> BuyerAgentMlsId = $agent_mls_id;
        $listing -> BuyerOfficeName = $agent_company;

        $listing -> BuyerOneFirstName = $buyer_one_first;
        $listing -> BuyerOneLastName = $buyer_one_last;
        $listing -> BuyerTwoFirstName = $buyer_two_first;
        $listing -> BuyerTwoLastName = $buyer_two_last;
        $listing -> Status = ResourceItems::GetResourceID('Under Contract', 'listing_status');
        $listing -> save();

        $using_heritage = $request -> using_heritage;
        $title_company = $request -> title_company;
        $earnest_amount = $request -> earnest_amount;
        $earnest_held_by = $request -> earnest_held_by;

        // new contract data
        $contract_data = $listing -> replicate();
        $contract_data -> Listing_ID = $Listing_ID;
        $contract_data -> BuyerAgentFirstName = $agent_first;
        $contract_data -> BuyerAgentLastName = $agent_last;
        $contract_data -> BuyerAgentEmail = $agent_email;
        $contract_data -> BuyerAgentPreferredPhone = $agent_phone;
        $contract_data -> BuyerAgentMlsId = $agent_mls_id;
        $contract_data -> BuyerOfficeName = $agent_company;

        $contract_data -> BuyerOneFirstName = $buyer_one_first;
        $contract_data -> BuyerOneLastName = $buyer_one_last;
        $contract_data -> BuyerTwoFirstName = $buyer_two_first;
        $contract_data -> BuyerTwoLastName = $buyer_two_last;
        $contract_data -> ContractDate = $request -> contract_date;
        $contract_data -> CloseDate = $request -> close_date;
        $contract_data -> ContractPrice = preg_replace('/[\$,]+/', '', $request -> contract_price);
        $contract_data -> LeaseAmount = preg_replace('/[\$,]+/', '', $request -> lease_amount);

        $contract_data -> EarnestAmount = preg_replace('/[\$,]+/', '', $earnest_amount);
        $contract_data -> EarnestHeldBy = $earnest_held_by;
        $contract_data -> UsingHeritage = $using_heritage;
        $contract_data -> TitleCompany = $title_company ?? '';

        $contract_data -> OtherAgent_ID = $OtherAgent_ID;
        $contract_data -> BuyerRepresentedBy = $BuyerRepresentedBy;

        $FullStreetAddress = ucwords(strtolower($contract_data -> FullStreetAddress));

        $contract_data -> Status = ResourceItems::GetResourceID('Active', 'contract_status');

        $contract_data = collect($contract_data -> toArray()) -> except(['Contract_ID']);

        $contract_data = json_decode($contract_data, true);

        $new_contract = Contracts::create($contract_data);
        $Contract_ID = $new_contract -> Contract_ID;

        // add email address
        $new_transaction = Contracts::find($Contract_ID);

        $code = $Contract_ID.'C';
        $address = preg_replace(config('global.vars.bad_characters'), '', $FullStreetAddress);
        $email = $address.'_'.$code.'@'.config('global.vars.property_email');

        // add to commission and get commission id
        $commission = new Commission();
        $commission -> Contract_ID = $Contract_ID;
        $commission -> Agent_ID = $Agent_ID;
        $commission -> save();
        $Commission_ID = $commission -> id;

        $commission_breakdown = new CommissionBreakdowns();
        $commission_breakdown -> Commission_ID = $Commission_ID;
        $commission_breakdown -> Contract_ID = $Contract_ID;
        $commission_breakdown -> Agent_ID = $Agent_ID;
        $commission_breakdown -> save();

        // add to earnest
        $add_earnest = new Earnest();
        $add_earnest -> Contract_ID = $Contract_ID;
        $add_earnest -> Agent_ID = $Agent_ID;
        $add_earnest -> save();
        $Earnest_ID = $add_earnest -> id;

        $new_transaction -> PropertyEmail = $email;
        $new_transaction -> Commission_ID = $Commission_ID;
        $new_transaction -> Earnest_ID = $Earnest_ID;
        $new_transaction -> save();

        // add Contract_ID to members already in members
        $import_members_from_listing = Members::where('Listing_ID', $Listing_ID) -> update(['Contract_ID' => $Contract_ID]);

        // add buyers and buyers agent to members
        $add_buyer_to_members = new Members();
        $add_buyer_to_members -> member_type_id = ResourceItems::BuyerResourceId();
        $add_buyer_to_members -> first_name = $buyer_one_first;
        $add_buyer_to_members -> last_name = $buyer_one_last;
        $add_buyer_to_members -> Contract_ID = $Contract_ID;
        $add_buyer_to_members -> Agent_ID = $Agent_ID;
        $add_buyer_to_members -> save();

        if($buyer_two_first != '') {
            $add_buyer_to_members = new Members();
            $add_buyer_to_members -> member_type_id = ResourceItems::BuyerResourceId();
            $add_buyer_to_members -> first_name = $buyer_two_first;
            $add_buyer_to_members -> last_name = $buyer_two_last;
            $add_buyer_to_members -> Contract_ID = $Contract_ID;
            $add_buyer_to_members -> Agent_ID = $Agent_ID;
            $add_buyer_to_members -> save();
        }

        if($BuyerRepresentedBy != 'none') {
            $add_buyer_agent_to_members = new Members();
            $add_buyer_agent_to_members -> member_type_id = ResourceItems::BuyerAgentResourceId();
            $add_buyer_agent_to_members -> first_name = $agent_first;
            $add_buyer_agent_to_members -> last_name = $agent_last;
            $add_buyer_agent_to_members -> cell_phone = $agent_phone;
            $add_buyer_agent_to_members -> email = $agent_email;
            $add_buyer_agent_to_members -> bright_mls_id = $agent_mls_id;
            $add_buyer_agent_to_members -> company = $agent_company;
            $add_buyer_agent_to_members -> address_office_street = $agent_street;
            $add_buyer_agent_to_members -> address_office_city = $agent_city;
            $add_buyer_agent_to_members -> address_office_state = $agent_state;
            $add_buyer_agent_to_members -> address_office_zip = $agent_zip;
            $add_buyer_agent_to_members -> Contract_ID = $Contract_ID;
            $add_buyer_agent_to_members -> Agent_ID = $Agent_ID;
            $add_buyer_agent_to_members -> save();
        }

        // if using heritage add them to members

        // TODO: notify title if using them
        if($using_heritage == 'yes') {
            $add_heritage_to_members = new Members();
            $add_heritage_to_members -> member_type_id = ResourceItems::TitleResourceId();
            $add_heritage_to_members -> company = 'Heritage Title';
            $add_heritage_to_members -> Contract_ID = $Contract_ID;
            $add_heritage_to_members -> Agent_ID = $Agent_ID;
            $add_heritage_to_members -> save();
        }
        // TODO: if earnest
        // if holding earnest
        // notify
        // add checklist
        $checklist_represent = 'buyer';

        if($Listing_ID > 0) {
            $checklist_represent = 'seller';
        }

        $checklist_property_type_id = $listing -> PropertyType;
        $checklist_property_sub_type_id = $listing -> PropertySubType;
        $checklist_sale_rent = $listing -> SaleRent;
        $checklist_state = $listing -> StateOrProvince;
        $checklist_location_id = $listing -> Location_ID;
        $transaction_checklist = TransactionChecklists::where('Listing_ID', $Listing_ID) -> first();
        $checklist_hoa_condo = $transaction_checklist -> hoa_condo;
        $checklist_year_built = $listing -> YearBuilt;

        // create checklist
        TransactionChecklists::CreateTransactionChecklist('', $Listing_ID, $Contract_ID, '', $listing -> Agent_ID, 'seller', 'contract', $checklist_property_type_id, $checklist_property_sub_type_id, $checklist_sale_rent, $checklist_state, $checklist_location_id, $checklist_hoa_condo, $checklist_year_built);

        // add folders from listing
        $folder = TransactionDocumentsFolders::where('Listing_ID', $Listing_ID) -> update(['Contract_ID' => $Contract_ID]);


        return response() -> json([
            'Contract_ID' => $Contract_ID,
        ]);

    }

    public function cancel_listing(Request $request) {
        $listing = Listings::find($request -> Listing_ID) -> update(['Status' => ResourceItems::GetResourceID('Canceled', 'listing_status')]);
        return response() -> json(['status' => 'success']);
    }

    public function cancel_contract(Request $request) {

        $Contract_ID = $request -> Contract_ID;
        $contract_submitted = $request -> contract_submitted;
        $contract = Contracts::find($Contract_ID);
        $listing = Listings::find($contract -> Listing_ID);

        $status = $contract_submitted == 'yes' ? 'Released' : 'Canceled';

        // update listing
        if($listing) {
            // remove Buyer from listing and update status
            $listing = Listings::find($contract -> Listing_ID);
            $listing -> BuyerAgentFirstName = '';
            $listing -> BuyerAgentLastName = '';
            $listing -> BuyerAgentEmail = '';
            $listing -> BuyerAgentPreferredPhone = '';
            $listing -> BuyerAgentMlsId = '';
            $listing -> BuyerOfficeName = '';
            $listing -> BuyerOfficeMlsId = '';
            $listing -> BuyerOfficeName = '';
            $listing -> BuyerOneFirstName = '';
            $listing -> BuyerOneLastName = '';
            $listing -> BuyerTwoFirstName = '';
            $listing -> BuyerTwoLastName = '';
            $listing -> Status = ResourceItems::GetResourceID('Active', 'listing_status');
            $listing -> save();
        }

        $contract = Contracts::find($Contract_ID);
        $contract -> Status = ResourceItems::GetResourceID($status, 'contract_status');
        $contract -> save();

        return true;

    }

    public function undo_cancel_listing(Request $request) {

        $Listing_ID = $request -> Listing_ID;
        $Agent_ID = $request -> Agent_ID;
        $status = 'Active';
        $expired = '';

        $listing = Listings::find($Listing_ID);

        if($listing -> ExpirationDate < date('Y-m-d')) {
            $expired = 'expired';
            $status = 'Expired';
        }
        $listing -> Status = ResourceItems::GetResourceID($status, 'listing_status');
        $listing -> save();

        return response() -> json(['expired' => $expired]);

    }

    public function undo_cancel_contract(Request $request) {

        $Contract_ID = $request -> Contract_ID;
        $contract = Contracts::find($Contract_ID);
        $Listing_ID = $contract -> Listing_ID;
        $Agent_ID = $request -> Agent_ID;

        if($Listing_ID > 0) {
            $active_ids = ResourceItems::GetActiveAndClosedContractStatuses();
            $open_contracts = Contracts::where('Listing_ID', $Listing_ID) -> whereIn('Status', $active_ids) -> get();
            $listing_under_contract = Listings::find($Listing_ID);

            if(count($open_contracts) > 0) {
                return response() -> json([
                    'error' => 'under_contract'
                ]);
            }
            // if no open contracts than add this contract to the listing
            $listing_under_contract -> Status = ResourceItems::GetResourceID('Under Contract', 'listing_status');
            $listing_under_contract -> save();
        }

        $contract -> Status = ResourceItems::GetResourceID('Active', 'contract_status');
        $contract -> save();

        // reject release if submitted
        $checklist_items = TransactionChecklistItems::where('Contract_ID', $Contract_ID) -> get();
        foreach($checklist_items as $checklist_item) {
            if(Upload::IsRelease($checklist_item -> checklist_form_id)) {

                // reject checklist item if release
                $checklist_item -> checklist_item_status = 'rejected';
                $checklist_item -> save();

                // add rejection to notes
                $add_notes = new TransactionChecklistItemsNotes();
                $add_notes -> checklist_id = $checklist_item -> checklist_id;
                $add_notes -> checklist_item_id = $checklist_item -> id;
                $add_notes -> Contract_ID = $Contract_ID;
                $add_notes -> note_user_id = auth() -> user() -> id;
                $add_notes -> note_status = 'unread';
                $add_notes -> notes = 'Cancellation undone by '.auth() -> user() -> name;
                $add_notes -> save();
            }
        }



    }

    public function check_docs_submitted_and_accepted(Request $request) {

        $Listing_ID = $request -> Listing_ID;
        $Contract_ID = $request -> Contract_ID;

        if($Listing_ID) {
            $docs_submitted = Upload::DocsSubmitted($Listing_ID, '');
        } else if($Contract_ID) {
            $docs_submitted = Upload::DocsSubmitted('', $Contract_ID);
        }

        return response() -> json([
            'listing_submitted' => $docs_submitted['listing_submitted'],
            'listing_accepted' => $docs_submitted['listing_accepted'],
            'listing_expired' => $docs_submitted['listing_expired'],
            'listing_withdraw_submitted' => $docs_submitted['listing_withdraw_submitted'],
            'contract_submitted' => $docs_submitted['contract_submitted'],
            'release_submitted' => $docs_submitted['release_submitted'],
            'our_listing' => $docs_submitted['our_listing']
        ]);
    }

    public function get_path($url) {
        return Storage::disk('public') -> path(preg_replace('/^.*\/storage\//', '', $url));
    }

    // search bright mls agents
    public function search_bright_agents(Request $request) {

        $val = $request -> val;

        $agents = AgentRoster::where('MemberLastName', 'like', '%'.$val.'%')
            -> orWhere('MemberEmail', 'like', '%'.$val.'%')
            -> orWhere('MemberMlsId', 'like', '%'.$val.'%')
            -> orWhereRaw('CONCAT(MemberFirstName, " ", MemberLastName) like \'%'.$val.'%\'')
            -> orWhereRaw('CONCAT(MemberNickname, " ", MemberLastName) like \'%'.$val.'%\'')
            -> orderBy('MemberLastName')
            -> limit(50)
            -> get();

        return compact('agents');
    }

    public function send_email(Request $request) {

        $type = $request -> type;
        $email = [];
        $from_address = $request -> from;
        $from_name = '';

        if(preg_match('/\<.*\>/', $from_address)) {
            preg_match('/(.*)[\s]*\<(.*)\>/', $from_address, $match);
            $from_name = $match[1];
            $from_address = $match[2];
        }

        $email['from'] = ['address' => $from_address, 'name' => $from_name];

        $email['subject'] = $request -> subject;
        $email['message'] = $request -> message;

        $email['tos_array'] = [];

        foreach (json_decode($request -> to_addresses) as $to_address) {

            $address = $to_address -> address;
            // if separated by , or ;
            if(preg_match('/[,;]+/', $address, $separator)) {
                $addresses = explode($separator[0], $address);

                foreach ($addresses as $address) {
                    $to = [];
                    $to['type'] = $to_address -> type;
                    $to['address'] = trim($address);
                    $email['tos_array'][] = $to;
                }

            } else {

                $to = [];
                $to['type'] = $to_address -> type;
                $to['address'] = $to_address -> address;
                $email['tos_array'][] = $to;

            }
        }

        $email['attachments'] = [];
        $attachment_size = 0;

        if($request -> attachments) {

            foreach (json_decode($request -> attachments) as $attachment) {
                $file = [];
                $file['name'] = $attachment -> filename;
                $file['location'] = $attachment -> file_location;
                $email['attachments'][] = $file;
                $attachment_size += filesize(Storage::disk('public') -> path($attachment -> file_location));

            }

            $attachment_size = get_mb($attachment_size);
            if($attachment_size > 20) {
                $fail = json_encode(['fail' => true, 'attachment_size' => $attachment_size]);
                return ($fail);
            }

        }

        $email['tos'] = [];
        $email['ccs'] = [];
        $email['bccs'] = [];

        foreach ($email['tos_array'] as $to) {

            $to_address = $to['address'];
            $to_name = '';

            if(preg_match('/\<.*\>/', $to['address'])) {
                preg_match('/(.*)[\s]*\<(.*)\>/', $to['address'], $match);
                $to_name = $match[1];
                $to_address = $match[2];
            }

            if($to['type'] == 'to') {
                $email['tos'][] = ['name' => $to_name, 'email' => $to_address];
            } else if($to['type'] == 'cc') {
                $email['ccs'][] = ['name' => $to_name, 'email' => $to_address];
            } else if($to['type'] == 'bcc') {
                $email['bccs'][] = ['name' => $to_name, 'email' => $to_address];
            }

        }

        if($type == 'documents') {
            $new_mail = new Documents($email);
        } else {
            $new_mail = new DefaultEmail($email);
        }

        //return ($new_mail) -> render();

        Mail::to($email['tos'])
            -> cc($email['ccs'])
            -> bcc($email['bccs'])
            -> send($new_mail);

    }

}
