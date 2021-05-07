<?php

namespace App\Console;

use Illuminate\Support\Facades\Storage;
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
        if(config('app.env') == 'local') {
            $schedule -> command('bright_mls:add_listings') -> cron('15 * * * *');
            $schedule -> command('bright_mls:update_listings') -> cron('5,35 * * * * ');
            $schedule -> command('bright_mls:find_withdraw_listings') -> cron('55 * * * *');
        }

        // set listings  status
        $schedule -> command('doc_management:set_listing_status') -> dailyAt('00:01');

        // calendar and tasks
        $schedule -> command('calendar:tasks_all_day') -> dailyAt('08:00:00');
        $schedule -> command('calendar:tasks_reminders') -> everyMinute();
        $schedule -> command('calendar:events_all_day') -> dailyAt('08:00:00');
        $schedule -> command('calendar:events_timed') -> everyMinute();

        // update agents
        if(config('app.env') == 'production') {
            //$schedule -> command('old_db:update_agents') -> everyMinute() -> withoutOverlapping(1);
            //$schedule -> command('old_db:update_agents_other_tables') -> everyMinute() -> withoutOverlapping(1);
        }

        // clear temp files
        $schedule -> exec('sudo find '.Storage::path('').'/doc_management/transactions/contracts/*/emailed_docs/* -mtime +2 -exec rm -rf {} \\') -> daily();
        $schedule -> exec('sudo find '.Storage::path('').'/tmp* -maxdepth 1 -type f -mtime +1 -exec rm -rf {} \\') -> daily();
        $schedule -> exec('sudo find /var/www/tmp* -mtime +1 -exec rm -rf {} \\') -> daily();

        if(config('app.env') == 'local') {
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
