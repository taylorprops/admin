<?php

namespace App\Models\DocManagement\Resources;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Checklists\Checklists;
use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\DocManagement\Create\Upload\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ResourceItems extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_resource_items';
    protected $primaryKey = 'resource_id';
    public $timestamps = false;
    protected $guarded = [];

    public function uploads() {
        return $this -> hasMany(\App\Models\DocManagement\Create\Upload\Upload::class, 'form_group_id', 'resource_id') -> where(['published' => 'yes', 'active' => 'yes']) -> orderBy('file_name_display');
    }

    public function earnest() {
        return $this -> hasMany(\App\Models\DocManagement\Earnest\Earnest::class, 'earnest_account_id', 'resource_id') -> with('checks');
    }

    // allow only active records on all queries
    /* public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query -> where('resource_active', 'yes');
        });
    } */

    /* public function scopeIsResourceInUse($query, $resource_id) {

        $checklist_property_type_ids = Checklists::select('checklist_property_type_id') -> get() -> pluck('checklist_property_type_id') -> toArray();
        $checklist_property_sub_type_ids = Checklists::select('checklist_property_sub_type_id') -> get() -> pluck('checklist_property_sub_type_id') -> toArray();
        $checklist_location_ids = Checklists::select('checklist_location_id') -> get() -> pluck('checklist_location_id') -> toArray();
        $checklist_item_group_ids = ChecklistsItems::select('checklist_item_group_id') -> get() -> pluck('checklist_item_group_id') -> toArray();
        $form_group_ids = Upload::select('form_group_id') -> get() -> pluck('form_group_id') -> toArray();
        $form_categories_list = Upload::select('form_categories') -> get() -> pluck('form_categories') -> toArray();
        $form_categories_ids = [];
        foreach($form_categories_list as $form_categories) {
            $form_categories_array = explode(',', $form_categories);
            foreach($form_categories_array as $form_categories_item) {
                $form_categories_ids[] = $form_categories_item;
            }
        }
        $all_ids = array_merge($checklist_property_type_ids, $checklist_property_sub_type_ids, $checklist_location_ids, $checklist_item_group_ids, $form_group_ids, $form_categories_ids);
        $resource_ids_in_use = array_unique($all_ids);
        if(in_array($resource_id, $resource_ids_in_use)) {
            return true;
        }
        return false;

    } */
    public function scopeSellerResourceId() {
        $resource_id = self::select('resource_id') -> where('resource_name', 'Seller') -> first();

        return $resource_id -> resource_id;
    }

    public function scopeBuyerResourceId() {
        $resource_id = self::select('resource_id') -> where('resource_name', 'Buyer') -> first();

        return $resource_id -> resource_id;
    }

    public function scopeBuyerAgentResourceId() {
        $resource_id = self::select('resource_id') -> where('resource_name', 'Buyer Agent') -> first();

        return $resource_id -> resource_id;
    }

    public function scopeListingAgentResourceId() {
        $resource_id = self::select('resource_id') -> where('resource_name', 'Listing Agent') -> first();

        return $resource_id -> resource_id;
    }

    public function scopeTitleResourceId() {
        $resource_id = self::select('resource_id') -> where('resource_name', 'Title') -> first();

        return $resource_id -> resource_id;
    }

    public function scopeBrokerResourceId() {
        $resource_id = self::select('resource_id') -> where('resource_name', 'Broker') -> first();

        return $resource_id -> resource_id;
    }

    public function getCountFormGroup($id) {
        $uploads = Upload::where('form_group_id', $id) -> get() -> count();

        return $uploads;
    }

    public function scopeGetLocation($query, $id) {
        $location = $query -> select('resource_name') -> where('resource_id', $id) -> first();

        return $location -> resource_name;
    }

    public function scopeGetState($query, $resource_id) {
        $location = $query -> select('resource_state') -> where('resource_id', $resource_id) -> first();

        return $location -> resource_state;
    }

    public function scopeGetResourceName($query, $id) {
        if ($id) {
            $resource = $query -> select('resource_name') -> where('resource_id', $id) -> first();

            return $resource -> resource_name;
        }

        return false;
    }

    public function scopeGetResourceID($query, $name, $type) {
        if ($name) {
            $resource = $query -> select('resource_id') -> where('resource_name', $name) -> where('resource_type', $type) -> first();

            return $resource -> resource_id;
        }

        return false;
    }

    public function scopeGetCategoryColor($query, $id) {
        $tags = $query -> select('resource_color') -> where('resource_id', $id) -> first();

        return $tags -> resource_color;
    }

    public function scopeGetActiveListingStatuses($query, $include_under_contract, $include_expired, $include_withdrawn) {
        $statuses = ['Pre-Listing', 'Active'];
        if ($include_under_contract == 'yes') {
            $statuses[] = 'Under Contract';
        }
        if ($include_expired == 'yes') {
            $statuses[] = 'Expired';
        }
        if ($include_withdrawn == 'yes') {
            $statuses[] = 'Withdrawn';
        }
        $ids = $this -> where('resource_type', 'listing_status') -> whereIn('resource_name', $statuses) -> pluck('resource_id');

        return $ids;
    }

    public function scopeGetClosedAndCanceledListingStatuses($query) {
        $statuses = ['Closed', 'Withdrawn', 'Canceled'];

        $ids = $this -> where('resource_type', 'listing_status') -> whereIn('resource_name', $statuses) -> pluck('resource_id');

        return $ids;
    }

    public function scopeGetClosedAndCanceledContractStatuses($query) {
        $statuses = ['Closed', 'Released', 'Cancel Pending', 'Canceled'];

        $ids = $this -> where('resource_type', 'listing_status') -> whereIn('resource_name', $statuses) -> pluck('resource_id');

        return $ids;
    }

    public function scopeGetActiveContractStatuses() {
        $ids = $this -> where('resource_type', 'contract_status') -> where('resource_name', 'Active') -> pluck('resource_id');

        return $ids;
    }

    public function scopeGetActiveAndClosedContractStatuses() {
        $ids = $this -> where('resource_type', 'contract_status') -> whereIn('resource_name', ['Active', 'Closed']) -> pluck('resource_id');

        return $ids;
    }

    public function scopeGetActiveReferralStatuses() {
        $ids = $this -> where('resource_type', 'referral_status') -> where('resource_name', 'Active') -> pluck('resource_id');

        return $ids;
    }
}
