<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions\Add;

use Config;

use App\User;
use Illuminate\Http\Request;
use App\Models\CRM\CRMContacts;
use App\Models\Employees\Agents;
use App\Models\BrightMLS\Offices;
use App\Http\Controllers\Controller;
use App\Models\Commission\Commission;
use App\Models\Resources\LocationData;
use App\Traits\InHouseNotificationEmail;
use App\Notifications\GlobalNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\DocManagement\Earnest\Earnest;
use App\Models\Commission\CommissionBreakdowns;
use App\Models\Employees\TransactionCoordinators;
use App\Models\DocManagement\Checklists\Checklists;
// use App\Models\DocManagement\Checklists\Checklists;
// use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Transactions\Data\ListingsData;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
use App\Models\DocManagement\Transactions\Data\ListingsRemovedData;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklists;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;
use App\Http\Controllers\Agents\DocManagement\Functions\GlobalFunctionsController;
use App\Http\Controllers\Agents\DocManagement\Transactions\Details\TransactionsDetailsController;

class TransactionsAddController extends Controller {

    use InHouseNotificationEmail;

    public function add_transaction(Request $request) {

		$transaction_type = $request -> type;


        $agents = Agents::where('active', 'yes');

        if ($transaction_type == 'listing') {
            $transaction_type_header = 'Listing';
            $agents = $agents -> where('company', 'not like', '%referral%');
        } elseif ($transaction_type == 'contract') {
            $transaction_type_header = 'Contract/Lease';
            $agents = $agents -> where('company', 'not like', '%referral%');
        } elseif ($transaction_type == 'referral') {
            $transaction_type_header = 'Referral Agreement';
        }

        $agents = $agents -> orderBy('last_name') -> get();

        $states = LocationData::ActiveStates();

        return view('agents/doc_management/transactions/add/transaction_add', compact('transaction_type', 'transaction_type_header', 'states', 'agents'));
    }

    public function transaction_add_details_existing(Request $request) {

		$transaction_type = strtolower($request -> transaction_type);
        $bright_type = $request -> bright_type;
        $bright_id = $request -> bright_id;
        $state = $request -> state;
        $Agent_ID = $request -> Agent_ID;
        $tax_id = '';
        $mls_verified = 'no';
        $bright_db_search = '';

        $agent = Agents::find($Agent_ID);

        $agent_bright_mls_id = $agent -> bright_mls_id_md_dc_tp;
        $office_bright_mls_id = 'TAYL1';
        $office_name = 'Taylor Properties';
        if ($state == 'MD' && $agent -> company == 'Anne Arundel Properties') {
            $agent_bright_mls_id = $agent -> bright_mls_id_md_aap;
            $office_bright_mls_id = 'AAP1';
            $office_name = 'Anne Arundel Properties';
        } elseif ($state == 'VA') {
            $agent_bright_mls_id = $agent -> bright_mls_id_va_tp;
            $office_bright_mls_id = 'TAYL13';
        }

        // only pulling tax records from MD
        if ($state == 'MD') {
            $tax_id = $request -> tax_id;
        }

        $select_columns_bright = config('global.select_columns_bright');
        $select_columns_db = explode(',', $select_columns_bright);


        if ($bright_type == 'db_active') {
            $bright_db_search = ListingsData::select($select_columns_db) -> where('ListingId', $bright_id) -> first() -> toArray();
            $mls_verified = 'yes';
        } elseif ($bright_type == 'bright') {
            $rets_config = new \PHRETS\Configuration;
            $rets_config -> setLoginUrl(config('rets.rets.url'))
                -> setUsername(config('rets.rets.username'))
                -> setPassword(config('rets.rets.password'))
                -> setRetsVersion('RETS/1.8')
                -> setUserAgent('Bright RETS Application/1.0')
                -> setHttpAuthenticationMethod('digest')
                -> setOption('disable_follow_location', false); // or 'basic' if required
            // -> setOption('use_post_method', true)

            $rets = new \PHRETS\Session($rets_config);
            $connect = $rets -> Login();
            $resource = 'Property';
            $class = 'ALL';
            $query = '(ListingId='.$bright_id.')';

            $bright_db_search = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Count' => 0,
                    'Select' => $select_columns_bright,
                ]
            );

            $bright_db_search = $bright_db_search[0]-> toArray();

            // remove all fields that do not apply to current listing
            //if ($bright_db_search['MlsStatus'] == 'CLOSED' && $bright_db_search['CloseDate'] <td date("Y-m-d", strtotime("-1 year"))) {
            $bright_db_search['MlsStatus'] = '';
            $bright_db_search['CloseDate'] = '';
            $bright_db_search['ListingId'] = '';
            $bright_db_search['ListPrice'] = '';
            $bright_db_search['PropertyType'] = '';
            $bright_db_search['PropertySubType'] = '';
            $bright_db_search['MLSListDate'] = '';
            $bright_db_search['PublicRemarks'] = '';
            $bright_db_search['NewConstructionYN'] = '';

            if ($transaction_type == 'listing') {
                $bright_db_search['ListAgentEmail'] = $agent -> email;
                $bright_db_search['ListAgentFirstName'] = $agent -> first_name;
                $bright_db_search['ListAgentLastName'] = $agent -> last_name;
                $bright_db_search['ListAgentMlsId'] = $agent_bright_mls_id;
                $bright_db_search['ListAgentPreferredPhone'] = $agent -> cell_phone;
                $bright_db_search['ListOfficeMlsId'] = $office_bright_mls_id;
                $bright_db_search['ListOfficeName'] = $office_name;

                $bright_db_search['BuyerAgentEmail'] = '';
                $bright_db_search['BuyerAgentFirstName'] = '';
                $bright_db_search['BuyerAgentLastName'] = '';
                $bright_db_search['BuyerAgentMlsId'] = '';
                $bright_db_search['BuyerAgentPreferredPhone'] = '';
                $bright_db_search['BuyerOfficeMlsId'] = '';
                $bright_db_search['BuyerOfficeName'] = '';
            } elseif ($transaction_type == 'contract') {
                $bright_db_search['ListAgentEmail'] = '';
                $bright_db_search['ListAgentFirstName'] = '';
                $bright_db_search['ListAgentLastName'] = '';
                $bright_db_search['ListAgentMlsId'] = '';
                $bright_db_search['ListAgentPreferredPhone'] = '';
                $bright_db_search['ListOfficeMlsId'] = '';
                $bright_db_search['ListOfficeName'] = '';

                $bright_db_search['BuyerAgentEmail'] = $agent -> email;
                $bright_db_search['BuyerAgentFirstName'] = $agent -> first_name;
                $bright_db_search['BuyerAgentLastName'] = $agent -> last_name;
                $bright_db_search['BuyerAgentMlsId'] = $agent_bright_mls_id;
                $bright_db_search['BuyerAgentPreferredPhone'] = $agent -> cell_phone;
                $bright_db_search['BuyerOfficeMlsId'] = $office_bright_mls_id;
                $bright_db_search['BuyerOfficeName'] = $office_name;
            }


