<?php

namespace App\Models\DocManagement\Earnest;

use Illuminate\Database\Eloquent\Model;

class Earnest extends Model
{
    public $table = 'earnest';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function checks()
    {
        return $this->hasMany(\App\Models\DocManagement\Earnest\EarnestChecks::class, 'Earnest_ID', 'id');
    }

    public function notes()
    {
        return $this->hasMany(\App\Models\DocManagement\Earnest\EarnestNotes::class, 'Earnest_ID', 'id')->orderBy('created_at', 'desc');
    }

    public function agent()
    {
        return $this->hasOne(\App\Models\Employees\Agents::class, 'id', 'Agent_ID');
    }

    public function property()
    {
        return $this->hasOne(\App\Models\DocManagement\Transactions\Contracts\Contracts::class, 'Contract_ID', 'Contract_ID');
    }

    public function earnest_account()
    {
        return $this->hasOne(\App\Models\DocManagement\Resources\ResourceItems::class, 'resource_id', 'earnest_account_id');
    }
}
