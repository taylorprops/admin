<?php

namespace App\Models\DocManagement\Checklists;

use Illuminate\Database\Eloquent\Model;

class Checklists extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_checklists';
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query -> where('checklist_active', 'yes');
        });
    }

    /* public function scopeIsChecklistInUse($query, $checklist_id) {
        $checklist_items = ChecklistsItems::where('checklist_id', $checklist_id) -> first();
        if(count($checklist_items) > 0) {
            return true;
        }
        return false;
    } */

    public function scopeGetChecklistsByPropertyType($query, $checklist_property_type_id, $checklist_location_id, $checklist_type) {
        $checklists = $query -> where('checklist_location_id', $checklist_location_id) -> where('checklist_property_type_id', $checklist_property_type_id);
        if ($checklist_type != '') {
            $checklists = $query -> where('checklist_type', $checklist_type);
        }
        $checklists = $query -> orderBy('checklist_order') -> get();

        return $checklists;
    }
}
