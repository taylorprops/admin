<?php

namespace App\Jobs\OldDB\Earnest;

use Illuminate\Bus\Queueable;
use App\Models\OldDB\EscrowExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\DocManagement\Earnest\Earnest;
use App\Models\DocManagement\Resources\ResourceItems;

class EscrowExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        $accounts = ResourceItems::where('resource_type', 'earnest_accounts') -> get();

        foreach($accounts as $account) {

            $account_id = $account -> resource_id;
            $company = stristr($account -> resource_name, 'taylor') ? 'tp' : 'aap';
            $state = strtolower($account -> resource_state);

            $new_earnest = Earnest::select(DB::raw('SUM(amount_total) as amount_totals')) -> where('earnest_account_id', $account_id) -> get();

            // update old db
            $total = $new_earnest[0]['amount_totals'] ? $new_earnest[0]['amount_totals'] : 0;
            $old_earnest = EscrowExport::where('id', 1) -> update([$company.'_'.$state => $total]);

        }

    }
}
