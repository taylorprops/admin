<?php

namespace App\Console\Commands\Calendar;

use Illuminate\Console\Command;
use App\Jobs\Calendar\TasksAllDayJob;

class TasksAllDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:tasks_all_day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'All Day Tasks Notification';

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
        TasksAllDayJob::dispatch();
    }
}
