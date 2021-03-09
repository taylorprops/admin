<?php

namespace App\Models\DocManagement\Transactions\Members;

use Illuminate\Database\Eloquent\Model;

class TransactionCoordinators extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transaction_coordinators';
    protected $guarded = [];
}
