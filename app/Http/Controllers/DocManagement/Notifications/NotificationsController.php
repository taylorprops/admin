<?php

namespace App\Http\Controllers\DocManagement\Notifications;

use Illuminate\Http\Request;
use App\Models\Config\Config;
use App\Models\Employees\InHouse;
use App\Http\Controllers\Controller;

class NotificationsController extends Controller {

    public function notifications(Request $request) {

		$categories = Config::where('config_role', 'notification_documents') -> orderBy('category') -> groupBy('category') -> pluck('category');
        $config_options = Config::where('config_role', 'notification_documents') -> orderBy('category') -> orderBy('order') -> get();
        $in_house_employees = InHouse::orderBy('emp_type') -> get();

        return view('doc_management/notifications/notifications', compact('categories', 'config_options', 'in_house_employees'));

    }

    public function reorder_notifications(Request $request) {

		$items = json_decode($request -> items, true);

        foreach ($items as $item) {
            $item = Config::find($item['config_id']) -> update(['order' => $item['order']]);
        }

        return response() -> json(['status' => 'success']);

    }

    public function save_notifications(Request $request) {

		$config_id = $request -> config_id;
        $title = $request -> title;
        $description = $request -> description;
        $emails = $request -> emails ?? null;
        $number = $request -> number ?? null;
        $on_off = $request -> on_off ?? null;

        $config_value = $emails;
        if($number > 0) {
            $config_value = $number;
        }
        if($on_off != '') {
            $config_value = $on_off;
        }

        $config = Config::find($config_id) -> update([
            'title' => $title,
            'description' => $description,
            'config_value' => $config_value
        ]);

        return response() -> json(['status' => 'success']);

    }

}
