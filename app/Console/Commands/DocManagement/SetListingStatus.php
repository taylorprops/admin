<?php

namespace App\Console\Commands\DocManagement;

use Illuminate\Console\Command;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Listings\Listings;

class SetListingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doc_management:set_listing_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set listing status to Active or Expired';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this -> set_listing_status();
    }

    /* ---------- Update Listing Statuses ---------- */
    public function set_listing_status()
    {
        $status_id_pre_listing = ResourceItems::GetResourceID('Pre-Listing', 'listing_status');
        $status_id_active = ResourceItems::GetResourceID('Active', 'listing_status');
        $status_id_expired = ResourceItems::GetResourceID('Expired', 'listing_status');

        // set pre-listing to active on their list date
        $pre_listings = Listings::where('Status', $status_id_pre_listing) -> get();

        foreach ($pre_listings as $pre_listing) {
            if ($pre_listing -> MLSListDate == date('Y-m-d')) {
                $pre_listing -> Status = $status_id_active;
                $pre_listing -> save();
            }
        }

        // set active to expired on their expiration date
        $active_listings = Listings::where('Status', $status_id_active) -> get();

        foreach ($active_listings as $active_listing) {
            if ($active_listing -> ExpirationDate < date('Y-m-d')) {
                $active_listing -> Status = $status_id_expired;
                $active_listing -> save();
            }
        }
    }

}
