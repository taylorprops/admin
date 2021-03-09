<?php

namespace App\Models\DocManagement\Create\Fields;

use Illuminate\Database\Eloquent\Model;

class CommonFieldsGroups extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_create_common_fields_groups';
    public $timestamps = false;
    protected $guarded = [];

    public function common_fields()
    {
        return $this->hasMany('App\Models\DocManagement\Create\Fields\CommonFields', 'group_id')->orderBy('group_id')->orderBy('field_order');
    }

    public function sub_groups()
    {
        return $this->hasMany('App\Models\DocManagement\Create\Fields\CommonFieldsSubGroups', 'group_id')->orderBy('group_id')->orderBy('sub_group_order');
    }
}
