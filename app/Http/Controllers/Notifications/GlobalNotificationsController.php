<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GlobalNotificationsController extends Controller
{

    public function get_notifications(Request $request) {

        if(auth() -> user()) {
            // notifications
            $notifications = auth() -> user() -> unreadNotifications;
            $read_notifications = auth() -> user() -> readNotifications -> where('created_at', '>', date('Y-m-d', strtotime('-1 month')));

            return view('/notifications/get_notifications_html', compact('notifications', 'read_notifications'));
        } else {
            return response() -> json(['status' => 'error']);
        }

    }

    public function mark_as_read(Request $request) {

        $id = $request -> id;
        $mark = $request -> mark;

        if($id != '0') {
            if($mark == 'read') {
                auth() -> user()
                    -> unreadNotifications
                    -> where('id', $id)
                    -> markAsRead();
            } else if($mark == 'unread') {
                auth() -> user()
                    -> readNotifications
                    -> where('id', $id)
                    -> markAsUnread();
            }
        } else {
            auth() -> user()
                -> unreadNotifications
                -> markAsRead();
        }

        return response() -> noContent();

    }

}
