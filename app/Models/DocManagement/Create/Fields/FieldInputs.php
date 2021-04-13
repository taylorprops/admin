<?php

namespace App\Models\DocManagement\Create\Fields;

use Illuminate\Database\Eloquent\Model;

class FieldInputs extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_create_fields_inputs';
    public $timestamps = false;
    protected $guarded = [];
}
