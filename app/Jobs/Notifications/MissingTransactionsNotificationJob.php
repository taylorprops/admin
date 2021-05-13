<?php

namespace App\Jobs\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Client\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\BrightMLS\CompanyListings;
use App\Notifications\GlobalNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;

class MissingTransactionsNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    private function get_missing_listings($agent_id) {

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
            'StateOrProvince',
            'type'
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


        return compact('properties');

    }

    private function get_missing_contracts($agent_id) {

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
            'StateOrProvince',
            'type'
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

        return compact('properties');

    }

    private function get_missing_contracts_our_listing($agent_id) {

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
            'StateOrProvince',
            'type'
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

        return compact('properties');

    }


    public function handle()
    {
        //notify agent
        $notification = config('notifications.agent_notification_emails_missing_transactions');

        if($notification['on_off'] == 'on') {

            $listings = $this -> get_missing_listings();
            $contracts = $this -> get_missing_contracts();
            $contracts_our_listings = $this -> get_missing_contracts_our_listings();
            dd($listings);


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

            Notification::send($user, new GlobalNotification($notification));

        }
    }
}
