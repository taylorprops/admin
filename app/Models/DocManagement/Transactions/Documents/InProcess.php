<?php

namespace App\Models\DocManagement\Transactions\Documents;

use Illuminate\Database\Eloquent\Model;

class InProcess extends Model
{
    protected $table = 'docs_transactions_docs_in_process';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
