<?php

namespace App\Models\DocManagement\Transactions\Upload;

use Illuminate\Database\Eloquent\Model;

class TransactionUploadImages extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_uploads_images';
}
