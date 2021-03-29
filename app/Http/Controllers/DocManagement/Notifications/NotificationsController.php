<?php

namespace App\Http\Controllers\DocManagement\Notifications;


use Illuminate\Http\Request;
use App\Models\Config\Config;
use App\Models\Employees\InHouse;
use App\Http\Controllers\Controller;

class NotificationsController extends Controller {

    public function notifications(Request $request) {

		$categories = Config::where('config_role', 'notification') -> orderBy('category') -> groupBy('category') -> pluck('category');
        $config_options = Config::whereIn('config_role', ['setting', 'notification']) -> orderBy('category') -> orderBy('order') -> get();
        $in_house_employees = InHouse::with(['user_account:id,user_id,email']) -> orderBy('emp_type') -> get();


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
        $notify_by_email = $request -> notify_by_email ?? null;
        $notify_by_text = $request -> notify_by_text ?? null;
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
            'config_value' => $config_value,
            'notify_by_email' => $notify_by_email,
            'notify_by_text' => $notify_by_text
        ]);

        return response() -> json(['status' => 'success']);

    }

}
