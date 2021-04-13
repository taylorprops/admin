<?php

namespace App\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class CommissionIncomeDeductions extends Model
{
    protected $table = 'commission_income_deductions';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];
}
