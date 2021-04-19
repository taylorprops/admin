<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;
use App\Models\Employees\Agents;
use App\Models\Employees\InHouse;
use Intervention\Image\Facades\Image;
use App\Models\Employees\LoanOfficers;
use Illuminate\Support\Facades\Storage;
use App\Models\Employees\TransactionCoordinators;

class UserController extends Controller
{
    public function __construct() {
        $this -> middleware('auth');
    }

    public function get_users(Request $request) {

        $users = User::where('active', 'yes') -> get();

        return view('/users/users', compact('users'));

    }

    public function user_profile(Request $request) {

        $user = User::select('first_name', 'last_name', 'email', 'signature', 'photo_location') -> find(auth() -> user() -> id);

        return view('/users/user_profile', compact('user'));

    }

    public function save_profile(Request $request) {

        $user = User::find(auth() -> user() -> id) -> update([
            'signature' => $request -> signature
        ]);

        return response() -> json(['status' => 'success']);

    }

    public function save_cropped_upload(Request $request) {

        $file = $request -> file('cropped_image');

        $user = User::find(auth() -> user() -> id);

        if($user -> group == 'admin') {
            $employee = InHouse::where('email', $user -> email) -> first();
        } else if($user -> group == 'transaction_coordinator') {
            $employee = TransactionCoordinators::where('email', $user -> email) -> first();
        } else if(stristr($user -> group, 'agent')) {
            $employee = Agents::where('email', $user -> email) -> first();
        } else if($user -> group == 'loan_officer') {
            $employee = LoanOfficers::where('email', $user -> email) -> first();
        }
dd($employee);
        $filename = $employee -> first_name.'-'.$employee -> last_name.'.'.$file -> extension();
        $filename = time().'_'.$filename;

        $image_resize = Image::make($file -> getRealPath());
        $image_resize -> resize(300, 400);
        $image_resize -> save(Storage::disk('public') -> path('/employee_photos/'.$filename));


        $path = '/storage/employee_photos/'.$filename;

        $employee -> update(['photo_location' => $path]);

        $user -> photo_location = $path;
        $user -> save();

        return response() -> json(['status' => 'success', 'path' => $path]);


    }

    public function delete_photo(Request $request) {


        $user = User::find(auth() -> user() -> id);
        $user -> update([
            'photo_location' => ''
        ]);


        if(auth() -> user() -> group == 'admin') {
            $employee = InHouse::where('email', $user -> email) -> first();
        } else if(auth() -> user() -> group == 'transaction_coordinator') {
            $employee = TransactionCoordinators::where('email', $user -> email) -> first();
        }

        Storage::disk('public') -> delete(str_replace('/storage/', '', $employee -> photo_location));
        $employee -> update([
            'photo_location' => ''
        ]);



    }

}
