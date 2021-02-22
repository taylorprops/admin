<?php

namespace App\Models\DocManagement\Transactions\Listings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Referrals\Referrals;

class Listings extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    public $table = 'docs_transactions_listings';
    protected $primaryKey = 'Listing_ID';
    public $timestamps = false;
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if(auth() -> user()) {
                if(stristr(auth() -> user() -> group, 'agent')) {
                    $query -> where('Agent_ID', auth() -> user() -> user_id);
                } else if(auth() -> user() -> group == 'transaction_coordinator') {
                    $query -> where('TransactionCoordinator_ID', auth() -> user() -> user_id);
                }
            }
        });
    }

    public function ScopeGetPropertyDetails($request, $transaction_type, $id, $select = null) {

        if(is_array($id)) {
            if($transaction_type == 'listing') {
                $id = $id[0];
            } else if($transaction_type == 'contract') {
                $id = $id[1];
            } else if($transaction_type == 'referral') {
                $id = $id[2];
            }
        }

        if($transaction_type == 'listing') {
            $property = Listings::find($id);
        } else if($transaction_type == 'contract') {
            $property = Contracts::find($id);
        } else if($transaction_type == 'referral') {
            $property = Referrals::find($id);
        }
        if($select) {
            $property = $property -> select($select);
        }

        return $property;
    }


}
