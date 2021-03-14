<?php

namespace App\Models\DocManagement\Checklists;

use App\Models\DocManagement\Create\Upload\Upload;
use Illuminate\Database\Eloquent\Model;

class ChecklistsItems extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_checklists_items';
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query -> where('checklist_item_active', 'yes');
        });
    }

    public function ScopeGetFormName($query, $id) {
        $upload = Upload::where('file_id', $id) -> first();

        return $upload -> file_name_display;
    }

    /* public function ScopeGetFormHelpDetails($query, $id) {
        $upload = Upload::where('file_id', $id) -> first();
        $details = collect();
        $details -> helper_text = $upload -> helper_text;
        $details -> file_location = $upload -> file_location;

        return compact('details');
    } */

    public function ScopeGetChecklistItemsByGroup($query, $checklist_id, $group_id) {
        $checklists = $query -> where('checklist_id', $checklist_id) -> where('checklist_item_group_id', $group_id) -> orderBy('checklist_item_order') -> get();
        if (count($checklists) > 0) {
            return $checklists;
        }

        return null;
    }

    public function ScopeCountInChecklist($query, $form_id) {
        $found = $query -> where('checklist_form_id', $form_id) -> get();
        if ($found) {
            return count($found);
        }

        return 0;
    }

    public function ScopeIfFormInChecklist($query, $checklist_id, $form_id) {
        $found = self::where('checklist_id', $checklist_id) -> where('checklist_form_id', $form_id) -> first();
        if ($found) {
            return true;
        }

        return false;
    }

    public function scopeGetChecklistItemsCount($query, $checklist_id) {
        if ($checklist_id) {
            $checklist_items = $query -> where('checklist_id', $checklist_id) -> count();

            return $checklist_items;
        }

        return '0';
    }

    public function updateChecklistItemsOrder($checklist_id) {
        // reset order to start from 0
        $checklist_items = self::where('checklist_id', $checklist_id) -> orderBy('checklist_item_order', 'ASC') -> get();
        foreach ($checklist_items as $index => $checklist_item) {
            $item = self::where('id', $checklist_item -> id) -> first();
            $item -> checklist_item_order = $index;
            $item -> save();
        }
    }
}
