<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

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
    public function handle() {


        $tables = ['admin_resource_items', 'config', 'emp_in_house', 'emp_transaction_coordinators', 'docs_resource_items'];

        foreach($tables as $table) {

            $filename = "backup-" . Carbon::now() -> format('Y-m-d') . "_".$table.".sql";

            $command = "mysqldump --user=" . env('DB_USERNAME') . " --password='" . env('DB_PASSWORD') . "' --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . " ".$table."  > " . storage_path() . "/app/backup/" . $filename;


            $returnVar = NULL;
            $output = NULL;


            exec($command, $output, $returnVar);

        }

    }

}
