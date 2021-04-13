<?php

namespace App\Models\DocManagement\Transactions\Members;

use App\Models\DocManagement\Resources\ResourceItems;
use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_transactions_members';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function ScopeGetMemberTypeID($query, $member_type) {
        $member_type = ResourceItems::where('resource_name', $member_type) -> where('resource_type', 'contact_type') -> first();

        return $member_type -> resource_id;
    }

    public function contracts() {
        return $this -> hasMany(\App\Models\DocManagement\Transactions\Contracts\Contracts::class, 'Contract_ID', 'Contract_ID');
    }

    public function listings() {
        return $this -> hasMany(\App\Models\DocManagement\Transactions\Listings\Listings::class, 'Listing_ID', 'Listing_IDz');
    }

}
