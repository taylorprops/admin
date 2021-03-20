<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanOfficerDocs extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    public $table = 'emp_loan_officers_docs';
    protected $guarded = [];

}
