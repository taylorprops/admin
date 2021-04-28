<?php

namespace App\Console\Commands\Calendar;

use Illuminate\Console\Command;
use App\Jobs\Calendar\TasksRemindersJob;

class TasksReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:tasks_reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        TasksRemindersJob::dispatch();
    }
}
