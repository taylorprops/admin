<?php

namespace App\Console\Commands\DocManagement;

use Illuminate\Console\Command;

use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Resources\ResourceItems;

class ExpireListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doc_management:expire_listings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set listings to expired';

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
        $this -> set_listings_expired();
    }

    /* ---------- Update Listing Statuses ---------- */
    public function set_listings_expired() {

        $status_id_active = ResourceItems::GetResourceID('Active', 'listing_status');
        $status_id_expired = ResourceItems::GetResourceID('Expired', 'listing_status');

        $listings = Listings::where('Status', $status_id_active) -> get();

        foreach($listings as $listing) {

            if($listing -> ExpirationDate < date('Y-m-d')) {
                $listing -> Status = $status_id_expired;
                $listing -> save();
            }

        }

    }

}
