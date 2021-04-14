<?php

namespace App\Console\Commands\BrightMLS;

use Illuminate\Console\Command;
use App\Jobs\BrightMLS\UpdateListingsJob;

class UpdateListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:update_listings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Company Listings';

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
        UpdateListingsJob::dispatch();
    }
}
