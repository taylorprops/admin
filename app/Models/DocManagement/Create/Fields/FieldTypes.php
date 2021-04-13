<?php

namespace App\Models\DocManagement\Create\Fields;

use Illuminate\Database\Eloquent\Model;

class FieldTypes extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_create_field_types';
    public $timestamps = false;
    protected $guarded = [];
}
