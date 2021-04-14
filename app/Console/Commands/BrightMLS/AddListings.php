<?php

namespace App\Console\Commands\BrightMLS;

use Illuminate\Console\Command;
use App\Jobs\BrightMLS\AddListingsJob;

class AddListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:add_listings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Company Listings';

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
        AddListingsJob::dispatch();
    }
}
