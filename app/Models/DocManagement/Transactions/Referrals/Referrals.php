<?php

namespace App\Models\DocManagement\Transactions\Referrals;

use Illuminate\Database\Eloquent\Model;

class Referrals extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_referrals';
    protected $primaryKey = 'Referral_ID';
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if (auth() -> user()) {
                if (stristr(auth() -> user() -> group, 'agent')) {
                    $query -> where('Agent_ID', auth() -> user() -> user_id);
                } elseif (stristr(auth() -> user() -> group, 'transaction_coordinator')) {
                    $query -> where('TransactionCoordinator_ID', auth() -> user() -> user_id);
                }
                $query -> where('Status', '>', '0');
            } else {
                abort(404);
            }
        });
    }

    public function agent() {
        return $this -> hasOne(\App\Models\Employees\Agents::class, 'id', 'Agent_ID');
    }

    public function transaction_coordinator() {
        return $this -> hasOne(\App\Models\Employees\TransactionCoordinators::class, 'id', 'TransactionCoordinator_ID');
    }

    public function status() {
        return $this -> hasOne(\App\Models\DocManagement\Resources\ResourceItems::class, 'resource_id', 'Status');
    }

    public function checklist() {
        return $this -> hasOne(\App\Models\DocManagement\Transactions\Checklists\TransactionChecklists::class, 'Referral_ID', 'Referral_ID');
    }
}
