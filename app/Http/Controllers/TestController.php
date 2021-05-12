<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Tasks\Tasks;
use Illuminate\Http\Request;
use App\Models\Employees\Agents;
use App\Models\Calendar\Calendar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\BrightMLS\CompanyListings;
use App\Notifications\GlobalNotification;
use Illuminate\Support\Facades\Notification;


class TestController extends Controller
{
    public function test(Request $request) {

        //return view('tests/test');

        $file_location = str_replace(Storage::path(''), '/storage/', '/var/www/admin/storage/app/public/test_folder/filename.pdf');
        //$file_location = str_replace('/storage/app/public', '/storage', $file_location);
        dd($file_location);

    }



}
