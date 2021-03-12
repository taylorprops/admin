<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;
use App\Imports\ContactsImport;
use App\Models\CRM\CRMContacts;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Resources\LocationData;

class ContactsController extends Controller {

    public function contacts(Request $request) {

        $states = LocationData::AllStates();

        return view('/CRM/contacts', compact('states'));

    }

    public function get_contacts(Request $request) {

        $contacts = CRMContacts::where('contact_active', 'yes') -> with('members') -> get();

        return view('/CRM/get_contacts_html', compact('contacts'));

    }

    public function delete(Request $request) {

        $contact_ids = explode(',', $request -> contact_ids);
        $delete_contacts = CRMContacts::whereIn('id', $contact_ids) -> update(['contact_active' => 'no']);

        return response() -> json(['status' => 'success']);

    }

    public function save(Request $request) {

        $contact = CRMContacts::firstOrCreate([
            'id' => $request -> contact_id
        ]);

        foreach($request -> all() as $key => $val) {
            if($key != 'contact_id') {
                $contact[$key] = $val;
            }
        }
        $contact -> save();

    }

    public function import_from_excel(Request $request) {

        $Agent_ID = auth() -> user() -> user_id;
        $import = Excel::import(new ContactsImport($Agent_ID), request() -> file('contacts_file'));
        dump($import);

    }

}
