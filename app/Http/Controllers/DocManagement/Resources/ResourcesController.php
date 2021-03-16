<?php

namespace App\Http\Controllers\DocManagement\Resources;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Create\Fields\CommonFields;
use App\Models\DocManagement\Create\Fields\CommonFieldsGroups;
use App\Models\DocManagement\Create\Fields\CommonFieldsSubGroups;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\Resources\LocationData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResourcesController extends Controller
{
    public function common_fields(Request $request) {

		$common_fields_groups = CommonFieldsGroups::orderBy('group_order')
            -> with('sub_groups')
            -> get();

        $common_fields_sub_groups = CommonFieldsSubGroups::orderBy('sub_group_order')
            -> get();

        $db_fields_listing = DB::getSchemaBuilder() -> getColumnListing('docs_transactions_listings');
        $db_fields_contract = DB::getSchemaBuilder() -> getColumnListing('docs_transactions_contracts');
        $db_fields = array_unique(array_merge($db_fields_listing, $db_fields_contract));

        return view('/doc_management/resources/common_fields', compact('common_fields_groups', 'common_fields_sub_groups', 'db_fields'));
    }

    public function get_common_fields(Request $request) {

		$common_fields_groups = CommonFieldsGroups::with('common_fields:id,field_type,group_id,sub_group_id,field_name,db_column_name')
            -> with('sub_groups')
            -> orderBy('group_order')
            -> get();

        $common_fields_sub_groups = CommonFieldsSubGroups::orderBy('sub_group_order')
            -> get();

        $db_fields_listing = DB::getSchemaBuilder() -> getColumnListing('docs_transactions_listings');
        $db_fields_contract = DB::getSchemaBuilder() -> getColumnListing('docs_transactions_contracts');
        $db_fields = array_unique(array_merge($db_fields_listing, $db_fields_contract));

        return view('/doc_management/resources/common_fields_html', compact('common_fields_groups', 'common_fields_sub_groups', 'db_fields'));
    }

    public function save_add_common_field(Request $request) {

		$field_name = $request -> field_name;
        $field_type = $request -> field_type;
        $group_id = $request -> group_id;
        $sub_group_id = $request -> sub_group_id;
        $db_column_name = $request -> db_column_name;

        $add_common_field = new CommonFields();
        $add_common_field -> field_name = $field_name;
        $add_common_field -> field_type = $field_type;
        $add_common_field -> group_id = $group_id;
        $add_common_field -> sub_group_id = $sub_group_id;
        $add_common_field -> db_column_name = $db_column_name;
        $add_common_field -> save();

        return response() -> json(['success' => true]);
    }

    public function save_edit_common_field(Request $request) {

		$id = $request -> id;
        $field_name = $request -> field_name;
        $field_type = $request -> field_type;
        $group_id = $request -> group_id;
        $sub_group_id = $request -> sub_group_id;
        $db_column_name = $request -> db_column_name;

        $common_field = CommonFields::find($id) -> update([
            'field_name' => $field_name,
            'field_type' => $field_type,
            'group_id' => $group_id,
            'sub_group_id' => $sub_group_id,
            'db_column_name' => $db_column_name,
        ]);
    }

    public function reorder_common_fields(Request $request) {

		$fields = json_decode($request -> fields, true);

        foreach ($fields as $field) {
            $field = CommonFields::find($field['field_id']) -> update(['field_order' => $field['order']]);
        }

        return response() -> json(['status' => 'success']);
    }

    public function resources() {
        $states = LocationData::ActiveStates();
        $resources_items_model = new ResourceItems();
        $resources = $resources_items_model -> groupBy('resource_type') -> get();
        $resources_items = $resources_items_model -> orderBy('resource_order') -> get();

        return view('/doc_management/resources/resources', compact('resources', 'resources_items', 'states', 'resources_items_model'));
    }

    public function resources_reorder(Request $request) {

		$data = json_decode($request['data'], true);
        $data = $data['resource'];

        foreach ($data as $item) {
            $resource_id = $item['resource_id'];
            $resource_order = $item['resource_index'];
            $reorder = ResourceItems::whereResourceId($resource_id) -> first();
            $reorder -> resource_order = $resource_order;
            $reorder -> save();
        }
    }

    public function resources_add(Request $request) {

		$resource_type = $request -> resource_type;
        $resource_type_title = ucwords(str_replace('_', ' ', $resource_type));
        $resource_name = $request -> resource_name;
        $resource_state = $request -> resource_state;
        $resource_color = $request -> resource_color;
        $resource_association = $request -> resource_association;
        $resource_addendums = $request -> resource_addendums;
        $resource_form_group_type = $request -> resource_form_group_type;
        $resource_county_abbr = $request -> resource_county_abbr;

        // get default values from existing
        // $defaults = ResourceItems::whereResourceType($resource_type) -> first();
        // $resource_type_title = $defaults -> resource_type_title;

        $resource_item = new ResourceItems();
        $resource_item -> resource_type = $resource_type;
        $resource_item -> resource_type_title = $resource_type_title;
        $resource_item -> resource_name = $resource_name;
        $resource_item -> resource_state = $resource_state;
        $resource_item -> resource_color = $resource_color;
        $resource_item -> resource_association = $resource_association;
        $resource_item -> resource_addendums = $resource_addendums;
        $resource_item -> resource_form_group_type = $resource_form_group_type;
        $resource_item -> resource_county_abbr = $resource_county_abbr;
        $resource_item -> resource_order = 0;
        $resource_item -> save();

        return $resource_item -> resource_id;
    }

    public function resources_edit(Request $request) {

		$resource_item = ResourceItems::whereResourceId($request -> resource_id) -> first();
        $resource_item -> resource_name = $request -> resource_name;
        $resource_item -> resource_state = $request -> resource_state;
        $resource_item -> resource_color = $request -> resource_color;
        $resource_item -> resource_association = $request -> resource_association;
        $resource_item -> resource_addendums = $request -> resource_addendums;
        $resource_item -> resource_form_group_type = $request -> resource_form_group_type;
        $resource_item -> resource_county_abbr = $request -> resource_county_abbr;
        $resource_item -> save();
    }

    public function delete_deactivate(Request $request) {

		if ($request -> action == 'delete') {
            $resource_item = ResourceItems::whereResourceId($request -> resource_id) -> delete();
        } elseif ($request -> action == 'deactivate') {
            $resource_item = ResourceItems::whereResourceId($request -> resource_id) -> first();
            $resource_item -> resource_active = 'no';
            $resource_item -> save();
        }
    }
}
