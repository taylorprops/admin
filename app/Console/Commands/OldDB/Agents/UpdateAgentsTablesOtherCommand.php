<?php

namespace App\Console\Commands\OldDB\Agents;

use Illuminate\Console\Command;
use App\Jobs\OldDB\Agents\UpdateAgentsTablesOtherJob;

class UpdateAgentsTablesOtherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old_db:add_agents_other_tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Agents Related Tables From Old DB';

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
        UpdateAgentsTablesOtherJob::dispatch();
    }
}
