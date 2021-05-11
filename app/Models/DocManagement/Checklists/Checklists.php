<?php

namespace App\Models\DocManagement\Checklists;

use Illuminate\Database\Eloquent\Model;

class Checklists extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_checklists';
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query -> where('checklist_active', 'yes');
        });
    }

    public function checklist_items() {
        return $this -> hasMany(\App\Models\DocManagement\Checklists\ChecklistsItems::class, 'checklist_id', 'id') -> orderBy('checklist_item_order');
    }

    public function scopeGetChecklistsByPropertyType($query, $checklist_property_type_id, $checklist_location_id, $checklist_type) {
        $checklists = $query -> where('checklist_location_id', $checklist_location_id)
        -> where('checklist_property_type_id', $checklist_property_type_id)
        -> where('active', 'yes');
        if ($checklist_type != '') {
            $checklists = $query -> where('checklist_type', $checklist_type);
        }
        $checklists = $query -> orderBy('checklist_order') -> get();

        return $checklists;
    }
}
