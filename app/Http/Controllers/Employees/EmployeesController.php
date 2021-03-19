<?php

namespace App\Http\Controllers\Employees;

use App\User;
use Illuminate\Http\Request;
use App\Models\Employees\Title;
use App\Models\Employees\InHouse;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Models\Employees\LoanOfficers;
use App\Models\Resources\LocationData;
use Illuminate\Support\Facades\Storage;
use App\Models\Employees\EmployeeImages;
use App\Models\Employees\TransactionCoordinators;

class EmployeesController extends Controller {

    public function employees(Request $request) {

        $states = LocationData::AllStates();

        return view('/employees/employees', compact('states'));

    }

    public function get_employees(Request $request) {

        $type = $request -> type;
        $active = $request -> active;

        $select = ['id', 'active', 'first_name', 'last_name', 'photo_location', 'email', 'cell_phone', 'address_street', 'address_unit', 'address_city', 'address_state', 'address_zip', 'emp_type', 'emp_position'];

        if($type == 'in_house') {
            $employees = InHouse::select($select) -> where('active', $active) -> orderBy('last_name', 'asc') -> get();
        } else if($type == 'transaction_coordinator') {
            $employees = TransactionCoordinators::select($select) -> where('active', $active) -> orderBy('last_name', 'asc') -> get();
        }

        return view('/employees/get_employees_html', compact('employees', 'type', 'active'));

    }

    public function save_employee(Request $request) {

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

        // update users table
        $user = User::where('email', $request -> email_orig)
            -> update([
                'active' => $request -> active,
                'name' => $request -> first_name.' '.$request -> last_name,
                'email' => $request -> email,
                'group' => $request -> emp_type
            ]);

        return response() -> json(['status' => 'success']);


    }

    public function save_cropped_upload(Request $request) {

        $file = $request -> file('cropped_image');
        $emp_id = $request -> emp_id;
        $employee = InHouse::find($emp_id);

        $filename = $employee -> first_name.'-'.$employee -> last_name.'.'.$file -> extension();
        $filename = time().'_'.$filename;

        $image_resize = Image::make($file -> getRealPath());
        $image_resize -> resize(300, 400);
        $image_resize -> save(Storage::disk('public') -> path('/employee_photos/'.$filename));

        //$save_file = $file -> storeAs('employee_photos/', $filename, 'public');
        $path = Storage::disk('public') -> url('/employee_photos/'.$filename);

        $employee -> update(['photo_location' => $path]);



        return response() -> json(['status' => 'success', 'path' => $path]);


    }

    public function delete_photo(Request $request) {

        $emp = InHouse::find($request -> emp_id) -> update([
            'photo_location' => ''
        ]);

    }

    public function docs_upload(Request $request) {

        dd($request -> all());

    }

}
