<?php

namespace App\Http\Controllers\Employees;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Employees\Title;
use App\Models\Employees\InHouse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Users\PasswordResets;
use Illuminate\Support\Facades\Hash;
use App\Models\Employees\InHouseDocs;
use Intervention\Image\Facades\Image;
use App\Models\Employees\LoanOfficers;
use App\Models\Resources\LocationData;
use App\Notifications\RegisterEmployee;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Models\Employees\TransactionCoordinators;
use App\Models\Employees\TransactionCoordinatorsDocs;

class EmployeesController extends Controller {

    public function employees(Request $request) {

        $states = LocationData::AllStates();

        return view('/employees/employees', compact('states'));

    }

    public function register_employee(Request $request) {

        $email = $request -> email;
        $user = User::where('email', $email) -> first();
        $url = $this -> create_password_reset_url($user, 'register');


        Notification::send($user, new RegisterEmployee($url));

    }

    public function create_password_reset_url($user, $action) {

        $token = str_random(60);
        PasswordResets::where('email', $user -> email) -> delete();
        PasswordResets::insert([
            'email' => $user -> email,
            'token' => Hash::make($token),
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $user -> email,
            'action' => $action
        ], false));

        return $url;

    }

    public function get_employees(Request $request) {

        $emp_type = $request -> emp_type;
        $active = $request -> active;

        $select = ['id', 'active', 'first_name', 'last_name', 'photo_location', 'email', 'cell_phone', 'address_street', 'address_unit', 'address_city', 'address_state', 'address_zip', 'emp_type', 'emp_position'];

        if($emp_type == 'in_house') {
            $employees = InHouse::select($select) -> where('active', $active) -> orderBy('last_name', 'asc') -> get();
        } else if($emp_type == 'transaction_coordinator') {
            $employees = TransactionCoordinators::select($select) -> where('active', $active) -> orderBy('last_name', 'asc') -> get();
        }

        return view('/employees/get_employees_html', compact('employees', 'emp_type', 'active'));

    }

    public function get_users(Request $request) {

        $users = User::where('active', 'yes') -> get();

        return view('/employees/get_users_html', compact('users'));

    }

    public function save_employee(Request $request) {

        if($request -> id == '') {
            $check_if_email_exists = User::where('email', $request -> email) -> count();
            if($check_if_email_exists > 0) {
                return response() -> json(['status' => 'error', 'message' => 'exists']);
            }
        }

        $add_employee = DB::transaction(function () use ($request) {

            if($request -> emp_type == 'transaction_coordinator') {
                $employee = TransactionCoordinators::firstOrCreate([
                    'id' => $request -> id
                ]);
            } else {
                $employee = InHouse::firstOrCreate([
                    'id' => $request -> id
                ]);
            }

            $ignore_cols = ['id', 'email_orig'];
            foreach($request -> all() as $key => $val) {
                if(!in_array($key, $ignore_cols)) {
                    $employee[$key] = $val;
                }
            }
            $employee -> save();

            $emp_id = $employee -> id;

            // update users table
            if($request -> email_orig != '') {
                $user = User::where('email', $request -> email_orig) -> first();

                $user -> update([
                    'active' => $request -> active,
                    'name' => $request -> first_name.' '.$request -> last_name,
                    'email' => $request -> email,
                    'group' => $request -> emp_type,
                    'user_id' => $emp_id
                ]);
            } else {
                $user = new User();
                $temp_pass = 'Akd'.time().'zlq70k30wj';
                $user -> password = Hash::make($temp_pass);
                $user -> active = 'yes';
                $user -> name = $request -> first_name.' '.$request -> last_name;
                $user -> email = $request -> email;
                $user -> group = $request -> emp_type;
                $user -> user_id = $emp_id;
                $user -> save();
            }

            return $emp_id;

        });

        return response() -> json(['status' => 'success', 'emp_id' => $add_employee]);

    }

    public function save_cropped_upload(Request $request) {

        $file = $request -> file('cropped_image');
        $emp_id = $request -> emp_id;
        $emp_type = $request -> emp_type;

        if($emp_type == 'in_house') {
            $employee = InHouse::find($emp_id);
        } else if($emp_type == 'transaction_coordinator') {
            $employee = TransactionCoordinators::find($emp_id);
        }

        $filename = $employee -> first_name.'-'.$employee -> last_name.'.'.$file -> extension();
        $filename = time().'_'.$filename;

        $image_resize = Image::make($file -> getRealPath());
        $image_resize -> resize(300, 400);
        $image_resize -> save(Storage::disk('public') -> path('/employee_photos/'.$filename));


        $path = '/storage/employee_photos/'.$filename;

        $employee -> update(['photo_location' => $path]);

        $user = User::where('email', $employee -> email) -> first();
        $user -> photo_location = $path;
        $user -> save();

        return response() -> json(['status' => 'success', 'path' => $path]);


    }

    public function delete_photo(Request $request) {

        $emp_id = $request -> emp_id;
        $emp_type = $request -> emp_type;

        if($emp_type == 'in_house') {
            $emp = InHouse::find($emp_id);
        } else if($emp_type == 'transaction_coordinator') {
            $emp = TransactionCoordinators::find($emp_id);
        }

        $user = User::where('email', $emp -> email) -> first() -> update([
            'photo_location' => ''
        ]);

        Storage::disk('public') -> delete(str_replace('/storage/', '', $emp -> photo_location));
        $emp -> update([
            'photo_location' => ''
        ]);



    }

    public function docs_upload(Request $request) {

        $file = $request -> file('agent_docs_file');
        $emp_id = $request -> emp_id;
        $emp_type = $request -> emp_type;

        $file_name = $file -> getClientOriginalName();
        $ext = $file -> extension();
        $file_name = preg_replace('/\.'.$ext.'/i', '', $file_name);
        $file_name = time().'_'.sanitize($file_name).'.'.$ext;
        $file -> storeAs('employee_docs/', $file_name, 'public');
        $file_location = Storage::disk('public') -> url('/employee_docs/'.$file_name);
        $file_location = str_replace(config('app.url'), '', $file_location);

        if($emp_type == 'in_house') {
            $add_file = InHouseDocs::create([
                'emp_in_house_id' => $emp_id,
                'file_name' => $file_name,
                'file_location' => $file_location
            ]);
        } else if($emp_type == 'transaction_coordinator') {
            $add_file = TransactionCoordinatorsDocs::create([
                'emp_transaction_coordinators_id' => $emp_id,
                'file_name' => $file_name,
                'file_location' => $file_location
            ]);
        }



    }

    public function get_docs(Request $request) {

        $emp_id = $request -> emp_id;
        $emp_type = $request -> emp_type;

        if($emp_type == 'in_house') {
            $docs = InHouseDocs::where('emp_in_house_id', $emp_id) -> orderBy('created_at', 'desc') -> get();
        } else if($emp_type == 'transaction_coordinator') {
            $docs = TransactionCoordinatorsDocs::where('emp_transaction_coordinators_id', $emp_id) -> orderBy('created_at', 'desc') -> get();
        }

        return view('/employees/get_employee_docs_html', compact('docs'));

    }

    public function delete_doc(Request $request) {

        $doc_id = $request -> doc_id;
        $emp_type = $request -> emp_type;
        $active = $request -> active;

        if($emp_type == 'in_house') {
            $doc_delete = InHouseDocs::find($doc_id) -> update(['active' => $active]);
        } else if($emp_type == 'transaction_coordinator') {
            $doc_delete = TransactionCoordinatorsDocs::find($doc_id) -> update(['active' => $active]);
        }

        return response() -> json(['status' => 'success']);

    }


}
