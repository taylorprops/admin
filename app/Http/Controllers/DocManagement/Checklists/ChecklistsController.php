<?php

namespace App\Http\Controllers\DocManagement\Checklists;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Checklists\Checklists;
use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Resources\ResourceItems;
use Illuminate\Http\Request;

class ChecklistsController extends Controller
{

    public function duplicate_checklist(Request $request) {

		$checklist_id = $request -> checklist_id;
        $checklist = Checklists::where('id', $checklist_id) -> first();
        $duplicate = $checklist -> replicate();
        $duplicate -> save();
        $new_checklist_id = $duplicate -> id;

        // get all checklist items from target checklists to copy
        $add_from_checklist_items = ChecklistsItems::where('checklist_id', $checklist_id) -> get();
        foreach ($add_from_checklist_items as $add_from_item) {
            $checklist_item_copy = $add_from_item -> replicate();
            $checklist_item_copy -> checklist_id = $new_checklist_id;
            $checklist_item_copy -> save();
        }
    }

    public function save_copy_checklists(Request $request) {

		$copy_from_location_id = $request -> location_id;
        $checklist_type = $request -> checklist_type;
        $copy_to_checklist_location_ids = explode(',', $request -> checklist_location_ids);
        $checklists_to_copy_ids = explode(',', $request -> checklists_to_copy_ids);
        // get all checklist ids from checklists in checklist_locations_ids
        $remove_checklist_ids = Checklists::select('id') -> where('checklist_type', $checklist_type) -> whereIn('checklist_location_id', $copy_to_checklist_location_ids) -> get() -> toArray();

        if (count($remove_checklist_ids) > 0) {
            // delete all checklist items from target checklists using checklist ids
            $remove_checklist_items = ChecklistsItems::whereIn('checklist_id', $remove_checklist_ids) -> delete();
            // delete all target checklists using checklist ids
            $remove_checklists = Checklists::whereIn('id', $remove_checklist_ids) -> delete();
        }

        // add checklists and checklist items from source form group
        // get all target checklists to copy
        $add_from_checklists = Checklists::whereIn('id', $checklists_to_copy_ids) -> get();

        // loop through all target locations and add checklists and checklist items
        foreach ($copy_to_checklist_location_ids as $location) {
            // replicate all checklists from target location to new one
            $state = ResourceItems::getState($location);

            foreach ($add_from_checklists as $add_from_checklist) {
                $checklist_copy = $add_from_checklist -> replicate();
                $checklist_copy -> checklist_location_id = $location;
                $checklist_copy -> checklist_state = $state;
                $checklist_copy -> save();
                $new_checklist_id = $checklist_copy -> id;

                // get all checklist items from target checklists to copy
                $add_from_checklist_items = ChecklistsItems::where('checklist_id', $add_from_checklist -> id) -> get();
                foreach ($add_from_checklist_items as $add_from_item) {
                    $checklist_item_copy = $add_from_item -> replicate();
                    $checklist_item_copy -> checklist_id = $new_checklist_id;
                    $checklist_item_copy -> save();
                }
            }
        }
    }

    public function get_copy_checklists(Request $request) {

		$location_id = $request -> location_id;
        $checklist_type = $request -> checklist_type;

        $resource_items = new ResourceItems();
        // form groups to add to
        $form_groups = $resource_items -> where('resource_type', 'checklist_locations') -> where('resource_id', '!=', $location_id) -> orderBy('resource_order') -> get();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();

        $checklists_functions = new Checklists();

        return view('doc_management/checklists/get_copy_checklists_html', compact('location_id', 'checklist_type', 'form_groups', 'property_types', 'resource_items', 'checklists_functions'));
    }

    public function get_checklist_item_details(Request $request) {

		$details = ChecklistsItems::where('checklist_form_id', $request -> form_id) -> first();

        return $details ?? null;
    }

    public function get_checklist_items(Request $request) {

		$checklist_id = $request -> checklist_id;

        $files = new Upload();
        $resource_items = new ResourceItems();

        $checklist = Checklists::whereId($checklist_id) -> first();

        $checklist_types = ['listing', 'both'];
        if ($checklist -> checklist_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } elseif ($checklist -> checklist_type == 'referral') {
            $checklist_types = ['referral'];
        }

        $checklist_items_model = new ChecklistsItems();
        $form_groups = $resource_items -> where('resource_type', 'form_groups') -> orderBy('resource_order') -> get();
        $checklist_groups = $resource_items -> where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        return view('doc_management/checklists/get_add_checklist_items_html', compact('checklist', 'form_groups', 'files', 'resource_items', 'checklist_groups', 'checklist_items_model', 'checklist_id'));
    }

    public function checklists() {
        $resource_items = new ResourceItems();
        $property_types = ResourceItems::where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = ResourceItems::where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();
        $locations = ResourceItems::where('resource_type', 'checklist_locations') -> orderBy('resource_order') -> get();

        return view('doc_management/checklists/checklists', compact('resource_items', 'property_types', 'property_sub_types', 'locations'));
    }

