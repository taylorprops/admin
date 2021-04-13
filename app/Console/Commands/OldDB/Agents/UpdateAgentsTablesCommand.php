<?php

namespace App\Console\Commands\OldDB\Agents;

use Illuminate\Console\Command;
use App\Jobs\OldDB\Agents\UpdateAgentsTablesJob;

class UpdateAgentsTablesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old_db:update_agents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Agents From Old DB';

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
        UpdateAgentsTablesJob::dispatch();

    }
}
