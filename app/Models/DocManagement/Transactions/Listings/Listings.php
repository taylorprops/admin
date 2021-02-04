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
                if(auth() -> user() -> group == 'agent') {
                    $query -> where('Agent_ID', auth() -> user() -> user_id);
                }
            }
        });
    }

    public function ScopeGetPropertyDetails($request, $transaction_type, $id) {

        if(is_array($id)) {
            $id = max($id);
        }

        if($transaction_type == 'listing') {
            $property = Listings::find($id);
        } else if($transaction_type == 'contract') {
            $property = Contracts::find($id);
        } else if($transaction_type == 'referral') {
            $property = Referrals::find($id);
        }
        return $property;
    }


}
