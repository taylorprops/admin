<?php

namespace App\Jobs\Agents\DocManagement\Transactions\EditFiles;

use App\Models\DocManagement\Transactions\EditFiles\UserFieldsInputs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TheRezor\TransactionalJobs\Contracts\RunAfterTransaction;

class SaveEditSystemInputs implements ShouldQueue, RunAfterTransaction
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    protected $inputs;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (count($this->inputs) > 0) {
            foreach ($this->inputs as $input) {
                $updated_input = UserFieldsInputs::find($input['id']);

                if ($updated_input) {
                    $updated_input->update(['input_value' => $input['value']]);

                    //update all common fields on other docs
                    if ($updated_input->input_db_column != '') {
                        // update all with same transaction_type, Listing_ID, Contract_ID, Referral_ID and input_db_column
                        $common_inputs = UserFieldsInputs::where([
                            'transaction_type' => $updated_input->transaction_type,
                            'Listing_ID' => $updated_input->Listing_ID ?? 0,
                            'Contract_ID' => $updated_input->Contract_ID ?? 0,
                            'Referral_ID' => $updated_input->Referral_ID ?? 0,
                            'input_db_column' => $updated_input->input_db_column,
                        ])
                        ->update([
                            'input_value' => $updated_input->input_value,
                        ]);
                    }
                }
            }
        }
    }
}
