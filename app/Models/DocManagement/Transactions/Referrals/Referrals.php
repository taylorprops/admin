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
            if(stristr(auth() -> user() -> group, 'agent')) {
                $query -> where('Agent_ID', auth() -> user() -> user_id);
            } else if(stristr(auth() -> user() -> group, 'transaction_coordinator')) {
                $query -> where('TransactionCoordinator_ID', auth() -> user() -> user_id);
            }
        });
    }

    public function agent() {
        return $this -> hasOne('App\Models\Employees\Agents', 'id', 'Agent_ID');
    }

    public function transaction_coordinator() {
        return $this -> hasOne('App\Models\DocManagement\Transactions\Members\TransactionCoordinators', 'id', 'TransactionCoordinator_ID');
    }

    public function status() {
        return $this -> hasOne('App\Models\DocManagement\Resources\ResourceItems', 'resource_id', 'Status');
    }

    public function checklist() {
        return $this -> hasOne('App\Models\DocManagement\Transactions\Checklists\TransactionChecklists', 'Referral_ID', 'Referral_ID');
    }

}
