<?php

namespace App\Models\DocManagement\Transactions\EditFiles;

use Illuminate\Database\Eloquent\Model;

class UserFieldsInputs extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_transaction_fields_inputs';
    protected $guarded = [];
}
