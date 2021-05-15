<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Tasks\Tasks;
use Illuminate\Http\Request;
use App\Models\Employees\Agents;
use App\Models\Calendar\Calendar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\BrightMLS\CompanyListings;
use App\Notifications\GlobalNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Jobs\Notifications\MissingTransactionsNotificationJob;
use App\Models\DocManagement\Transactions\Contracts\Contracts;


class TestController extends Controller
{

    public function skyslope(Request $request) {

        $auth = $this -> skyslope_auth();
        $session = $auth['Session'];
        $headers = [
            'Content-Type' => 'application/json',
            'Session' => $session
        ];

        $query = [
            //'earliestDate' => strtotime(date('2021-05-01'))
            //'type' => 'listing',
            'agentGuid' => '6bff1a51-032d-409a-a05e-6fb3bc380e3b'
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'query' => $query
        ]);

        //$r = $client -> request('GET', 'https://api.skyslope.com/api/files/listings');
        //$r = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/2be27e67-5924-44e8-b032-57de5d5877a9');
        //$r = $client -> request('GET', 'https://api.skyslope.com/api/files/listings/2be27e67-5924-44e8-b032-57de5d5877a9/documents');
        //$r = $client -> request('GET', 'https://api.skyslope.com/api/offices');
        $r = $client -> request('GET', 'https://api.skyslope.com/api/files/listings');
        //$r = $client -> request('GET', 'https://api.skyslope.com/api/users');
        //$r = $client -> request('GET', 'https://api.skyslope.com/api/users/6bff1a51-032d-409a-a05e-6fb3bc380e3b');

        $response = $r -> getBody() -> getContents();
        $response = json_decode($response, true);
        dump($response);

