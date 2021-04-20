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

        $bug_reports = BugReports::where('active', 'yes') -> with(['user']) -> get();

        return view('/bug_reports/bug_reports', compact('bug_reports'));

    }

    public function view_bug_report(Request $request) {

        $id = $request -> id;
        $bug_report = BugReports::find($id);

        return view('/bug_reports/view_bug_report', compact('bug_report'));

    }

    public function submit_bug_report(Request $request) {

        $user_id = auth() -> user() -> id;
        $user_message = $request -> message;
        $url = $request -> url;
        $image = $request -> image;

        $browser = [
            'userAgent' => Browser::userAgent(),
            'isMobile' => Browser::isMobile(),
            'isTablet' => Browser::isTablet(),
            'isDesktop' => Browser::isDesktop(),
            'browserName' => Browser::browserName(),
            'browserVersion' => Browser::browserVersion(),
            'platformName' => Browser::platformName(),
            'platformFamily' => Browser::platformFamily(),
            'platformVersion' => Browser::platformVersion()
        ];

        $browser = json_encode($browser);

        $report = new BugReports;
        $report -> user_id = $user_id;
        $report -> message = $user_message;
        $report -> url = $url;
        $report -> image = $image;
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
