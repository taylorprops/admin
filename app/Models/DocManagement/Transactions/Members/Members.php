<?php

namespace App\Models\DocManagement\Transactions\Members;

use App\Models\DocManagement\Resources\ResourceItems;
use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_members';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function ScopeGetMemberTypeID($query, $member_type)
    {
        $member_type = ResourceItems::where('resource_name', $member_type)->where('resource_type', 'contact_type')->first();

        return $member_type->resource_id;
    }
}