        // $next = $response['links'][0]['href'];
        // if($next) {
        //     $r = $client -> request('GET', $next);
        //     $response = $r -> getBody() -> getContents();
        //     $response = json_decode($response, true);
        //     dump($response);
        // }


    }

    public function skyslope_auth() {

        $timestamp = str_replace(' ', 'T', gmdate('Y-m-d H:i:s')).'Z';

        $key = config('skyslope.key');
        $client_id = config('skyslope.client_id');
        $client_secret = config('skyslope.client_secret');
        $secret = config('skyslope.secret');

        $str = $client_id.':'.$client_secret.':'.$timestamp;

        $hmac = base64_encode(hash_hmac('sha256', $str, $secret, true));

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'SS '.$key.':'.$hmac,
            'Timestamp' => $timestamp
        ];

        $json = [
            'clientID' => $client_id,
            'clientSecret' => $client_secret
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'json' => $json
        ]);

        $r = $client -> request('POST', 'https://api.skyslope.com/auth/login');
        $response = $r -> getBody() -> getContents();

        return json_decode($response, true);

    }


    public function test(Request $request) {



        //return view('tests/test');

        $notification = config('notifications.agent_notification_emails_missing_transactions');

        //if($notification['on_off'] == 'on') {

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
                'ListOfficeMlsId',
                'MLSListDate',
                'MlsStatus',
                'PostalCode',
                'StateOrProvince'
            ];

            $contracts_select = [
                'Agent_ID',
                'BuyerAgentEmail',
                'BuyerAgentFirstName',
                'BuyerAgentLastName',
                'BuyerAgentPreferredPhone',
                'BuyerOfficeMlsId',
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

            $listing_ListingIds = Listings::select('ListingId', 'Agent_ID')
            -> whereNotNull('ListingId')
            -> where('ListingId', '!=', '')
            -> where('MLSListDate', '>', date('Y-m-d', strtotime('-3 month')))
            -> get();

            $contract_ListingIds = Contracts::select('ListingId', 'Agent_ID')
            -> whereNotNull('ListingId')
            -> where('ListingId', '!=', '')
            -> where('ContractDate', '>', date('Y-m-d', strtotime('-6 month')))
            -> get();


            $agents = Agents::select('id', 'email')
            -> where('active', 'yes')
            -> where('id', '3167')
            -> with(['company_listings:'.implode(',', $listings_select)])
            -> limit(10)
            -> get();


            foreach($agents as $agent) {
                dd($agent);
                $Agent_ID = $agent -> id;

                $agent_listing_ListingIds = $listing_ListingIds
                -> where('Agent_ID', $Agent_ID)
                -> pluck('ListingId');

                $agent_contract_ListingIds = $contract_ListingIds
                -> where('Agent_ID', $Agent_ID)
                -> pluck('ListingId');

                $missing_agent_listings = $agent -> company_listings
                -> whereIn('ListOfficeMlsId', config('bright_office_codes'))
                -> whereNotIn('ListingId', $agent_listing_ListingIds)
                -> where('MLSListDate', '>', date('Y-m-d', strtotime('-18 month')));

                $missing_agent_contracts = $agent -> company_listings;
                //-> whereIn('BuyerOfficeMlsId', config('bright_office_codes'))
                //-> whereNotIn('ListingId', $agent_contract_ListingIds)
                //-> where('PurchaseContractDate', '>', date('Y-m-d', strtotime('-18 month')));
                dd($missing_agent_contracts);

                $subject = '';
                $message = '';
                $message_email = '
                <div style="font-size: 15px; width:100%;" width="100%">

                    <br><br>
                    Thank You,<br>
                    Taylor Properties
                </div>';


                // $notification['type'] = 'commission_ready';
                // $notification['sub_type'] = 'contract';
                // $notification['sub_type_id'] = $ListingId;
                // $notification['subject'] = $subject;
                // $notification['message'] = $message;
                // $notification['message_email'] = $message_email;

                //Notification::send($user, new GlobalNotification($notification));

            }

        //}

    }

    private function get_missing_listings($Agent_ID) {

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
        -> where('Agent_ID', $Agent_ID)
        -> whereNotNull('ListingId')
        -> where('ListingId', '!=', '')
        -> where('MLSListDate', '>', date('Y-m-d', strtotime('-3 month')))
        -> pluck('ListingId');

        $properties = CompanyListings::select($listings_select)
        -> where('Agent_ID', $Agent_ID)
        -> whereIn('ListOfficeMlsId', config('bright_office_codes'))
        -> whereNotIn('ListingId', $ListingIds)
        -> where('MLSListDate', '>', date('Y-m-d', strtotime('-3 month')))
        -> with(['agent:id,email'])
        -> get();
        $properties = $properties -> append('transaction_type');

        return $properties;

    }

    private function get_missing_contracts($Agent_ID) {

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
        -> where('Agent_ID', $Agent_ID)
        -> whereNotNull('ListingId')
        -> where('ListingId', '!=', '')
        -> where('ContractDate', '>', date('Y-m-d', strtotime('-6 month')))
        -> pluck('ListingId');

        $properties = CompanyListings::select($contracts_select)
        -> where('Agent_ID', $Agent_ID)
        -> whereIn('BuyerOfficeMlsId', config('bright_office_codes'))
        -> whereNotIn('ListingId', $ListingIds)
        -> where('PurchaseContractDate', '>', date('Y-m-d', strtotime('-6 month')))
        -> with(['agent:id,email'])
        -> get();
        $properties = $properties -> append('transaction_type');

        return $properties;

    }

    private function get_missing_contracts_our_listing($Agent_ID) {

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
        -> where('Agent_ID', $Agent_ID)
        -> whereNotNull('ListingId')
        -> where('ListingId', '!=', '')
        -> where('ContractDate', '>', date('Y-m-d', strtotime('-6 month')))
        -> pluck('ListingId');

        $properties = CompanyListings::select($listings_select)
        -> where('Agent_ID', $Agent_ID)
        -> whereIn('ListOfficeMlsId', config('bright_office_codes'))
        -> whereNotIn('ListingId', $ListingIds)
        -> whereIn('MlsStatus', ['PENDING', 'ACTIVE UNDER CONTRACT'])
        -> with(['agent:id,email'])
        -> get();
        $properties = $properties -> append('transaction_type');

        return $properties;

    }

    public function add_missing_listings() {

        $rets_config = new \PHRETS\Configuration;
        $rets_config -> setLoginUrl(config('rets.rets.url'))
            -> setUsername(config('rets.rets.username'))
            -> setPassword(config('rets.rets.password'))
            -> setRetsVersion('RETS/1.8')
            -> setUserAgent('Bright RETS Application/1.0')
            -> setHttpAuthenticationMethod('digest')
            -> setOption('disable_follow_location', false)
            -> setOption('use_post_method', false);

        $rets = new \PHRETS\Session($rets_config);

        $connect = $rets -> Login();

        $existing = CompanyListings::select('ListingKey') -> get() -> pluck('ListingKey') -> toArray();

        $resource = 'Property';
        $class = 'ALL';

        $start = date("Y-m-d", strtotime("-6000 days"));
        $end = date("Y-m-d", strtotime("-5000 days"));
        echo $start.' = '.$end.'<br>';

        $office_codes = [];
        foreach(config('bright_office_codes') as $code) {
            $office_codes[] = '(ListOfficeMlsId='.$code.')|(BuyerOfficeMlsId='.$code.')';
        }
        $office_codes = implode('|', $office_codes);

        $query = '(MLSListDate='.$start.'-'.$end.'),('.$office_codes.')';

        $results = $rets -> Search(
            $resource,
            $class,
            $query
        );

        $listings = $results -> toArray();

        $rets -> Disconnect();

        foreach($listings as $listing) {

            $listing_key = $listing['ListingKey'];
            echo $listing_key.' ';
            if(!in_array($listing_key, $existing)) {

                echo 'adding '.$listing_key.'<br>';
                $add_listing = new CompanyListings();
                foreach($listing as $col => $val) {
                    $add_listing -> $col = $val;
                }
                if(in_array($listing['BuyerOfficeMlsId'], config('bright_office_codes'))) {
                    $mls_id = $listing['BuyerAgentMlsId'];
                } else {
                    $mls_id = $listing['ListAgentMlsId'];
                }
                dump($mls_id.' Agent_ID = '.$this -> agent_id($mls_id));
                $add_listing -> Agent_ID = $this -> agent_id($mls_id);
                $add_listing -> save();

            }

        }

    }

    public function test_find_missing() {

        $rets_config = new \PHRETS\Configuration;
        $rets_config -> setLoginUrl(config('rets.rets.url'))
            -> setUsername(config('rets.rets.username'))
            -> setPassword(config('rets.rets.password'))
            -> setRetsVersion('RETS/1.8')
            -> setUserAgent('Bright RETS Application/1.0')
            -> setHttpAuthenticationMethod('digest')
            -> setOption('disable_follow_location', false)
            -> setOption('use_post_method', false);

        $rets = new \PHRETS\Session($rets_config);

        $connect = $rets -> Login();

        $resource = 'Property';
        $class = 'ALL';

        // get company listings count
        // not closed or withdrawn
        $company_listings_keys = CompanyListings::where('MlsListDate', '>=', '2018-01-01') -> get() -> pluck('ListingKey') -> toArray();
        $company_listings_count = count($company_listings_keys);

        // get bright listings count
        $bright_office_codes = implode(',', config('bright_office_codes'));

        // not closed
        $query = '(MLSListDate=2018-01-01+),((ListOfficeMlsId='.$bright_office_codes.')|(BuyerOfficeMlsId='.$bright_office_codes.'))';


        $results = $rets -> Search(
            $resource,
            $class,
            $query,
            [
                'Select' => 'ListingKey'
            ]
        );

        $bright_listings = $results -> toArray();
        $bright_listings_count = $results -> count();

        $bright_listing_keys = [];
        foreach($bright_listings as $bright_listing) {
            $bright_listing_keys[] = $bright_listing['ListingKey'];
        }

        dd($company_listings_count, $bright_listings_count);
        if($company_listings_count != $bright_listings_count) {

            // get missing listing keys
            $missing_company = array_diff($bright_listing_keys, $company_listings_keys);
            arsort($missing_company);
            $withdrawn = array_diff($company_listings_keys, $bright_listing_keys);

            if(count($missing_company) > 0) {

                $missing = array_slice($missing_company, 0, 1000);
                $query = '(ListingKey='.implode(',', $missing).')';

                $resource = 'Property';
                $class = 'ALL';

                $results = $rets -> Search(
                    $resource,
                    $class,
                    $query
                );

                $listings = $results -> toArray();

                foreach($listings as $listing) {

                    $listing_key = $listing['ListingKey'];

                    if($listing['ListingId'] != '') {

                        $add_listing = CompanyListings::firstOrCreate([
                            'ListingKey' => $listing_key
                        ]);

                        foreach($listing as $col => $val) {
                            $add_listing -> $col = $val;
                        }

                        $add_listing -> save();

                    }

                }

            }

            if(count($withdrawn) > 0) {

                $update_listings = CompanyListings::whereIn('ListingKey', $withdrawn)
                    -> update([
                        'MlsStatus' => 'Withdrawn',
                        'CloseDate' => date('Y-m-d')
                    ]);

            }

            $rets -> Disconnect();

        }

    }

    public function add_missing_agent_ids() {

        $rets_config = new \PHRETS\Configuration;
        $rets_config -> setLoginUrl(config('rets.rets.url'))
            -> setUsername(config('rets.rets.username'))
            -> setPassword(config('rets.rets.password'))
            -> setRetsVersion('RETS/1.8')
            -> setUserAgent('Bright RETS Application/1.0')
            -> setHttpAuthenticationMethod('digest')
            -> setOption('disable_follow_location', false)
            -> setOption('use_post_method', false);

        $rets = new \PHRETS\Session($rets_config);

        $connect = $rets -> Login();

        $listings = CompanyListings::select('ListingKey')
        -> whereNull('Agent_ID')
        -> orWhere('Agent_ID', '0')
        -> limit(200)
        -> pluck('ListingKey')
        -> toArray();

        $resource = 'Property';
        $class = 'ALL';

        $query = '(ListingKey='.implode(',', $listings).')';

        $results = $rets -> Search(
            $resource,
            $class,
            $query,
            [
                'Limit' => 200,
                'Select' => 'ListingKey,BuyerOfficeMlsId,BuyerAgentMlsId,ListOfficeMlsId,ListAgentMlsId'
            ]
        );

        $listings = $results -> toArray();

        $rets -> Disconnect();

        foreach($listings as $listing) {

            $listing_key = $listing['ListingKey'];
            echo $listing_key.' ';

            if(in_array($listing['BuyerOfficeMlsId'], config('bright_office_codes'))) {
                $mls_id = $listing['BuyerAgentMlsId'];
            } else {
                $mls_id = $listing['ListAgentMlsId'];
            }

            $Agent_ID = $this -> agent_id($mls_id);
            echo $Agent_ID.'<br>';
            $listing = CompanyListings::find($listing_key)
            -> update([
                'Agent_ID' => $Agent_ID
            ]);

        }

    }

    public function agent_id($agent_mls_id) {

        $agent_id = Agents::where('bright_mls_id_md_dc_tp', $agent_mls_id)
        -> orWhere('bright_mls_id_va_tp', $agent_mls_id)
        -> orWhere('bright_mls_id_md_aap', $agent_mls_id)
        -> pluck('id');

        $id = '0000000';
        if(count($agent_id) > 0) {
            $id = $agent_id[0];
        }

        return $id;

    }

}
