<?php

namespace App\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class CommissionChecksInQueue extends Model
{
    protected $table = 'commission_checks_in_queue';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

    public function agent() {
        return $this -> belongsTo(\App\Models\Employees\Agents::class, 'Agent_ID');
    }
}
