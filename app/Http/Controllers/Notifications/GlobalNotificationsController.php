<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GlobalNotificationsController extends Controller
{

    public function get_notifications(Request $request) {

        // notifications
        $notifications = auth() -> user() -> unreadNotifications;
        $read_notifications = auth() -> user() -> readNotifications -> where('created_at', '>', date('Y-m-d', strtotime('-1 month')));

        return view('/notifications/get_notifications_html', compact('notifications', 'read_notifications'));

    }

    public function mark_as_read(Request $request) {

        $id = $request -> id;

        if($id != '0') {
            auth() -> user()
                -> unreadNotifications
                -> where('id', $id)
                -> markAsRead();
        } else {
            auth() -> user()
                -> unreadNotifications
                -> markAsRead();
        }

        return response() -> noContent();

    }

}
