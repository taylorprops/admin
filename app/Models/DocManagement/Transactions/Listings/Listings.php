<?php

namespace App\Models\DocManagement\Transactions\Listings;

use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listings extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    public $table = 'docs_transactions_listings';
    protected $primaryKey = 'Listing_ID';
    //public $timestamps = false;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if (auth()->user()) {
                if (stristr(auth()->user()->group, 'agent')) {
                    $query->where(function ($query) {
                        $query->where('Agent_ID', auth()->user()->user_id)
                        ->orWhere('CoAgent_ID', auth()->user()->user_id);
                    });
                } elseif (auth()->user()->group == 'transaction_coordinator') {
                    $query->where('TransactionCoordinator_ID', auth()->user()->user_id);
                }
            }
        });
    }

    public function agent()
    {
        return $this->hasOne('App\Models\Employees\Agents', 'id', 'Agent_ID');
    }

    public function co_agent()
    {
        return $this->hasOne('App\Models\Employees\Agents', 'id', 'CoAgent_ID');
    }

    public function team()
    {
        return $this->hasOne('App\Models\Employees\AgentsTeams', 'id', 'Team_ID');
    }

    public function transaction_coordinator()
    {
        return $this->hasOne('App\Models\DocManagement\Transactions\Members\TransactionCoordinators', 'id', 'TransactionCoordinator_ID');
    }

    public function status()
    {
        return $this->hasOne('App\Models\DocManagement\Resources\ResourceItems', 'resource_id', 'Status');
    }

    public function contract()
    {
        return $this->hasOne('App\Models\DocManagement\Transactions\Contracts\Contracts', 'Contract_ID', 'Contract_ID');
    }

    public function checklist()
    {
        return $this->hasOne('App\Models\DocManagement\Transactions\Checklists\TransactionChecklists', 'Listing_ID', 'Listing_ID');
    }

    public function ScopeGetPropertyDetails($request, $transaction_type, $id, $select = null)
    {
        if (is_array($id)) {
            if ($transaction_type == 'listing') {
                $id = $id[0];
            } elseif ($transaction_type == 'contract') {
                $id = $id[1];
            } elseif ($transaction_type == 'referral') {
                $id = $id[2];
            }
        }

        if ($transaction_type == 'listing') {
            $property = self::find($id);
        } elseif ($transaction_type == 'contract') {
            $property = Contracts::find($id);
        } elseif ($transaction_type == 'referral') {
            $property = Referrals::find($id);
        }
        if ($select) {
            $property = $property->select($select);
        }

        return $property;
    }
}
