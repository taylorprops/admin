<?php

namespace App\Models\DocManagement\Transactions\Contracts;

use App\Models\DocManagement\Transactions\Listings\Listings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Schema;

class Contracts extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    protected $table = 'docs_transactions_contracts';
    protected $primaryKey = 'Contract_ID';
    //public $timestamps = false;
    protected $guarded = [];
    protected $appends = ['transaction_type'];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if (auth() -> user()) {
                if (stristr(auth() -> user() -> group, 'agent')) {
                    $query -> where('Agent_ID', auth() -> user() -> user_id)
                        -> orWhere('CoAgent_ID', auth() -> user() -> user_id);
                } elseif (stristr(auth() -> user() -> group, 'transaction_coordinator')) {
                    $query -> where('TransactionCoordinator_ID', auth() -> user() -> user_id);
                }
            }
            $query -> where('Status', '>', '0');
        });
    }

    public function getTransactionTypeAttribute() {
        return 'contract';
    }

    public function agent() {
        return $this -> hasOne(\App\Models\Employees\Agents::class, 'id', 'Agent_ID');
    }

    public function co_agent() {
        return $this -> hasOne(\App\Models\Employees\Agents::class, 'id', 'CoAgent_ID');
    }

    public function team() {
        return $this -> hasOne(\App\Models\Employees\AgentsTeams::class, 'id', 'Team_ID');
    }

    public function transaction_coordinator() {
        return $this -> hasOne(\App\Models\Employees\TransactionCoordinators::class, 'id', 'TransactionCoordinator_ID');
    }

    public function members() {
        return $this -> hasMany(\App\Models\DocManagement\Transactions\Members\Members::class, 'Contract_ID', 'Contract_ID');
    }


    public function commission() {
        return $this -> hasOne(\App\Models\Commission\Commission::class, 'Contract_ID');
    }

    public function commission_breakdown() {
        return $this -> hasOne(\App\Models\Commission\CommissionBreakdowns::class, 'Contract_ID');
    }

    public function earnest() {
        return $this -> hasOne(\App\Models\DocManagement\Earnest\Earnest::class, 'Contract_ID', 'Contract_ID');
    }

    public function listing() {
        return $this -> hasOne(\App\Models\DocManagement\Transactions\Listings\Listings::class, 'Listing_ID', 'Listing_ID');
    }

    public function status() {
        return $this -> hasOne(\App\Models\DocManagement\Resources\ResourceItems::class, 'resource_id', 'Status');
    }

    public function checklist() {
        return $this -> hasOne(\App\Models\DocManagement\Transactions\Checklists\TransactionChecklists::class, 'Contract_ID', 'Contract_ID');
    }

    public function ScopeContractColumnsNotInListings() {
        $listing_columns = Schema::getColumnListing('docs_transactions_listings');
        $contract_columns = Schema::getColumnListing('docs_transactions_contracts');

        return array_diff($contract_columns, $listing_columns);
    }
}
