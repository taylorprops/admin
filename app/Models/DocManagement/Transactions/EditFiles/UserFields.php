<?php

namespace App\Models\DocManagement\Transactions\EditFiles;

use Illuminate\Database\Eloquent\Model;

class UserFields extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_transaction_fields';
    protected $guarded = [];

    public function user_field_inputs() {
        return $this -> hasMany(\App\Models\DocManagement\Transactions\EditFiles\UserFieldsInputs::class, 'transaction_field_id', 'id');
    }

    public function common_field() {
        return $this -> hasOne(\App\Models\DocManagement\Create\Fields\CommonFields::class, 'id', 'common_field_id');
    }
}
