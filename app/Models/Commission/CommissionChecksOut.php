<?php

namespace App\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class CommissionChecksOut extends Model
{
    protected $table = 'commission_checks_out';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];
}
