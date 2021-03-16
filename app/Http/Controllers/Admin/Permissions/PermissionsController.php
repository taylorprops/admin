<?php

namespace App\Http\Controllers\Admin\Permissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Config\Config;
use App\Models\Employees\InHouse;

class PermissionsController extends Controller {

    public function permissions(Request $request) {

		$categories = Config::where('config_role', 'permissions') -> orderBy('category') -> groupBy('category') -> pluck('category');
        $config_options = Config::where('config_role', 'permissions') -> orderBy('category') -> orderBy('order') -> get();
        $in_house_employees = InHouse::orderBy('emp_type') -> get();

        return view('admin/permissions/permissions', compact('categories', 'config_options', 'in_house_employees'));

    }

    public function reorder_permissions(Request $request) {

		$items = json_decode($request -> items, true);

        foreach ($items as $item) {
            $item = Config::find($item['config_id']) -> update(['order' => $item['order']]);
        }

        return response() -> json(['status' => 'success']);

    }

    public function save_permissions(Request $request) {

		$config_id = $request -> config_id;
        $title = $request -> title;
        $description = $request -> description;
        $emails = $request -> emails;

        $config_value = $emails;

        $config = Config::find($config_id) -> update([
            'title' => $title,
            'description' => $description,
            'config_value' => $config_value
        ]);

        return response() -> json(['status' => 'success']);

    }

}