    public function get_checklists(Request $request) {

		$checklist_location_id = $request -> checklist_location_id;
        $checklist_type = $request -> checklist_type;

        $checklists_model = new Checklists();
        $checklists = Checklists::where('checklist_location_id', $checklist_location_id) -> where('active', 'yes') -> orderBy('checklist_order', 'ASC') -> get();
        $checklists_count = count($checklists);

        $checklist_property_type_id = '';

        $resource_items = new ResourceItems();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $checklist_location = $resource_items -> where('resource_id', $checklist_location_id) -> first();
        $checklist_state = $checklist_location -> resource_state ?? null;

        return view('doc_management/checklists/get_checklists_html', compact('checklists_model', 'property_types', 'checklists_count', 'resource_items', 'checklist_type', 'checklist_location_id', 'checklist_state', 'checklist_property_type_id'));
    }

    public function add_checklist_items(Request $request) {

		$checklist_id = $request -> checklist_id;
        // delete current checklist items
        $delete_checklist_items = ChecklistsItems::where('checklist_id', $checklist_id) -> delete();

        $checklist_array = json_decode($request -> checklist_items);

        foreach ($checklist_array as $checklist_items) {
            $add_checklist_items = new ChecklistsItems();

            $add_checklist_items -> checklist_id = $checklist_id;
            $add_checklist_items -> checklist_form_id = $checklist_items -> checklist_form_id;
            $add_checklist_items -> checklist_item_required = $checklist_items -> checklist_item_required;
            $add_checklist_items -> checklist_item_group_id = $checklist_items -> checklist_item_group_id;
            $add_checklist_items -> checklist_item_order = $checklist_items -> checklist_item_order;

            $add_checklist_items -> save();
        }

        // set checklist count column
        $checklist_item_count = count($checklist_array);
        $update_count = Checklists::where('id', $checklist_id) -> first();
        $update_count -> checklist_count = $checklist_item_count;
        $update_count -> save();

        $reorder_items = new ChecklistsItems();
        $reorder_items -> updateChecklistItemsOrder($checklist_id);
    }

    public function add_checklist_referral(Request $request) {

		$checklist = new Checklists();
        $checklist -> checklist_location_id = $request -> checklist_location_id;
        $checklist -> checklist_type = $request -> checklist_type;
        $checklist -> checklist_state = $request -> checklist_state;
        $checklist -> checklist_order = 0;
        $checklist -> save();
    }

    public function add_checklist(Request $request) {

		$checklist_property_sub_type_id = 0;
        if ($request -> checklist_property_sub_type_id) {
            $checklist_property_sub_type_id = $request -> checklist_property_sub_type_id;
        }
        $checklist = new Checklists();
        $checklist -> checklist_location_id = $request -> checklist_location_id;
        $checklist -> checklist_represent = $request -> checklist_represent;
        $checklist -> checklist_type = $request -> checklist_type;
        $checklist -> checklist_sale_rent = $request -> checklist_sale_rent;
        $checklist -> checklist_property_type_id = $request -> checklist_property_type_id;
        $checklist -> checklist_property_sub_type_id = $checklist_property_sub_type_id;
        $checklist -> checklist_state = $request -> checklist_state;
        $checklist -> checklist_order = 0;
        $checklist -> save();
    }

    public function edit_checklist(Request $request) {

		$checklist = Checklists::where('id', $request -> checklist_id) -> first();
        $checklist -> checklist_location_id = $request -> checklist_location_id;
        $checklist -> checklist_represent = $request -> checklist_represent;
        $checklist -> checklist_type = $request -> checklist_type;
        $checklist -> checklist_sale_rent = $request -> checklist_sale_rent;
        $checklist -> checklist_property_type_id = $request -> checklist_property_type_id;
        $checklist -> checklist_property_sub_type_id = $request -> checklist_property_sub_type_id;
        $checklist -> checklist_state = $request -> checklist_state;
        $checklist -> save();
    }

    public function delete_checklist(Request $request) {

		$checklist_id = $request -> checklist_id;
        if ($checklist_id) {
            $checklist = Checklists::where('id', $checklist_id) -> update([
                'active' => 'no'
            ]);
            $checklist_items = ChecklistsItems::where('checklist_id', $checklist_id) -> update([
                'active' => 'no'
            ]);
        }
    }

    public function reorder_checklists(Request $request) {

		$data = json_decode($request['data'], true);
        $data = $data['checklist'];

        foreach ($data as $item) {
            $checklist_id = $item['checklist_id'];
            $checklist_order = $item['checklist_index'];
            $reorder = Checklists::whereId($checklist_id) -> first();
            $reorder -> checklist_order = $checklist_order;
            $reorder -> save();
        }

        $reorder_items = new ChecklistsItems();
        $reorder_items -> updateChecklistItemsOrder($checklist_id);
    }
}
