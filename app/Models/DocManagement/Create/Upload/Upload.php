<?php

namespace App\Models\DocManagement\Create\Upload;

use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklists;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;
use App\Models\DocManagement\Transactions\Listings\Listings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_create_uploads';
    protected $primaryKey = 'file_id';
    protected $guarded = [];

    public function fields()
    {
        return $this->hasMany('App\Models\DocManagement\Create\Fields\Fields', 'file_id');
    }

    public function images()
    {
        return $this->hasMany('App\Models\DocManagement\Create\Upload\UploadImages', 'file_id');
    }

    public function scopeIsContract($query, $checklist_form_id)
    {
        if ($checklist_form_id > 0) {
            $upload = $this->where('file_id', $checklist_form_id)->first();

            if ($upload->form_tags == ResourceItems::GetResourceID('contract', 'form_tags')) {
                return true;
            }
        }

        return false;
    }

    public function scopeIsClosingDoc($query, $checklist_form_id)
    {
        if ($checklist_form_id > 0) {
            $upload = $this->where('file_id', $checklist_form_id)->first();

            if ($upload->form_tags == ResourceItems::GetResourceID('closing_docs', 'form_tags')) {
                return true;
            }
        }

        return false;
    }

    public function scopeIsRelease($query, $checklist_form_id)
    {
        if ($checklist_form_id > 0) {
            $upload = $this->where('file_id', $checklist_form_id)->first();

            if ($upload->form_tags == ResourceItems::GetResourceID('release', 'form_tags')) {
                return true;
            }
        }

        return false;
    }

    public function scopeIsWithdraw($query, $checklist_form_id)
    {
        if ($checklist_form_id > 0) {
            $upload = $this->where('file_id', $checklist_form_id)->first();

            if ($upload->form_tags == ResourceItems::GetResourceID('withdraw', 'form_tags')) {
                return true;
            }
        }

        return false;
    }

    public function scopePendingReleases($query)
    {

        // get ids of all release forms
        $release_ids = $this->where('form_tags', ResourceItems::GetResourceID('release', 'form_tags'))->pluck('file_id');
        // limit checklist item search to only contracts pending cancellation
        $contracts_pending_cancellation_ids = Contracts::where('Status', ResourceItems::GetResourceID('Cancel Pending', 'contract_status'))->pluck('Contract_ID');
        // get checklist items that are releases
        $contract_checklist_items = TransactionChecklistItems::whereIn('Contract_ID', $contracts_pending_cancellation_ids)->whereIn('checklist_form_id', $release_ids)->pluck('id');
        // get contracts with pending releases uploaded
        $contract_ids = TransactionChecklistItemsDocs::whereIn('checklist_item_id', $contract_checklist_items)->where('doc_status', 'pending')->groupBy('Contract_ID')->pluck('Contract_ID');

        return $contract_ids;
    }

    public function scopeDocsSubmitted($query, $Listing_ID, $Contract_ID)
    {
        if ($Listing_ID) {
            $checklist = TransactionChecklists::where('Listing_ID', $Listing_ID)->first();
        } else {
            $checklist = TransactionChecklists::where('Contract_ID', $Contract_ID)->first();
        }
        $checklist_id = $checklist->id;
        $checklist_items = TransactionChecklistItems::where('checklist_id', $checklist_id)->get();

        $listing_submitted = false;
        $listing_accepted = false;
        $listing_withdraw_submitted = false;
        $listing_expired = false;
        $contract_submitted = false;
        $release_submitted = false;
        $our_listing = false;

        $listing_agreement_form_tags = ResourceItems::GetResourceID('listing_agreement', 'form_tags');
        $withdraw_form_tags = ResourceItems::GetResourceID('withdraw', 'form_tags');
        $contract_form_tags = ResourceItems::GetResourceID('contract', 'form_tags');
        $release_form_tags = ResourceItems::GetResourceID('release', 'form_tags');

        foreach ($checklist_items as $checklist_item) {
            if ($checklist_item->checklist_form_id > 0) {
                $checklist_form_id = $checklist_item->checklist_form_id;
                $upload = self::find($checklist_form_id);

                if ($upload->form_tags == $listing_agreement_form_tags) {
                    $listing_submitted_check = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item->id)->first();
                    if ($listing_submitted_check) {
                        if ($checklist_item->checklist_item_status != 'rejected') {
                            $listing_submitted = true;
                            if ($checklist_item->checklist_item_status == 'accepted') {
                                $listing_accepted = true;
                                $listing = Listings::find($Listing_ID);
                                if ($listing->ExpirationDate <= date('Y-m-d')) {
                                    $listing_expired = true;
                                    $our_listing = true;
                                }
                            }
                        }
                    }
                } elseif ($upload->form_tags == $withdraw_form_tags) {
                    $listing_withdraw_submitted_submitted_check = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item->id)->first();
                    if ($listing_withdraw_submitted_submitted_check) {
                        if ($checklist_item->checklist_item_status != 'rejected') {
                            $listing_withdraw_submitted = true;
                        }
                    }
                } elseif ($upload->form_tags == $contract_form_tags) {
                    $contract_submitted_check = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item->id)->first();
                    if ($contract_submitted_check) {
                        if ($checklist_item->checklist_item_status != 'rejected') {
                            $contract_submitted = true;
                        }
                    }
                } elseif ($upload->form_tags == $release_form_tags) {
                    $release_submitted_check = TransactionChecklistItemsDocs::where('checklist_item_id', $checklist_item->id)->first();
                    if ($release_submitted_check) {
                        if ($checklist_item->checklist_item_status != 'rejected') {
                            $release_submitted = true;
                        }
                    }
                }
            }
        }

        return compact('listing_submitted', 'listing_accepted', 'listing_withdraw_submitted', 'listing_expired', 'contract_submitted', 'release_submitted', 'our_listing');
    }

    public function scopeGetFormCount($query, $location_id)
    {
        $form_count = $this->where('form_group_id', $location_id)->count();

        return compact('form_count');
    }

    public function scopeFormGroupFiles($query, $location_id, $Listing_ID, $Contract_ID, $type)
    {
        $forms_available = $this->where('form_group_id', $location_id)
            ->where('published', 'yes')
            ->where('active', 'yes')
            ->orderBy('file_name_display', 'ASC')->get();

        //$forms_in_use = null;

        //if($type != '') {
        /* $field = 'Listing_ID';
        if($type == 'contract') {
            $field = 'Contract_ID';
        } */
        /* if($Contract_ID > 0 || $Listing_ID > 0) {
            $trash_folder = TransactionDocumentsFolders::where(function($query) use ($Contract_ID, $Listing_ID) {
                $query -> where(function($q) use ($Listing_ID) {
                    $q -> where('Listing_ID', $Listing_ID) -> where('Listing_ID', '>', '0');
                })
                -> orWhere(function($q) use ($Contract_ID) {
                    $q -> where('Contract_ID', $Contract_ID) -> where('Contract_ID', '>', '0');
                });
            })
            -> where('folder_name', 'Trash') -> first(); */

        /* $forms_in_use = TransactionDocuments::select('orig_file_id')
            -> where($field, $id)
            -> where('orig_file_id', '>', '0')
            -> where('folder', '!=', $trash_folder -> id)
            -> pluck('orig_file_id'); */
        //}
        //}

        return compact('forms_available');
    }

    public function scopeGetFormName($query, $form_id)
    {
        if ($form_id) {
            $form_name = $query->where('file_id', $form_id)->first();
            if ($form_name) {
                return $form_name->file_name_display;
            }

            return true;
        }

        return  true;
    }

    public function scopeGetFormLocation($query, $form_id)
    {
        $form_name = $query->where('file_id', $form_id)->first();
        if ($form_name->file_location) {
            return $form_name->file_location;
        }

        return '';
    }
}
