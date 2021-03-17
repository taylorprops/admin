<?php

namespace App\Http\Controllers\Employees;

use Illuminate\Http\Request;
use App\Models\Employees\InHouse;
use App\Http\Controllers\Controller;
use App\Models\Resources\LocationData;
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
        } else if($type == 'transaction_coordinators') {
            $employees = TransactionCoordinators::select($select) -> where('active', $active) -> orderBy('last_name', 'asc') -> get();
        }

        return view('/employees/get_employees_html', compact('employees', 'type', 'active'));

    }

    public function save_employee(Request $request) {

        if($request -> emp_type == 'transaction_coordinators') {
            $employee = TransactionCoordinators::firstOrCreate([
                'id' => $request -> id
            ]);
        } else {
            $employee = InHouse::firstOrCreate([
                'id' => $request -> id
            ]);
        }

        foreach($request -> all() as $key => $val) {
            if($key != 'id') {
                $employee[$key] = $val;
            }
        }
        $employee -> save();

        return response() -> json(['status' => 'success']);


    }

}
