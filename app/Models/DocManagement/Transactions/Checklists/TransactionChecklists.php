<?php

namespace App\Models\DocManagement\Transactions\Checklists;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DocManagement\Checklists\Checklists;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItems;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;
use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Create\Upload\Upload;

class TransactionChecklists extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_checklists';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function items() {
        return $this -> hasMany('App\Models\DocManagement\Checklists\ChecklistsItems', 'checklist_id', 'id') -> with('docs') -> with('notes');
    }

    public function ScopeCreateTransactionChecklist($request, $checklist_id, $Listing_ID, $Contract_ID, $Referral_ID, $Agent_ID, $checklist_represent, $checklist_type, $checklist_property_type_id, $checklist_property_sub_type_id, $checklist_sale_rent, $checklist_state, $checklist_location_id, $checklist_hoa_condo, $checklist_year_built) {

        $for_sale_and_rent = false;
        if($checklist_type == 'referral') {
            $where = [['checklist_type', 'referral']];
        } else {
            if($checklist_sale_rent == 'both') {
                $checklist_sale_rent = 'sale';
                $for_sale_and_rent = true;
            } else if($checklist_sale_rent == 'rental') {
                $checklist_property_sub_type_id = 0;
            }
            $where = [
                ['checklist_represent', $checklist_represent],
                ['checklist_type', $checklist_type],
                ['checklist_property_type_id', $checklist_property_type_id],
                ['checklist_property_sub_type_id', $checklist_property_sub_type_id],
                ['checklist_sale_rent', $checklist_sale_rent],
                ['checklist_state', $checklist_state],
                ['checklist_location_id', $checklist_location_id]
            ];
        }



        // get checklist
        $checklist = Checklists::where($where) -> first();

        /* $checklist = Checklists::where($where);
        dd(vsprintf(str_replace('?', '%s', $checklist -> toSql()), collect($checklist -> getBindings()) -> map(function($binding){
            return is_numeric($binding) ? $binding : "'{$binding}'";
        }) -> toArray())); */

        // get checklist items
        $items = ChecklistsItems::where('checklist_id', $checklist -> id) -> orderBy('checklist_item_order') -> get();

        // some items and docs from old checklist will be kept and added to the new
        $remove_ids = [];
        $keep_form_ids = [];

        // if existing checklist then keep items/forms from old checklist
        // that will be on the new so the user doesn't have to add docs and notes to checklist
        if($checklist_id) {

            $existing_checklist = TransactionChecklists::find($checklist_id);
            $existing_checklist -> checklist_id = $checklist -> id;
            $existing_checklist -> Listing_ID = $Listing_ID;
            $existing_checklist -> Contract_ID = $Contract_ID;
            $existing_checklist -> Agent_ID = $Agent_ID;
            $existing_checklist -> save();

            $existing_items = TransactionChecklistItems::where('checklist_id', $checklist_id) -> get();

            // get all ids for new checklist items
            $new_form_ids = [];
            foreach($items as $item) {
                $new_form_ids[] = $item -> checklist_form_id;
            }
            // group items that are going to be kept and removed
            foreach($existing_items as $existing_item) {
                if(in_array($existing_item -> checklist_form_id, $new_form_ids)) {
                    $keep_form_ids[] = $existing_item -> checklist_form_id;
                } else {
                    $remove_ids[] = $existing_item -> id;
                }
            }

            // if there are items no longer needed, remove them
            if(count($remove_ids) > 0) {
                $remove_items = TransactionChecklistItems::whereIn('id', $remove_ids) -> delete();
                $remove_item_docs = TransactionChecklistItemsDocs::whereIn('checklist_item_id', $remove_ids) -> delete();
                $remove_item_notes = TransactionChecklistItemsNotes::whereIn('checklist_item_id', $remove_ids) -> delete();
                $remove_docs = TransactionDocuments::whereIn('checklist_item_id', $remove_ids) -> update(['assigned' => 'no', 'checklist_item_id' => '0']);
            }

        } else {

            // if no existing checklist create a new one
            $add_checklist = new TransactionChecklists();
            $add_checklist -> checklist_id = $checklist -> id;
            $add_checklist -> Listing_ID = $Listing_ID;
            $add_checklist -> Contract_ID = $Contract_ID;
            $add_checklist -> Referral_ID = $Referral_ID;
            $add_checklist -> Agent_ID = $Agent_ID;
            $add_checklist -> hoa_condo = $checklist_hoa_condo;
            $add_checklist -> year_built = $checklist_year_built;
            $add_checklist -> sale_rent = $checklist_sale_rent;
            $add_checklist -> save();
            $checklist_id = $add_checklist -> id;

        }

        // if not an item from old checklist that is transferred to new, add to new checklist
        $required_count = 0;
        foreach($items as $item) {

            if($item -> checklist_item_required == 'yes') {
                $required_count += 1;
            }

            if(!in_array($item -> checklist_form_id, $keep_form_ids)) {

                $add_checklist_items = new TransactionChecklistItems();
                $add_checklist_items -> checklist_id = $checklist_id;
                $add_checklist_items -> Listing_ID = $Listing_ID;
                $add_checklist_items -> Contract_ID = $Contract_ID;
                $add_checklist_items -> Referral_ID = $Referral_ID;
                $add_checklist_items -> Agent_ID = $Agent_ID;
                $add_checklist_items -> checklist_form_id = $item -> checklist_form_id;
                $add_checklist_items -> checklist_item_required = $item -> checklist_item_required;
                $add_checklist_items -> checklist_item_group_id = $item -> checklist_item_group_id;
                $add_checklist_items -> checklist_item_order = $item -> checklist_item_order;
                /* if($item -> checklist_item_required == 'yes') {
                    $add_checklist_items -> checklist_item_status = 'Required';
                } */

                $add_checklist_items -> save();

            }

        }


        // update required items if lead, hoa, etc
        $form_tags = ResourceItems::where('resource_type', 'form_tags') -> get();

        if($checklist_type == 'listing') {
            $property = Listings::find($Listing_ID);
        } else if($checklist_type == 'contract') {
            $property = Contracts::find($Contract_ID);
        } else if($checklist_type == 'referral') {
            $property = Referrals::find($Referral_ID);
        }

        $property -> DocsMissingCount = $required_count;
        $property -> save();

        // set all tagged items to not required
        $if_applicable = [];
        foreach($form_tags as $form_tag) {
            $if_applicable[$form_tag -> resource_name]['id'] = $form_tag -> resource_id;
            $if_applicable[$form_tag -> resource_name]['required'] = false;
        }

        // tags that will always be required
        $if_applicable['listing_agreement']['required'] = true;
        $if_applicable['contract']['required'] = true;

        // tags to ignore
        $ignore_tags = ['release', 'closing_docs', 'withdraw'];

        // set items to required if applicable for this transaction
        // set lead paint, hoa and condo
        if($checklist_state == 'MD' || $checklist_state == 'DC') {
            if($checklist_year_built < 1978) {
                $if_applicable['lead_paint']['required'] = true;
            }
            if($checklist_hoa_condo == 'hoa') {
                $if_applicable['hoa']['required'] = true;
            } else if($checklist_hoa_condo == 'condo') {
                $if_applicable['condo']['required'] = true;
            }
        }

        // set earnest held by title
        if($property -> EarnestHeldBy == 'title') {
            $if_applicable['title_holding_earnest']['required'] = true;
        }

        // if both listing and contract require rental listing agreement too
        if($for_sale_and_rent) {
            $if_applicable['rental_listing_agreement']['required'] = true;
        }

        $transaction_checklist_items = TransactionChecklistItems::where('checklist_id', $checklist_id) -> get();

        // mark all if applicable items required or remove
        $transaction_checklist_items -> map(function($transaction_checklist_item) use ($if_applicable, $form_tags, $ignore_tags) {

            $upload = Upload::where('file_id', $transaction_checklist_item -> checklist_form_id) -> first();
            $form_tag_id = $upload -> form_tags;

            foreach($form_tags -> whereNotIn('resource_name', $ignore_tags) as $form_tag) {
                // see if required form tag matches checklist item form_tag
                if($if_applicable[$form_tag -> resource_name]['id'] == $form_tag_id) {
                    if($if_applicable[$form_tag -> resource_name]['required']) {
                        $transaction_checklist_item -> checklist_item_required = 'yes';
                        $transaction_checklist_item -> save();
                    } else {
                        $transaction_checklist_item -> delete();
                    }
                }
            }

        });

    }
}
