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
    public function test(Request $request) {

        $timestamp = str_replace(' ', 'T', gmdate('Y-m-d H:i:s')).'Z';

        $key = config('skyslope.key');
        $client_id = config('skyslope.client_id');
        $client_secret = config('skyslope.client_secret');
        $secret = config('skyslope.secret');

        $str = $client_id.':'.$client_secret.':'.$timestamp;

        $hmac = base64_encode(hash_hmac('sha256', $str, $secret, true));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.skyslope.com/auth/login",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n\t\"clientID\": \"".$client_id."\",\n\t\"clientSecret\": \"".$client_secret."\"}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: SS ".$key.":".$hmac,
                "Timestamp: ".$timestamp,
                "Content-Type: application/json"
            ),
        ));



        $response = curl_exec($curl);

        curl_close($curl);

        dd($response);

        //return view('tests/test');

        $notification = config('notifications.agent_notification_emails_missing_transactions');

        //if($notification['on_off'] == 'on') {

            $listings = $this -> get_missing_listings();
            $contracts = $this -> get_missing_contracts();
            $contracts_our_listings = $this -> get_missing_contracts_our_listing();




            $subject = '';
            $message = '';
            $message_email = '
            <div style="font-size: 15px; width:100%;" width="100%">

                <br><br>
                Thank You,<br>
                Taylor Properties
            </div>';


            $notification['type'] = 'commission_ready';
            $notification['sub_type'] = 'contract';
            $notification['sub_type_id'] = $Contract_ID;
            $notification['subject'] = $subject;
            $notification['message'] = $message;
            $notification['message_email'] = $message_email;

            //Notification::send($user, new GlobalNotification($notification));

        //}

    }

    private function get_missing_listings() {

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
        -> with(['agent:id,email'])
        -> get();
        $properties = $properties -> append('transaction_type');

        return $properties;

    }

    private function get_missing_contracts() {

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
        -> with(['agent:id,email'])
        -> get();
        $properties = $properties -> append('transaction_type');

        return $properties;

    }

    private function get_missing_contracts_our_listing() {

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
        -> with(['agent:id,email'])
        -> get();
        $properties = $properties -> append('transaction_type');

        return $properties;

    }



}