            $rets -> disconnect();
        }

        $tax_record_search = null;
        if ($tax_id != '') {
            $functions = new GlobalFunctionsController();
            $tax_record_search = $functions -> tax_records('', '', '', '', $tax_id, $state);
        }

        $property_details = [];

        // if only brightmls results
        if ($bright_db_search && ! $tax_record_search) {
            $property_details = $bright_db_search;

        // if only tax record results
        } elseif (! $bright_db_search && $tax_record_search) {
            $property_details = $tax_record_search;
        } elseif ($bright_db_search && $tax_record_search) {

            // keep bright results, replace a few and add rest from tax records
            $property_details = array_merge($tax_record_search, $bright_db_search);
        }

        if (!$bright_db_search || $bright_type == 'db_active') {
            if ($transaction_type == 'listing') {
                $property_details['ListAgentEmail'] = $agent -> email;
                $property_details['ListAgentFirstName'] = $agent -> first_name;
                $property_details['ListAgentLastName'] = $agent -> last_name;
                $property_details['ListAgentMlsId'] = $agent_bright_mls_id;
                $property_details['ListAgentPreferredPhone'] = $agent -> cell_phone;
                $property_details['ListOfficeMlsId'] = $office_bright_mls_id;
                $property_details['ListOfficeName'] = $office_name;
            } elseif ($transaction_type == 'contract') {
                $property_details['BuyerAgentEmail'] = $agent -> email;
                $property_details['BuyerAgentFirstName'] = $agent -> first_name;
                $property_details['BuyerAgentLastName'] = $agent -> last_name;
                $property_details['BuyerAgentMlsId'] = $agent_bright_mls_id;
                $property_details['BuyerAgentPreferredPhone'] = $agent -> cell_phone;
                $property_details['BuyerOfficeMlsId'] = $office_bright_mls_id;
                $property_details['BuyerOfficeName'] = $office_name;
            }
        }

        $property_details['MLS_Verified'] = $mls_verified;
        $property_details['transaction_type'] = $transaction_type;

        $property_details['FullStreetAddress'] = ucwords(strtolower($property_details['FullStreetAddress']));
        $property_details['City'] = ucwords(strtolower($property_details['City']));
        $property_details['County'] = ucwords(strtolower($property_details['County']));

        $property_details = (object) $property_details;

        $property_type_resource_ids = Checklists::where('active', 'yes') -> pluck('checklist_property_type_id');
        $property_types = ResourceItems::where('resource_type', 'checklist_property_types')
            -> whereIn('resource_id', $property_type_resource_ids)
            -> orderBy('resource_order') -> get();

        $property_sub_type_resource_ids = Checklists::where('active', 'yes') -> pluck('checklist_property_sub_type_id');
        $property_sub_types = ResourceItems::where('resource_type', 'checklist_property_sub_types')
            -> whereIn('resource_id', $property_sub_type_resource_ids)
            -> orderBy('resource_order') -> get();

        $request -> session() -> put('property_details', $property_details);

        return view('agents/doc_management/transactions/add/transaction_add_details', compact('Agent_ID', 'property_details', 'property_types', 'property_sub_types'));
    }



    public function transaction_add_details_referral(Request $request) {

		$status = ResourceItems::GetResourceID('Active', 'referral_status');
        $property_details = [
            'FullStreetAddress' => $request -> street_number.' '.ucwords(strtolower($request -> street_name)).' '.$request -> street_dir.' '.$request -> unit_number,
            'StreetNumber' => $request -> street_number,
            'StreetName' => ucwords(strtolower($request -> street_name)),
            'StreetDirPrefix' => $request -> street_dir,
            'UnitNumber' => $request -> unit_number,
            'City' => ucwords(strtolower($request -> city)),
            'StateOrProvince' => $request -> state,
            'PostalCode' => $request -> zip,
            'County' => ucwords(strtolower($request -> county)),
            'Agent_ID' => $request -> Agent_ID,
            'Status' => $status,
        ];

        $property_details = (object) $property_details;

        $add_referral = new Referrals();
        $add_referral -> Status = ResourceItems::GetResourceID('Active', 'referral_status');
        foreach ($property_details as $key => $val) {
            $add_referral -> $key = $val;
        }
        $add_referral -> save();

        $Referral_ID = $add_referral -> Referral_ID;

        // add to commission and get commission id
        $commission = new Commission();
        $commission -> Referral_ID = $Referral_ID;
        $commission -> Agent_ID = $request -> Agent_ID;
        $commission -> save();
        $Commission_ID = $commission -> id;

        $commission_breakdown = new CommissionBreakdowns();
        $commission_breakdown -> Commission_ID = $Commission_ID;
        $commission_breakdown -> Referral_ID = $Referral_ID;
        $commission_breakdown -> Agent_ID = $request -> Agent_ID;
        $commission_breakdown -> save();

        // add email address
        $address = preg_replace(config('global.bad_characters'), '', $request -> street_number.ucwords(strtolower($request -> street_name)).$request -> street_dir.$request -> unit_number);
        $email = $address.'_'.$Referral_ID.'R@'.config('global.property_email');

        $add_referral -> PropertyEmail = $email;
        $add_referral -> Commission_ID = $Commission_ID;
        $add_referral -> save();

        return response() -> json(['Referral_ID' => $Referral_ID]);
    }

    public function transaction_required_details_referral(Request $request) {

		$Referral_ID = $request -> Referral_ID;
        $referral = Referrals::find($Referral_ID);
        $states = LocationData::AllStates();

        return view('agents/doc_management/transactions/add/transaction_required_details_referral', compact('referral', 'states'));
    }

    public function transaction_save_details_referral(Request $request) {

		$Referral_ID = $request -> Referral_ID;
        $Agent_ID = $request -> Agent_ID;
        $data = $request -> all();
        $referral = Referrals::find($Referral_ID);
        foreach ($data as $key => $value) {
            if ($key != 'Referral_ID' && $key != 'hidden') {
                if (preg_match('/\$/', $value)) {
                    $value = preg_replace('/[\$,]/', '', $value);
                }
                $referral -> $key = $value;
            }
        }
        $referral -> save();

        // Add folders
        $docs_folder = new TransactionDocumentsFolders();
        $docs_folder -> Referral_ID = $Referral_ID;
        $docs_folder -> Agent_ID = $Agent_ID;
        $docs_folder -> folder_name = 'Referral Documents';
        $docs_folder -> folder_order = 0;
        $docs_folder -> save();

        $trash_folder = new TransactionDocumentsFolders();
        $trash_folder -> Referral_ID = $Referral_ID;
        $trash_folder -> Agent_ID = $Agent_ID;
        $trash_folder -> folder_name = 'Trash';
        $trash_folder -> folder_order = 100;
        $trash_folder -> save();

        // Add checklist
        TransactionChecklists::CreateTransactionChecklist('', '', '', $Referral_ID, $Agent_ID, '', 'referral', '', '', '', '', '', '', '');

        return true;
    }



    public function transaction_add_details_new(Request $request) {

		$transaction_type = $request -> transaction_type;
        $Agent_ID = $request -> Agent_ID;
        $agent = Agents::find($Agent_ID);
        $state = $request -> state;

        $agent_bright_mls_id = $agent -> bright_mls_id_md_dc_tp;
        $office_bright_mls_id = 'TAYL1';
        $office_name = 'Taylor Properties';
        if ($state == 'MD' && $agent -> company == 'Anne Arundel Properties') {
            $agent_bright_mls_id = $agent -> bright_mls_id_md_aap;
            $office_bright_mls_id = 'AAP1';
            $office_name = 'Anne Arundel Properties';
        } elseif ($state == 'VA') {
            $agent_bright_mls_id = $agent -> bright_mls_id_va_tp;
            $office_bright_mls_id = 'TAYL13';
        }

        $property_details = [
            'FullStreetAddress' => $request -> street_number.' '.ucwords(strtolower($request -> street_name)).' '.$request -> street_dir.' '.$request -> unit_number,
            'StreetNumber' => $request -> street_number,
            'StreetName' => ucwords(strtolower($request -> street_name)),
            'StreetDirPrefix' => $request -> street_dir,
            'UnitNumber' => $request -> unit_number,
            'City' => ucwords(strtolower($request -> city)),
            'StateOrProvince' => $request -> state,
            'PostalCode' => $request -> zip,
            'County' => ucwords(strtolower($request -> county)),
        ];

        if ($transaction_type == 'listing') {
            $property_details['ListAgentEmail'] = $agent -> email;
            $property_details['ListAgentFirstName'] = $agent -> first_name;
            $property_details['ListAgentLastName'] = $agent -> last_name;
            $property_details['ListAgentMlsId'] = $agent_bright_mls_id;
            $property_details['ListAgentPreferredPhone'] = $agent -> cell_phone;
            $property_details['ListOfficeMlsId'] = $office_bright_mls_id;
            $property_details['ListOfficeName'] = $office_name;
        } elseif ($transaction_type == 'contract') {
            $property_details['BuyerAgentEmail'] = $agent -> email;
            $property_details['BuyerAgentFirstName'] = $agent -> first_name;
            $property_details['BuyerAgentLastName'] = $agent -> last_name;
            $property_details['BuyerAgentMlsId'] = $agent_bright_mls_id;
            $property_details['BuyerAgentPreferredPhone'] = $agent -> cell_phone;
            $property_details['BuyerOfficeMlsId'] = $office_bright_mls_id;
            $property_details['BuyerOfficeName'] = $office_name;
        }

        $property_details['transaction_type'] = $transaction_type;
        $property_details = (object) $property_details;

        $resource_items = new ResourceItems();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = $resource_items -> where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();

        $request -> session() -> put('property_details', $property_details);

        return view('agents/doc_management/transactions/add/transaction_add_details', compact('Agent_ID', 'property_details', 'property_types', 'property_sub_types'));
    }



    public function save_add_transaction(Request $request) {

		$property_details = (object) session('property_details');
        $transaction_type = $request -> transaction_type;
        unset($property_details -> transaction_type);
        $resource_items = new ResourceItems();

        // TODO add more agent fields

        // add user id if logged in, otherwise it will be added from admin
        if ($request -> Agent_ID) {
            $agent = Agents::AgentDetails($request -> Agent_ID);
        } else {
            $agent = \Session::get('user_details');
        }
        // add agent details
        $property_details -> Agent_ID = $agent -> id;

        if ($transaction_type == 'contract') {
            if ($request -> listing_type == 'sale') {
                $property_details -> ContractPrice = preg_replace('/[\$,]+/', '', $request -> contract_price) ?? null;
            } else {
                $property_details -> LeaseAmount = preg_replace('/[\$,]+/', '', $request -> contract_price) ?? null;
            }
        }

        // replace current values from property details with new data
        $property_details -> SaleRent = $request -> listing_type;
        $property_details -> PropertyType = $resource_items -> GetResourceID($request -> property_type, 'checklist_property_types'); // convert to integer
        $property_details -> PropertySubType = $resource_items -> GetResourceID($request -> property_sub_type, 'checklist_property_sub_types'); // convert to integer
        $property_details -> YearBuilt = $request -> year_built ?? null;
        $property_details -> ListPrice = preg_replace('/[\$,]+/', '', $request -> list_price) ?? null;
        $property_details -> HoaCondoFees = $request -> hoa_condo ?? null;
        if ($property_details -> StateOrProvince == 'MD') {
            $location_id = $resource_items -> GetResourceID($property_details -> County, 'checklist_locations');
        } else {
            $location_id = $resource_items -> GetResourceID($property_details -> StateOrProvince, 'checklist_locations');
        }
        $property_details -> Location_ID = $location_id;

        $new_transaction = new Contracts;
        $new_transaction -> Status = ResourceItems::GetResourceID('Active', 'contract_status');
        if ($transaction_type == 'listing') {
            $new_transaction = new Listings;
            $new_transaction -> Status = ResourceItems::GetResourceID('Active', 'listing_status');
        }

        foreach ($property_details as $key => $val) {
            $new_transaction -> $key = $val ?? null;
        }

        if (auth() -> user() -> group == 'transaction_coordinator') {
            $new_transaction -> TransactionCoordinator_ID = auth() -> user() -> user_id;
        }

        $new_transaction -> save();

        if ($transaction_type == 'listing') {
            $code = $new_transaction -> Listing_ID.'L';
        } elseif ($transaction_type == 'contract') {
            $code = $new_transaction -> Contract_ID.'C';
        }

        $street_address = ucwords(strtolower($property_details -> FullStreetAddress));

        $Commission_ID = '';
        $Earnest_ID = '';
        if ($transaction_type == 'contract') {
            // add to commission and get commission id
            $commission = new Commission();
            if ($transaction_type == 'contract') {
                $commission -> Contract_ID = $new_transaction -> Contract_ID;
            } elseif ($transaction_type == 'referral') {
                $commission -> Referral_ID = $new_transaction -> Referral_ID;
            }
            $commission -> Agent_ID = $request -> Agent_ID;
            $commission -> save();
            $Commission_ID = $commission -> id;

            $commission_breakdown = new CommissionBreakdowns();
            $commission_breakdown -> Commission_ID = $Commission_ID;
            $commission_breakdown -> Contract_ID = $new_transaction -> Contract_ID;
            $commission_breakdown -> Referral_ID = $new_transaction -> Referral_ID;
            $commission_breakdown -> Agent_ID = $request -> Agent_ID;
            $commission_breakdown -> save();

            // add to earnest
            $add_earnest = new Earnest();
            $add_earnest -> Contract_ID = $new_transaction -> Contract_ID;
            $add_earnest -> Agent_ID = $request -> Agent_ID;
            $add_earnest -> save();
            $Earnest_ID = $add_earnest -> id;
        }

        // add email address
        $address = preg_replace(config('global.bad_characters'), '', $street_address);
        $email = $address.'_'.$code.'@'.config('global.property_email');

        $new_transaction -> Commission_ID = $Commission_ID;
        $new_transaction -> Earnest_ID = $Earnest_ID;
        $new_transaction -> PropertyEmail = $email;
        $new_transaction -> save();

        // add default docs folders
        $Listing_ID = '';
        $Contract_ID = '';
        $Referral_ID = '';
        if ($transaction_type == 'listing') {
            $Listing_ID = $new_transaction -> Listing_ID;
            $new_folder = new TransactionDocumentsFolders();
            $new_folder -> Listing_ID = $Listing_ID;
            $new_folder -> Contract_ID = $Contract_ID;
            $new_folder -> Agent_ID = $agent -> id;
            $new_folder -> folder_name = 'Listing Documents';
            $new_folder -> folder_order = 0;
            $new_folder -> save();
        } elseif ($transaction_type == 'contract') {
            $Contract_ID = $new_transaction -> Contract_ID;
        } elseif ($transaction_type == 'referral') {
            $Referral_ID = $new_transaction -> Referral_ID;
        }

        $type = $transaction_type == 'referral' ? 'Referral' : 'Contract';

        $new_folder = new TransactionDocumentsFolders();
        $new_folder -> Listing_ID = $Listing_ID;
        $new_folder -> Contract_ID = $Contract_ID;
        $new_folder -> Referral_ID = $Referral_ID;
        $new_folder -> Agent_ID = $agent -> id;
        $new_folder -> folder_name = $type.' Documents';
        $new_folder -> folder_order = 0;
        $new_folder -> save();

        $new_folder = new TransactionDocumentsFolders();
        $new_folder -> Listing_ID = $Listing_ID;
        $new_folder -> Contract_ID = $Contract_ID;
        $new_folder -> Referral_ID = $Referral_ID;
        $new_folder -> Agent_ID = $agent -> id;
        $new_folder -> folder_name = 'Trash';
        $new_folder -> folder_order = 100;
        $new_folder -> save();

        return response() -> json(['id' => $Listing_ID.$Contract_ID.$Referral_ID]);
    }



    public function transaction_required_details(Request $request) {

		$transaction_type = $request -> transaction_type;
        $id = $request -> id;

        //$property = Contracts::find($id);

        $property = Listings::GetPropertyDetails($transaction_type, $id);

        $Agent_ID = $property -> Agent_ID;

        $office = null;
        if ($property -> ListOfficeMlsId != '') {
            $office = Offices::where('OfficeMlsId', $property -> ListOfficeMlsId) -> first();
        }

        $for_sale = true;
        if ($property -> SaleRent == 'rental') {
            $for_sale = false;
        }

        $states = LocationData::AllStates();
        $states_json = $states -> toJson();
        $statuses = ResourceItems::where('resource_type', 'listing_status') -> orderBy('resource_order') -> get();

        $contacts = [];
        if (stristr(auth() -> user() -> group, 'agent') || auth() -> user() -> group == 'transaction_coordinator') {
            $contacts = CRMContacts::where('Agent_ID', $Agent_ID) -> get();
        } elseif (auth() -> user() -> group == 'admin') {
            $contacts = CRMContacts::get();
        }

        $resource_items = new ResourceItems();

        /* $transaction_type_header = 'Contract/Lease';
        if($transaction_type == 'listing') {
            $transaction_type_header = 'Listing';
        } else if($transaction_type == 'referral') {
            $transaction_type_header = 'Referral Agreement';
        } */

        return view('agents/doc_management/transactions/add/transaction_required_details_'.$transaction_type, compact('property', 'office', 'for_sale', 'states', 'states_json', 'statuses', 'contacts', 'resource_items', 'transaction_type'));
    }

    public function save_transaction_required_details(Request $request) {

		$Listing_ID = $request -> Listing_ID ?? null;
        $Contract_ID = $request -> Contract_ID ?? null;
        $Agent_ID = $request -> Agent_ID;
        $transaction_type = $request -> transaction_type;

        if ($transaction_type == 'contract') {
            $field = 'Contract_ID';
            $id = $Contract_ID;
            $property = Contracts::find($Contract_ID);
        } else {
            $field = 'Listing_ID';
            $id = $Listing_ID;
            $property = Listings::find($Listing_ID);
        }

        $agent = Agents::where('id', $Agent_ID) -> first();
        // get state to see which bright_mls_id is required
        $state = $property -> StateOrProvince;

        $bright_mls_id = $agent -> bright_mls_id_md_dc_tp;
        if ($state == 'MD' && $agent -> company == 'Anne Arundel Properties') {
            $bright_mls_id = $agent -> bright_mls_id_md_aap;
        } elseif ($state == 'VA') {
            $bright_mls_id = $agent -> bright_mls_id_va_tp;
        }

        // get property details to add listing agent to members if contract
        if ($transaction_type == 'contract') {

            $listing_agent = new Members();

            $listing_agent -> first_name = $request -> ListAgentFirstName;
            $listing_agent -> last_name = $request -> ListAgentLastName;
            $listing_agent -> cell_phone = $request -> ListAgentPreferredPhone;
            $listing_agent -> email = $request -> ListAgentEmail;
            $listing_agent -> company = $request -> ListAgentOfficeName;

            // add list agent details to property
            $property -> ListAgentFirstName = $request -> ListAgentFirstName;
            $property -> ListAgentLastName = $request -> ListAgentLastName;
            $property -> ListAgentPreferredPhone = $request -> ListAgentPreferredPhone;
            $property -> ListAgentEmail = $request -> ListAgentEmail;
            $property -> ListOfficeName = $request -> ListAgentOfficeName;

            $list_office_mls_id = $request -> ListOfficeMlsId ?? null;

            $listing_agent -> bright_mls_id = $request -> ListAgentMlsId;
            $listing_agent -> address_office_street = $request -> ListAgentOfficeStreet;
            $listing_agent -> address_office_city = $request -> ListAgentOfficeCity;
            $listing_agent -> address_office_state = $request -> ListAgentOfficeState;
            $listing_agent -> address_office_zip = $request -> ListAgentOfficeZip;
            $listing_agent -> Contract_ID = $Contract_ID;
            $listing_agent -> member_type_id = ResourceItems::ListingAgentResourceId();
            $listing_agent -> transaction_type = 'contract';

            $listing_agent -> save();


            $buyers_agent = new Members();
            $buyers_agent -> first_name = $agent -> first_name;
            $buyers_agent -> last_name = $agent -> last_name;
            $buyers_agent -> cell_phone = $agent -> cell_phone;
            $buyers_agent -> email = $agent -> email;
            $buyers_agent -> company = $agent -> company;
            $buyers_agent -> bright_mls_id = $bright_mls_id;
            $buyers_agent -> address_office_street = config('global.company_street');
            $buyers_agent -> address_office_city = config('global.company_city');
            $buyers_agent -> address_office_state = config('global.company_state');
            $buyers_agent -> address_office_zip = config('global.company_zip');
            $buyers_agent -> Contract_ID = $Contract_ID;
            $buyers_agent -> Agent_ID = $Agent_ID;
            $buyers_agent -> member_type_id = ResourceItems::BuyerAgentResourceId();
            $buyers_agent -> transaction_type = 'contract';
            $buyers_agent -> disabled = true;
            $buyers_agent -> save();

            // add list agent details to property
            $property -> BuyerAgentFirstName = $agent -> first_name;
            $property -> BuyerAgentLastName = $agent -> last_name;
            $property -> BuyerAgentPreferredPhone = $agent -> cell_phone;
            $property -> BuyerAgentEmail = $agent -> email;
            $property -> BuyerOfficeName = $agent -> company;

            // if using heritage add them to members
            if ($request -> UsingHeritage == 'yes') {
                $add_heritage_to_members = new Members();
                $add_heritage_to_members -> member_type_id = ResourceItems::TitleResourceId();
                $add_heritage_to_members -> company = 'Heritage Title';
                $add_heritage_to_members -> cell_phone = '(866) 913-4095';
                $add_heritage_to_members -> address_office_street = '175 Admiral Cochrane Dr., Suite 111';
                $add_heritage_to_members -> address_office_city = 'Annapolis';
                $add_heritage_to_members -> address_office_state = 'MD';
                $add_heritage_to_members -> address_office_zip = '21401';
                $add_heritage_to_members -> Contract_ID = $Contract_ID;
                $add_heritage_to_members -> transaction_type = 'contract';
                $add_heritage_to_members -> save();


                // notify heritage title
                $notification = config('notifications.in_house_notification_emails_using_heritage_title');
                $users = User::whereIn('email', $notification['emails']) -> get();

                $agent = $property -> agent;

                $subject = 'Agent Using Heritage Title Notification';
                $message = $agent -> full_name.' will be using Heritage Title for their contract.<br>'.$property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
                $message_email = '
                <div style="font-size: 15px; width:100%;" width="100%">
                An agent from '.$agent -> company.' has selected that they will be using Heritage Title for the contract on their listing.
                <br><br>
                <table>
                    <tr>
                        <td valign="top">Agent</td>
                        <td>'.$agent -> full_name.'<br>'.$agent -> cell_phone.'<br>'.$agent -> email.'</td>
                    </tr>
                    <tr><td colspan="2" height="20"></td></tr>
                    <tr>
                        <td valign="top">Property</td>
                        <td>'.$property -> FullStreetAddress.'<br>'.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode.'
                            <br>
                            <a href="'.config('app.url').'/agents/doc_management/transactions/transaction_details/'.$Contract_ID.'/contract" target="_blank">View Transaction</a>
                        </td>
                    </tr>
                </table>
                <br><br>
                Thank You,<br>
                Taylor Properties
                </div>';

                $notification['type'] = 'using_heritage_title';
                $notification['sub_type'] = 'contract';
                $notification['sub_type_id'] = $property -> Contract_ID;
                $notification['subject'] = $subject;
                $notification['message'] = $message;
                $notification['message_email'] = $message_email;

                Notification::send($users, new GlobalNotification($notification));

            }

        } else {

            // add listing agent to members if just a listing
            $listing_agent = new Members();
            $listing_agent -> first_name = $agent -> first_name;
            $listing_agent -> last_name = $agent -> last_name;
            $listing_agent -> cell_phone = $agent -> cell_phone;
            $listing_agent -> email = $agent -> email;
            $listing_agent -> company = $agent -> company;
            $listing_agent -> bright_mls_id = $bright_mls_id;
            $listing_agent -> address_office_street = config('global.company_street');
            $listing_agent -> address_office_city = config('global.company_city');
            $listing_agent -> address_office_state = config('global.company_state');
            $listing_agent -> address_office_zip = config('global.company_zip');
            $listing_agent -> Listing_ID = $Listing_ID;
            $listing_agent -> Agent_ID = $Agent_ID;
            $listing_agent -> member_type_id = ResourceItems::ListingAgentResourceId();
            $listing_agent -> transaction_type = 'listing';
            $listing_agent -> disabled = true;
            $listing_agent -> save();

            /* $broker = new Members();
            $broker -> company = $agent -> company;
            $broker -> address_office_street = config('global.company_street');
            $broker -> address_office_city = config('global.company_city');
            $broker -> address_office_state = config('global.company_state');
            $broker -> address_office_zip = config('global.company_zip');
            $broker -> Listing_ID = $Listing_ID;
            $broker -> Agent_ID = $Agent_ID;
            $broker -> member_type_id = ResourceItems::BrokerResourceId();
            $broker -> disabled = true;
            $broker -> save(); */

            $property -> ListAgentFirstName = $agent -> first_name;
            $property -> ListAgentLastName = $agent -> last_name;
            $property -> ListAgentEmail = $agent -> email;
            $property -> ListAgentPreferredPhone = $agent -> cell_phone;
            $property -> ListAgentMlsId = $bright_mls_id;
            $property -> ListOfficeName = $agent -> company;

            $office_bright_mls_id = 'AAP1';
            if ($property -> StateOrProvince == 'VA') {
                $office_bright_mls_id = 'TAYL13';
            } else {
                if (stristr($agent -> company, 'taylor')) {
                    $office_bright_mls_id = 'TAYL1';
                }
            }
            $property -> ListOfficeMlsId = $office_bright_mls_id;
        }

        // add transaction coordinator to members
        if($property -> TransactionCoordinator_ID > 0) {

            $transaction_coordinator = TransactionCoordinators::find($property -> TransactionCoordinator_ID);

            $transaction_coordinator_member = new Members();
            $transaction_coordinator_member -> first_name = $transaction_coordinator -> first_name;
            $transaction_coordinator_member -> last_name = $transaction_coordinator -> last_name;
            $transaction_coordinator_member -> cell_phone = $transaction_coordinator -> cell_phone;
            $transaction_coordinator_member -> email = $transaction_coordinator -> email;
            $transaction_coordinator_member -> member_type_id = ResourceItems::TransactionCoordinatorResourceId();
            $transaction_coordinator_member -> TransactionCoordinator_ID = $property -> TransactionCoordinator_ID;
            $transaction_coordinator_member -> transaction_type = 'listing';
            $transaction_coordinator_member -> Listing_ID = $Listing_ID;
            $transaction_coordinator_member -> Contract_ID = $Contract_ID;
            $transaction_coordinator_member -> save();

        }

        // add sellers to doc_members
        $seller_first = $request -> seller_first_name;
        $seller_last = $request -> seller_last_name;
        $seller_phone = $request -> seller_phone;
        $seller_email = $request -> seller_email;
        $seller_entity_name = $request -> seller_entity_name;
        $seller_address_street = $request -> seller_street;
        $seller_address_city = $request -> seller_city;
        $seller_address_state = $request -> seller_state;
        $seller_address_zip = $request -> seller_zip;
        $seller_crm_contact_id = $request -> seller_crm_contact_id;

        $seller_member_type_id = ResourceItems::SellerResourceId();
        $buyer_member_type_id = ResourceItems::BuyerResourceId();

        for ($i = 0; $i < count($seller_first); $i++) {
            $sellers = null;
            if ($seller_email) {
                if ($seller_email[$i] != '') {
                    $sellers = Members::where('email', $seller_email[$i]) -> where($field, $id) -> first();
                }
            }
            if (! $sellers) {
                $sellers = new Members();
            }
            if ($i == 0) {
                $sellers -> entity_name = $seller_entity_name;
            }
            $sellers -> first_name = $seller_first[$i];
            $sellers -> last_name = $seller_last[$i];
            $sellers -> cell_phone = $seller_phone[$i] ?? null;
            $sellers -> email = $seller_email[$i] ?? null;
            $sellers -> address_home_street = $seller_address_street[$i] ?? null;
            $sellers -> address_home_city = $seller_address_city[$i] ?? null;
            $sellers -> address_home_state = $seller_address_state[$i] ?? null;
            $sellers -> address_home_zip = $seller_address_zip[$i] ?? null;
            $sellers -> CRMContact_ID = $seller_crm_contact_id[$i] ?? 0;
            $sellers -> member_type_id = $seller_member_type_id;
            $sellers -> transaction_type = 'listing';
            $sellers -> Listing_ID = $Listing_ID;
            $sellers -> Contract_ID = $Contract_ID;
            $sellers -> save();

            $exists = CRMContacts::where('contact_email', $seller_email[$i]) -> first();
            if(!$exists && !$seller_crm_contact_id[$i] > 0) {
                $contact = new CRMContacts();
                $contact -> contact_first = $seller_first[$i];
                $contact -> contact_last = $seller_last[$i];
                $contact -> contact_phone_cell = $seller_phone[$i];
                $contact -> contact_email = $seller_email[$i];
                $contact -> contact_street = $seller_address_street[$i];
                $contact -> contact_city = $seller_address_city[$i];
                $contact -> contact_state = $seller_address_state[$i];
                $contact -> contact_zip = $seller_address_zip[$i];
                $contact -> Agent_ID = $Agent_ID;
                $contact -> save();
            }

            if ($i == 0) {
                $seller_one_first = $seller_entity_name;
                $seller_one_last = '';
                if ($seller_entity_name == '') {
                    $seller_one_first = $seller_first[$i];
                    $seller_one_last = $seller_last[$i];
                }
                $seller_two_first = null;
                $seller_two_last = null;
            } elseif ($i == 1) {
                $seller_two_first = $seller_first[$i];
                $seller_two_last = $seller_last[$i];
            }
        }

        // add buyers to doc_members
        if ($request -> buyer_first_name) {
            $buyer_entity_name = $request -> buyer_entity_name;
            $buyer_first = $request -> buyer_first_name;
            $buyer_last = $request -> buyer_last_name;
            $buyer_phone = $request -> buyer_phone;
            $buyer_email = $request -> buyer_email;
            $buyer_address_street = $request -> buyer_street;
            $buyer_address_city = $request -> buyer_city;
            $buyer_address_state = $request -> buyer_state;
            $buyer_address_zip = $request -> buyer_zip;
            $buyer_crm_contact_id = $request -> buyer_crm_contact_id;

            for ($i = 0; $i < count($buyer_first); $i++) {
                $buyers = null;
                if ($buyer_email) {
                    if ($buyer_email[$i] != '') {
                        $buyers = Members::where('email', $buyer_email[$i]) -> where($field, $id) -> first();
                    }
                }
                if (! $buyers) {
                    $buyers = new Members();
                }
                if ($i == 0) {
                    $buyers -> entity_name = $buyer_entity_name;
                }
                $buyers -> first_name = $buyer_first[$i];
                $buyers -> last_name = $buyer_last[$i];
                $buyers -> cell_phone = $buyer_phone[$i] ?? null;
                $buyers -> email = $buyer_email[$i] ?? null;
                $buyers -> address_home_street = $buyer_address_street[$i] ?? null;
                $buyers -> address_home_city = $buyer_address_city[$i] ?? null;
                $buyers -> address_home_state = $buyer_address_state[$i] ?? null;
                $buyers -> address_home_zip = $buyer_address_zip[$i] ?? null;
                $buyers -> CRMContact_ID = $buyer_crm_contact_id[$i] ?? 0;
                $buyers -> member_type_id = $buyer_member_type_id;
                $buyers -> transaction_type = 'contract';
                $buyers -> Listing_ID = $Listing_ID;
                $buyers -> Contract_ID = $Contract_ID;
                $buyers -> save();

                $exists = CRMContacts::where('contact_email', $buyer_email[$i]) -> first();
                if(!$exists && !$buyer_crm_contact_id[$i] > 0) {
                    $contact = new CRMContacts();
                    $contact -> contact_first = $buyer_first[$i];
                    $contact -> contact_last = $buyer_last[$i];
                    $contact -> contact_phone_cell = $buyer_phone[$i];
                    $contact -> contact_email = $buyer_email[$i];
                    $contact -> contact_street = $buyer_address_street[$i];
                    $contact -> contact_city = $buyer_address_city[$i];
                    $contact -> contact_state = $buyer_address_state[$i];
                    $contact -> contact_zip = $buyer_address_zip[$i];
                    $contact -> Agent_ID = $Agent_ID;
                    $contact -> save();
                }

                if ($i == 0) {
                    $buyer_one_first = $buyer_first[$i];
                    $buyer_one_last = $buyer_last[$i];
                    $buyer_two_first = null;
                    $buyer_two_last = null;
                } elseif ($i == 1) {
                    $buyer_two_first = $buyer_first[$i];
                    $buyer_two_last = $buyer_last[$i];
                }
            }
        }

        $update_transaction_members = (new TransactionsDetailsController) -> update_transaction_members($id, $transaction_type);

        if ($transaction_type == 'listing') {

            // update listing
            $property -> SellerOneFirstName = $seller_one_first;
            $property -> SellerOneLastName = $seller_one_last;
            $property -> SellerTwoFirstName = $seller_two_first;
            $property -> SellerTwoLastName = $seller_two_last;
            $property -> MLSListDate = $request -> MLSListDate;
            $property -> ExpirationDate = $request -> ExpirationDate;
            if (date('Y-m-d') < $request -> MLSListDate) {
                $property -> Status = ResourceItems::GetResourceID('Pre-Listing', 'listing_status');
            } elseif (date('Y-m-d') >= $request -> MLSListDate && date('Y-m-d') < $request -> ExpirationDate) {
                $property -> Status = ResourceItems::GetResourceID('Active', 'listing_status');
            } else {
                $property -> Status = ResourceItems::GetResourceID('Expired', 'listing_status');
            }
            $property -> save();

            $checklist_property_type_id = $property -> PropertyType;
            $checklist_property_sub_type_id = $property -> PropertySubType;
            $checklist_sale_rent = $property -> SaleRent;
            $checklist_state = $property -> StateOrProvince;
            $checklist_location_id = $property -> Location_ID;
            $checklist_hoa_condo = $property -> HoaCondoFees;
            $checklist_year_built = $property -> YearBuilt;

        } elseif ($transaction_type == 'contract') {

            // update contract
            $property -> SellerOneFirstName = $seller_one_first;
            $property -> SellerOneLastName = $seller_one_last;
            $property -> SellerTwoFirstName = $seller_two_first;
            $property -> SellerTwoLastName = $seller_two_last;
            $property -> BuyerOneFirstName = $buyer_one_first;
            $property -> BuyerOneLastName = $buyer_one_last;
            $property -> BuyerTwoFirstName = $buyer_two_first;
            $property -> BuyerTwoLastName = $buyer_two_last;
            $property -> ContractDate = $request -> ContractDate;
            $property -> CloseDate = $request -> CloseDate;
            $property -> Status = ResourceItems::GetResourceID('Active', 'contract_status');
            $property -> UsingHeritage = $request -> UsingHeritage;
            $property -> TitleCompany = $request -> TitleCompany;
            $property -> EarnestAmount = preg_replace('/[\$,]+/', '', $request -> EarnestAmount);
            $property -> EarnestHeldBy = $request -> EarnestHeldBy;
            $property -> save();

            $checklist_property_type_id = $property -> PropertyType;
            $checklist_property_sub_type_id = $property -> PropertySubType;
            $checklist_sale_rent = $property -> SaleRent;
            $checklist_state = $property -> StateOrProvince;
            $checklist_location_id = $property -> Location_ID;
            $checklist_hoa_condo = $property -> HoaCondoFees;
            $checklist_year_built = $property -> YearBuilt;

            // notify admin
            // if holding earnest
            // add earnest account id to earnest
            if ($request -> EarnestHeldBy == 'us') {
                $earnest_account_id = ResourceItems::where('resource_type', 'earnest_accounts')
                    -> where('resource_state', $property -> StateOrProvince)
                    -> where('resource_name', $agent -> company)
                    -> value('resource_id');

                $earnest = Earnest::where('Contract_ID', $Contract_ID) -> update(['earnest_account_id' => $earnest_account_id, 'held_by' => 'us']);


                // notify earnest admin
                $notification = config('notifications.in_house_notification_emails_holding_earnest');
                $users = User::whereIn('email', $notification['emails']) -> get();

                $subject = 'New Earnest Deposit Notification';
                $message = 'A contract was submitted that we are holding earnest for.<br>
                '.$property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;

                $message_email = '
                <div style="font-size: 15px; width:100%;" width="100%">
                    '.$agent -> full_name.' has indicated that '.$agent -> company.' will be holding the earnest deposit for the property below.
                    <br><br>
                    <table>
                        <tr>
                            <td valign="top">Agent</td>
                            <td>'.$agent -> full_name.'<br>'.$agent -> cell_phone.'<br>'.$agent -> email.'</td>
                        </tr>
                        <tr><td colspan="2" height="20"></td></tr>
                        <tr>
                            <td valign="top">Property</td>
                            <td>'.$property -> FullStreetAddress.'<br>'.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode.'
                            <br>
                            <a href="'.config('app.url').'/agents/doc_management/transactions/transaction_details/'.$Contract_ID.'/contract" target="_blank">View Transaction</a>
                        </td>
                        </tr>
                    </table>
                    <br><br>
                    Thank You,<br>
                    Taylor Properties
                    </div>';

                $notification['type'] = 'earnest';
                $notification['sub_type'] = 'contract';
                $notification['sub_type_id'] = $property -> Contract_ID;
                $notification['subject'] = $subject;
                $notification['message'] = $message;
                $notification['message_email'] = $message_email;

                Notification::send($users, new GlobalNotification($notification));


            }

        }

        // add checklist and checklist items
        $checklist_represent = ($Listing_ID > 0 ? 'seller' : 'buyer');
        $checklist_type = $transaction_type;

        $checklist_id = '';

        TransactionChecklists::CreateTransactionChecklist($checklist_id, $Listing_ID, $Contract_ID, '', $Agent_ID, $checklist_represent, $checklist_type, $checklist_property_type_id, $checklist_property_sub_type_id, $checklist_sale_rent, $checklist_state, $checklist_location_id, $checklist_hoa_condo, $checklist_year_built);

        if ($transaction_type == 'listing') {
            return response() -> json([
                'type' => 'listing',
                'id' => $Listing_ID,
            ]);
        }

        return response() -> json([
            'type' => 'contract',
            'id' => $Contract_ID,
        ]);
    }




    public function get_property_info(Request $request) {

        $request -> session() -> forget('property_details');

		$street_number = $street_name = $unit = $zip = $tax_id = $state = '';

        if ($request -> mls) {
            $ListingId = $request -> mls;
            $state = substr($request -> mls, 0, 2);
        } else {
            $street_number = $request -> street_number;
            $street_name = $request -> street_name;
            $street_suffix = '';
            $street_dir_suffix = '';
            $street_dir_suffix_alt = '';

            // remove all suffixes and dir suffixes to get just street name. Save them for later
            $street_suffixes_array = config('global.street_suffixes');

            $street_dir_suffixes_array = config('global.street_dir_suffixes');

            $street_dir_suffixes_alt_array = [
                ['orig' => 'E', 'alt' => 'EAST'],
                ['orig' => 'EAST', 'alt' => 'E'],
                ['orig' => 'W', 'alt' => 'WEST'],
                ['orig' => 'WEST', 'alt' => 'W'],
                ['orig' => 'S', 'alt' => 'SOUTH'],
                ['orig' => 'SOUTH', 'alt' => 'S'],
                ['orig' => 'N', 'alt' => 'NORTH'],
                ['orig' => 'NORTH', 'alt' => 'N'],
                ['orig' => 'NE', 'alt' => 'NORTHEAST'],
                ['orig' => 'NORTHEAST', 'alt' => 'NE'],
                ['orig' => 'NW', 'alt' => 'NORTHWEST'],
                ['orig' => 'NORTHWEST', 'alt' => 'NW'],
                ['orig' => 'SE', 'alt' => 'SOUTHEAST'],
                ['orig' => 'SOUTHEAST', 'alt' => 'SE'],
                ['orig' => 'SW', 'alt' => 'SOUTHWEST'],
                ['orig' => 'SOUTHWEST', 'alt' => 'SW'],
            ];

            foreach ($street_suffixes_array as $street_suffixes) {
                if (preg_match('/\s\b('.$street_suffixes.'(?!.*'.$street_suffixes.'))\b/i', $street_name, $matches)) {
                    $street_name = preg_replace('/\\s\b('.$street_suffixes.'(?!.*'.$street_suffixes.'))\b/i', '', $street_name);
                    $street_suffix = trim($matches[0]);
                }
            }

            foreach ($street_dir_suffixes_array as $street_dir_suffixes) {

                // do not pull prefixes i.e. 234 SW some st
                if (! preg_match('/[0-9]+\s('.$street_dir_suffixes.')/i', $street_name)) {

                    // only pull suffixes i.e. 234 Main St. Southwest
                    if (preg_match('/\s\b('.$street_dir_suffixes.')\b/i', $street_name, $matches)) {
                        $street_name = preg_replace('/\s\b('.$street_dir_suffixes.')\b/i', '', $street_name);
                        $street_dir_suffix = trim($matches[0]);

                        foreach ($street_dir_suffixes_alt_array as $dir) {
                            if (strtolower($dir['orig']) == strtolower($street_dir_suffix)) {
                                $street_dir_suffix_alt = $dir['alt'];
                            }
                        }
                    }
                }
            }

            $street_dir_suffix_bright_dmql = '';
            if ($street_dir_suffix != '') {
                $street_dir_suffix_bright_dmql = ',(((StreetDirSuffix=|'.$street_dir_suffix.')|(StreetDirSuffix=|'.$street_dir_suffix_alt.'))|((StreetDirPrefix=|'.$street_dir_suffix.')|(StreetDirPrefix=|'.$street_dir_suffix_alt.')))';
            }

            $unit = $request -> unit;
            $unit_bright_dmql = '';
            if ($unit != '') {
                $unit_bright_dmql = ',(UnitNumber=*'.$unit.'*)';
            }

            $city = $request -> city;
            $state = $request -> state;
            $zip = $request -> zip;
            $county = $request -> county;


        }

        $select_columns_db = ['ListPictureURL', 'FullStreetAddress', 'City', 'StateOrProvince', 'County', 'PostalCode', 'YearBuilt', 'BathroomsTotalInteger', 'BedroomsTotal', 'MlsStatus', 'ListingId', 'ListPrice', 'PropertyType', 'ListOfficeName', 'MLSListDate', 'ListAgentFirstName', 'ListAgentLastName', 'UnitNumber', 'CloseDate', 'ListingTaxID'];
        $select_columns_bright = 'ListPictureURL, FullStreetAddress, City, StateOrProvince, County, PostalCode, YearBuilt, BathroomsTotalInteger, BedroomsTotal, MlsStatus, ListingId, ListPrice, PropertyType, ListOfficeName, MLSListDate, ListAgentFirstName, ListAgentLastName, UnitNumber, CloseDate, ListingTaxID';

        $property_details = null;
        $results = [];
        $results['multiple'] = false;

        ///// DATABASE SEARCH FOR PROPERTY /////
        if ($request -> mls) {
            $bright_db_search = ListingsData::select($select_columns_db) -> where('ListingId', $ListingId) -> get() -> toArray();
        } else {
            $bright_db_search = ListingsData::select($select_columns_db) -> ListingSearch($state, $zip, $street_number, $street_name, $unit, $street_dir_suffix, $street_dir_suffix_alt);
        }


        if (count($bright_db_search) > 0) {
            $results['results_bright_type'] = 'db_active';
            if (count($bright_db_search) > 1) {

            // see if results have different unit numbers
                if ($bright_db_search[0]['UnitNumber'] != $bright_db_search[1]['UnitNumber']) {
                    $property_details['multiple'] = true;

                    return $property_details;
                } else {
                    $results['results_bright_id'] = $bright_db_search[0]['ListingId'];
                }
            } else {
                $results['results_bright_id'] = $bright_db_search[0]['ListingId'];
            }
        }

        ///// END DATABASE SEARCH FOR PROPERTY /////

        ///// BRIGHT SEARCH /////

        // If not found in database search search bright mls
        if (count($bright_db_search) == 0) {
            $rets_config = new \PHRETS\Configuration;
            $rets_config -> setLoginUrl(config('rets.rets.url'))
                -> setUsername(config('rets.rets.username'))
                -> setPassword(config('rets.rets.password'))
                -> setRetsVersion('RETS/1.8')
                -> setUserAgent('Bright RETS Application/1.0')
                -> setHttpAuthenticationMethod('digest')
                -> setOption('disable_follow_location', false); // or 'basic' if required
            // -> setOption('use_post_method', true)

            $rets = new \PHRETS\Session($rets_config);
            $connect = $rets -> Login();
            $resource = 'Property';
            $class = 'ALL';

            // get property results from brightmls
            if ($request -> mls) {
                $query = '(ListingId='.$ListingId.')';
            } else {
                $query = '(StateOrProvince=|'.$state.'),(PostalCode='.$zip.'),(StreetNumber='.$street_number.'),(StreetName='.$street_name.'*)'.$unit_bright_dmql.$street_dir_suffix_bright_dmql;
            }

            $bright_db_search = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Count' => 0,
                    'Select' => $select_columns_bright,
                ]
            );

            $bright_db_search = $bright_db_search -> toArray();

            $rets -> disconnect();

            if (count($bright_db_search) > 0) {
                $results['results_bright_type'] = 'bright';
                if (count($bright_db_search) > 1) {

                    // see if results have different unit numbers
                    if ($bright_db_search[0]['UnitNumber'] != $bright_db_search[1]['UnitNumber']) {
                        $property_details['multiple'] = true;

                        return $property_details;
                    } else {
                        $results['results_bright_id'] = $bright_db_search[0]['ListingId'];
                    }
                } else {
                    $results['results_bright_id'] = $bright_db_search[0]['ListingId'];
                }
            }
        }

        ///// END BRIGHT SEARCH /////


        ///// END DATABASE SEARCH FOR OLD PROPERTIES /////

        // get only most recent result
        if (count($bright_db_search) == 0) {
            $bright_db_search = null;
        }

        // get tax id to search for more property details
        $tax_id = '';

        if (isset($bright_db_search[0]['ListingTaxID'])) {
            $tax_id = $bright_db_search[0]['ListingTaxID'];
        }

        // search tax records by tax id if exists, otherwise use address
        $tax_record_search = '';

        if ($state == 'MD') {
            $functions = new GlobalFunctionsController();
            $tax_record_search = $functions -> tax_records($street_number, $street_name, $unit, $zip, $tax_id, $state);

            if (is_array($tax_record_search)) {
                // set tax id in case searched by address
                $results['results_tax_id'] = $tax_record_search['ListingTaxID'];
            }
        }

        // if only brightmls results
        if ($bright_db_search && ! is_array($tax_record_search)) {
            $property_details = $bright_db_search[0];

        // if only tax record results
        } elseif (! $bright_db_search && is_array($tax_record_search)) {
            $property_details = $tax_record_search;

        // if both results
        } elseif ($bright_db_search && is_array($tax_record_search)) {

            // keep bright results, replace a few and add rest from tax records
            $property_details = $bright_db_search[0];
            $property_details['Owner1'] = null;
            $property_details['Owner2'] = null;

            if (isset($tax_record_search['Owner1']) && $tax_record_search['Owner1'] != '') {
                $property_details['Owner1'] = $tax_record_search['Owner1'] ?? null;
                $property_details['Owner2'] = $tax_record_search['Owner2'] ?? null;
            }

            $property_details['ResidenceType'] = $tax_record_search['ResidenceType'] ?? null;
            $property_details['TaxRecordLink'] = $tax_record_search['TaxRecordLink'] ?? null;
            if($property_details['TaxRecordLink']) {
                $property_details['TaxRecordLink'] = str_replace('http:', 'https:', $property_details['TaxRecordLink']);
            }
        } else {
            $property_details = [];
        }

        if (count($property_details) > 0) {
            $property_details['results_bright_type'] = $results['results_bright_type'] ?? null;
            $property_details['results_bright_id'] = $results['results_bright_id'] ?? null;
            $property_details['results_tax_id'] = $results['results_tax_id'] ?? null;
            $property_details['multiple'] = false;
        }

        return $property_details;
    }

    public function update_county_select(Request $request) {

		$counties = LocationData::select('county') -> where('state', $request -> state) -> groupBy('county') -> orderBy('county') -> get() -> toJson();

        return $counties;
    }
}
