<?php

namespace App\Http\Controllers\BugReports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BugReports\BugReports;
use Illuminate\Support\Facades\Storage;

class BugReportsController extends Controller
{

    public function bug_report(Request $request) {

        $user_id = auth() -> user() -> id;
        $message = $request -> message;
        $url = $request -> url;
        $image = $request -> image;

        BugReports::create([
            'user_id' => $user_id,
            'message' => $message,
            'url' => $url,
            'image' => $image
        ]);

        return response() -> json(['status' => 'success']);

    }
}
