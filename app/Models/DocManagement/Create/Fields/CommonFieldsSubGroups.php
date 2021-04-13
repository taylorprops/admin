<?php

namespace App\Models\DocManagement\Create\Fields;

use Illuminate\Database\Eloquent\Model;

class CommonFieldsSubGroups extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_create_common_fields_sub_groups';
    public $timestamps = false;
    protected $guarded = [];

    public function ScopeGetSubGroupTitle($query, $id) {
        if ($id) {
            $sub_group = $query -> find($id);

            return $sub_group -> sub_group_name;
        }

        return false;
    }
}
