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
        \App\Console\Commands\DocManagement\ExpireListings::class,
        \App\Console\Commands\DatabaseBackUp::class,
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

        // clear temp files
        $schedule -> exec('sudo find '.base_path().'/storage/app/public/doc_management/transactions/contracts/*/emailed_docs/* -mtime +2 -exec rm -rf {} \\') -> daily();
        $schedule -> exec('sudo find '.base_path().'/storage/app/public/tmp* -maxdepth 1 -type f -mtime +1 -exec rm -rf {} \\') -> daily();
        $schedule -> exec('sudo find /var/www/tmp* -mtime +1 -exec rm -rf {} \\') -> daily();

        // set listings to expired
        $schedule -> command('doc_management:expire_listings') -> dailyAt('00:01');

        // mysql backup locally
        $schedule -> command('database:backup') -> daily();
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
