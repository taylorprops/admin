<?php

namespace App\Http\Controllers\BugReports;

use App\User;
use Browser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BugReports\BugReports;
use Illuminate\Support\Facades\Storage;
use App\Notifications\GlobalNotification;
use Illuminate\Support\Facades\Notification;

class BugReportsController extends Controller
{

    public function bug_reports(Request $request) {

        $bug_reports = BugReports::with(['user']) -> orderBy('active', 'desc') -> get();

        return view('/bug_reports/bug_reports', compact('bug_reports'));

    }

    public function view_bug_report(Request $request) {

        $id = $request -> id;
        $bug_report = BugReports::with(['user']) -> find($id);

        $browser_info = (object)  json_decode($bug_report -> browser_info, true);

        return view('/bug_reports/view_bug_report', compact('bug_report', 'browser_info'));

    }

    public function mark_resolved(Request $request) {

        $id = $request -> id;
        $action = $request -> action;

        BugReports::find($id) -> update([
            'active' => $action
        ]);

        return response() -> json(['status' => 'success']);

    }

    public function submit_bug_report(Request $request) {

        $user_id = auth() -> user() -> id;
        $user_message = $request -> message;
        $url = $request -> url;
        $image = $request -> image;

        $image = $request -> file('image');

        $image_name = $image -> getClientOriginalName();
        $ext = $image -> extension();
        $image_name = preg_replace('/\.'.$ext.'/i', '', $image_name);
        $image_name = time().'_'.sanitize($image_name).'.'.$ext;

        $image -> storeAs('bug_reports/', $image_name, 'public');
        $image_location = '/storage/bug_reports/'.$image_name;

        $device = [
            Browser::isMobile() => 'mobile',
            Browser::isTablet() => 'tablet',
            Browser::isDesktop() => 'desktop'
        ][1];

        $browser = [
            'userAgent' => Browser::userAgent(),
            'Device' => $device,
            'Browser Name' => Browser::browserName(),
            'Browser Version' => Browser::browserVersion(),
            'Platform Name' => Browser::platformName(),
            'Platform Family' => Browser::platformFamily(),
            'Platform Version' => Browser::platformVersion()
        ];

        $browser = json_encode($browser);

        $report = new BugReports;
        $report -> user_id = $user_id;
        $report -> message = $user_message;
        $report -> url = $url;
        $report -> image_location = $image_location;
        $report -> browser_info = $browser;
        $report -> save();
        $report_id = $report -> id;

        $notification = config('notifications.admin_bug_report');
        $users = User::whereIn('email', $notification['emails']) -> get();

        $subject = 'Bug Report Submitted by '.auth() -> user() -> name;
        $message = 'Bug Report Submitted by '.auth() -> user() -> name;
        $message_email = '
        <div style="font-size: 15px;">
        Bug Report Submitted by '.auth() -> user() -> name.'
        <br><br>
        '.$user_message.'
        <br><br>
        '.$url.'
        </div>';

        $notification['type'] = 'admin';
        $notification['sub_type'] = 'bug_report';
        $notification['sub_type_id'] = $report_id;
        $notification['subject'] = $subject;
        $notification['message'] = $message;
        $notification['message_email'] = $message_email;

        Notification::send($users, new GlobalNotification($notification));

        return response() -> json(['status' => 'success']);

    }
}
