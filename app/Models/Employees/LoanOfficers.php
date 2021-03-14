<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;

class LoanOfficers extends Model
{
    protected $connection = 'mysql';
    public $table = 'emp_loan_officers';
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query -> where('id', '!=', '95');
        });
    }
}
