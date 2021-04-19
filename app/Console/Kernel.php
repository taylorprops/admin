<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CheckEmailedDocuments::class,
        \App\Console\Commands\DocManagement\SetListingStatus::class,
        \App\Console\Commands\DatabaseBackUp::class,
        \App\Console\Commands\BrightMLS\AddListings::class,
        \App\Console\Commands\BrightMLS\UpdateListings::class,
        \App\Console\Commands\BrightMLS\FindWithdrawListings::class,
        \App\Console\Commands\OldDB\Agents\AddAgentsTablesCommand::class,
        \App\Console\Commands\OldDB\Agents\UpdateAgentsTablesCommand::class,
        \App\Console\Commands\OldDB\Agents\UpdateAgentsTablesOtherCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // get docs emailed for transactions
        $schedule -> command('doc_management:check_emailed_documents') -> everyMinute() -> withoutOverlapping(1);

        // add, update, withdraw listings
        $schedule -> command('bright_mls:add_listings') -> everyFifteenMinutes();
        $schedule -> command('bright_mls:update_listings') -> everyThirtyMinutes();
        $schedule -> command('bright_mls:find_withdraw_listings') -> everyThirtyMinutes();

        // set listings  status
        $schedule -> command('doc_management:set_listing_status') -> dailyAt('00:01');

        // update agents
        if(config('app.env') != 'development') {
            //$schedule -> command('old_db:update_agents') -> everyMinute() -> withoutOverlapping(1);
            //$schedule -> command('old_db:add_agents_other_tables') -> everyMinute() -> withoutOverlapping(1);
        }

        // clear temp files
        $schedule -> exec('sudo find '.base_path().'/storage/app/public/doc_management/transactions/contracts/*/emailed_docs/* -mtime +2 -exec rm -rf {} \\') -> daily();
        $schedule -> exec('sudo find '.base_path().'/storage/app/public/tmp* -maxdepth 1 -type f -mtime +1 -exec rm -rf {} \\') -> daily();
        $schedule -> exec('sudo find /var/www/tmp* -mtime +1 -exec rm -rf {} \\') -> daily();

        if(config('global.app_stage') == 'development') {
            // mysql backup locally
            //$schedule -> command('database:backup') -> dailyAt('08:25');
        }


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this -> load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
