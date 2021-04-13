<?php

namespace App\Models\DocManagement\Transactions\Documents;

use Illuminate\Database\Eloquent\Model;

class TransactionDocumentsEmailed extends Model
{
    protected $table = 'docs_transactions_emailed_docs';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
