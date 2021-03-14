<?php

namespace App\Models\DocManagement\Transactions\Checklists;

use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsNotes;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklists;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionChecklistItems extends Model
{
    public $table = 'docs_transactions_checklist_items';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

    public function docs() {
        return $this -> hasMany(\App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs::class, 'checklist_item_id', 'id');
    }

    public function notes() {
        return $this -> hasMany(\App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsNotes::class, 'checklist_item_id', 'id') -> orderBy('created_at', 'DESC');
    }

    public function upload() {
        return $this -> hasOne(\App\Models\DocManagement\Create\Upload\Upload::class, 'file_id', 'checklist_form_id') -> orderBy('created_at', 'DESC');
    }

    public function ScopeMakeClosingDocsRequired($query, $checklist_id) {
        $checklist_items = self::where('checklist_id', $checklist_id) -> get();
        foreach ($checklist_items as $checklist_item) {
            if (Upload::IsClosingDoc($checklist_item -> checklist_form_id)) {
                self::find($checklist_item -> id) -> update(['checklist_item_required' => 'yes']);
            }
        }
    }

    public function ScopeChecklistComplete($query, $checklist_id, $include_closing_docs = null) {
        $complete = true;
        $items_completed = 0;
        $checklist_items = self::where('checklist_id', $checklist_id) -> where('checklist_item_required', 'yes') -> get();
        foreach ($checklist_items as $checklist_item) {
            // if not a pending release or closing docs
            if (! $include_closing_docs) {
                if (Upload::IsClosingDoc($checklist_item -> checklist_form_id) == false && Upload::IsRelease($checklist_item -> checklist_form_id) == false && Upload::IsWithdraw($checklist_item -> checklist_form_id) == false) {
                    if ($checklist_item -> checklist_item_status == 'not_reviewed' || $checklist_item -> checklist_item_status == 'rejected') {
                        $complete = false;
                    } else {
                        $items_completed += 1;
                    }
                }
            } else {
                if ($checklist_item -> checklist_item_status == 'not_reviewed' || $checklist_item -> checklist_item_status == 'rejected') {
                    $complete = false;
                } else {
                    $items_completed += 1;
                }
            }
        }

        return [
            'complete' => $complete,
            'items_completed' => $items_completed,
        ];
    }

    public function ScopeGetStatus($query, $checklist_item_id) {

        // see if any docs have been added
        $checklist_item = $this -> with('docs') -> find($checklist_item_id);
        $checklist_item_docs = $checklist_item -> docs;

        $docs_count = count($checklist_item_docs);
        // if doc_status is null for any added doc for this item than a form has been added but has not been reviewed
        $pending = $checklist_item_docs -> where('doc_status', 'pending');

        $show_mark_required = false;
        $show_mark_not_required = false;
        if ($checklist_item -> checklist_item_required == 'yes') {
            $show_mark_not_required = true;
        } else {
            $show_mark_required = true;
        }

        // Pending, If Applicable, Incomplete, Complete
        if (count($pending) > 0) {
            $status = 'Pending';
            $agent_classes = 'bg-blue-light text-primary';
            $admin_classes = 'bg-danger text-white';
            $fa = '<i class="fal fa-stopwatch fa-lg mr-2"></i>';
            $helper_text = 'We have received your document for this item. It is in the review process';
            $badge_class = 'badge-blue-light text-primary';
        } else {

            // if no docs have been added to checklist item the the only options are Required or If Applicable
            if ($docs_count == 0 && $checklist_item -> checklist_item_status != 'rejected') {
                if ($checklist_item -> checklist_item_required == 'yes') {
                    $status = 'Required';
                    $agent_classes = 'bg-orange text-white';
                    $admin_classes = 'bg-orange text-white';
                    $fa = '<i class="fad fa-exclamation-circle fa-lg mr-2"></i>';
                    $helper_text = 'This is a required item for this checklist';
                    $badge_class = 'badge-orange text-white';
                } else {
                    $status = 'If Applicable';
                    $agent_classes = 'bg-default-light text-white';
                    $admin_classes = 'bg-default-light text-white';
                    $fa = '<i class="fad fa-minus-circle fa-lg mr-2"></i>';
                    $helper_text = 'Depending on the details of the transaction, this form might not be required';
                    $badge_class = 'badge-primary text-white';
                }

                // if docs HAVE been added to checklist item the the only options are Complete or Rejected
            } else {
                if ($checklist_item -> checklist_item_status == 'accepted') {
                    $status = 'Complete';
                    $agent_classes = 'bg-success text-white';
                    $admin_classes = 'bg-success text-white';
                    $fa = '<i class="fal fa-check fa-lg mr-2"></i>';
                    $helper_text = 'The requirements for this checklist item have been met';
                    $badge_class = 'badge-success text-white';
                } elseif ($checklist_item -> checklist_item_status == 'rejected') {
                    $status = 'Rejected';
                    $agent_classes = 'bg-danger text-white';
                    $admin_classes = 'bg-default text-white';
                    $fa = '<i class="fad fa-exclamation-circle fa-lg mr-2"></i>';
                    $helper_text = 'Documents for this item have been rejected. They must be added again';
                    $badge_class = 'badge-danger text-white';
                }
            }
        }

        $details = collect();
        $details -> status = $status;
        $details -> agent_classes = $agent_classes;
        $details -> admin_classes = $admin_classes;
        $details -> fa = $fa;
        $details -> helper_text = $helper_text;
        $details -> show_mark_required = $show_mark_required;
        $details -> show_mark_not_required = $show_mark_not_required;
        $details -> badge_class = $badge_class;

        return $details;
    }
}
