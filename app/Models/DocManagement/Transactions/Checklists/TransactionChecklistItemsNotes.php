<?php

namespace App\Models\DocManagement\Transactions\Checklists;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionChecklistItemsNotes extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_checklist_item_notes';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function ScopeGetNotes($query, $checklist_item_id)
    {
        $notes = $this->where('checklist_item_id', $checklist_item_id);
        $notes = $notes->orderBy('created_at', 'DESC')->get();

        return $notes;
    }

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'note_user_id');
    }
}
