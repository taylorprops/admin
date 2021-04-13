<?php

namespace App\Models\DocManagement\Transactions\Documents;

use Illuminate\Database\Eloquent\Model;

class TransactionDocumentsImages extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_transactions_docs_images';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
