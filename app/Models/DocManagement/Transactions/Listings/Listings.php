<?php

namespace App\Models\DocManagement\Transactions\Listings;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Referrals\Referrals;

class Listings extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    protected $table = 'docs_transactions_listings';
    protected $primaryKey = 'Listing_ID';
    //public $timestamps = false;
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if (auth() -> user()) {
                if (stristr(auth() -> user() -> group, 'agent')) {
                    $query -> where(function ($query) {
                        $query -> where('Agent_ID', auth() -> user() -> user_id)
                        -> orWhere('CoAgent_ID', auth() -> user() -> user_id);
                    });
                } elseif (auth() -> user() -> group == 'transaction_coordinator') {
                    $query -> where('TransactionCoordinator_ID', auth() -> user() -> user_id);
                }
                $query -> where('Status', '>', '0');
            }

        });
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

    public function status() {
        return $this -> hasOne(\App\Models\DocManagement\Resources\ResourceItems::class, 'resource_id', 'Status');
    }

    public function contract() {
        return $this -> hasOne(\App\Models\DocManagement\Transactions\Contracts\Contracts::class, 'Contract_ID', 'Contract_ID');
    }

    public function checklist() {
        return $this -> hasOne(\App\Models\DocManagement\Transactions\Checklists\TransactionChecklists::class, 'Listing_ID', 'Listing_ID');
    }

    public function members() {
        return $this -> hasMany(\App\Models\DocManagement\Transactions\Members\Members::class, 'Listing_ID', 'Listing_ID');
    }


    public function ScopeGetPropertyDetails($request, $transaction_type, $id, $select = null) {

        if($select) {
            array_push($select, 'Listing_ID', 'Contract_ID', 'Referral_ID', 'Agent_ID', 'TransactionCoordinator_ID', 'Status');
        }

        if ($transaction_type == 'listing') {
            if (is_array($id)) {
                $id = $id[0];
            }

        } elseif ($transaction_type == 'contract') {
            if (is_array($id)) {
                $id = $id[1];
            }

        } elseif ($transaction_type == 'referral') {
            if (is_array($id)) {
                $id = $id[2];
            }

        }


        if ($transaction_type == 'listing') {

            if($select) {
                $property = self::select($select) -> where('Listing_ID', $id);
            } else {
                $property = self::where('Listing_ID', $id);
            }
            $property = $property -> with(['agent', 'co_agent', 'team', 'transaction_coordinator', 'checklist', 'status', 'members']) -> first();

        } elseif ($transaction_type == 'contract') {

            if($select) {
                $property = Contracts::select($select) -> where('Contract_ID', $id);
            } else {
                $property = Contracts::where('Contract_ID', $id);
            }
            $property = $property -> with(['agent', 'team', 'transaction_coordinator', 'checklist', 'status', 'members']) -> first();

        } elseif ($transaction_type == 'referral') {

            if($select) {
                $property = Referrals::select($select) -> where('Referral_ID', $id);
            } else {
                $property = Referrals::where('Referral_ID', $id);
            }
            $property = $property -> with(['agent', 'transaction_coordinator', 'checklist', 'status']) -> first();

        }
        if(!$property) {
            return 'not found';
        }
        return $property;
    }
}
