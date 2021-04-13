<?php

namespace App\Models\DocManagement\Transactions\Documents;

use Illuminate\Database\Eloquent\Model;

class TransactionDocumentsFolders extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_transactions_docs_folders';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
