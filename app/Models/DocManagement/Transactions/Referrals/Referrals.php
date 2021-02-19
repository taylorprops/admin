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
            }
        });
    }

}
