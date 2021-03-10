<?php

namespace App\Http\Controllers\Admin\Resources;

use App\Http\Controllers\Controller;
use App\Models\Admin\Resources\ResourceItemsAdmin;
use App\Models\Resources\LocationData;
use Illuminate\Http\Request;

class ResourceItemsAdminController extends Controller
{
    public function resources_admin()
    {
        $states = LocationData::ActiveStates();

        $resources_items_model = new ResourceItemsAdmin();
        $resources = ResourceItemsAdmin::groupBy('resource_type') -> get();
        $resources_items = ResourceItemsAdmin::orderBy('resource_order') -> get();

        return view('/admin/resources/resources', compact('resources', 'resources_items', 'states', 'resources_items_model'));
    }

    public function resources_reorder(Request $request)
    {
        $data = json_decode($request['data'], true);
        $data = $data['resource'];

        foreach ($data as $item) {
            $resource_id = $item['resource_id'];
            $resource_order = $item['resource_index'];
            $reorder = ResourceItemsAdmin::whereResourceId($resource_id) -> first();
            $reorder -> resource_order = $resource_order;
            $reorder -> save();
        }
    }

    public function resources_add(Request $request)
    {
        $resource_type = $request -> resource_type;
        $resource_type_title = ucwords(str_replace('_', ' ', $resource_type));
        $resource_name = $request -> resource_name;
        $resource_state = $request -> resource_state;
        $resource_color = $request -> resource_color;

        // get default values from existing
        // $defaults = ResourceItemsAdmin::whereResourceType($resource_type) -> first();
        // $resource_type_title = $defaults -> resource_type_title;

        $resource_item = new ResourceItemsAdmin();
        $resource_item -> resource_type = $resource_type;
        $resource_item -> resource_type_title = $resource_type_title;
        $resource_item -> resource_name = $resource_name;
        $resource_item -> resource_state = $resource_state;
        $resource_item -> resource_color = $resource_color;
        $resource_item -> resource_order = 0;
        $resource_item -> save();

        return $resource_item -> resource_id;
    }

    public function resources_edit(Request $request)
    {
        $resource_item = ResourceItemsAdmin::whereResourceId($request -> resource_id) -> first();
        $resource_item -> resource_name = $request -> resource_name;
        $resource_item -> resource_state = $request -> resource_state;
        $resource_item -> resource_color = $request -> resource_color;
        $resource_item -> save();
    }

    public function delete_deactivate(Request $request)
    {
        if ($request -> action == 'delete') {
            $resource_item = ResourceItemsAdmin::whereResourceId($request -> resource_id) -> delete();
        } elseif ($request -> action == 'deactivate') {
            $resource_item = ResourceItemsAdmin::whereResourceId($request -> resource_id) -> first();
            $resource_item -> resource_active = 'no';
            $resource_item -> save();
        }
    }
}
