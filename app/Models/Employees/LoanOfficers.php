<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanOfficers extends Model {

    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'emp_loan_officers';
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query -> where('id', '!=', '95');
        });
    }
}
