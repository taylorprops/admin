<?php

namespace App\Console\Commands\Calendar;

use Illuminate\Console\Command;
use App\Jobs\Calendar\CalendarEventsJob;

class CalendarEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:events_timed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calendar Event with Time';

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
        CalendarEventsJob::dispatch();
    }
}
