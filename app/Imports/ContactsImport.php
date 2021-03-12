<?php

namespace App\Imports;

use App\Models\CRM\CRMContacts;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ContactsImport implements ToModel, WithStartRow {

    protected $Agent_ID;

    public function  __construct($Agent_ID) {
        $this -> Agent_ID = $Agent_ID;
    }

    public function startRow(): int {
        return 2;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new CRMContacts([

            'Agent_ID' => $this -> Agent_ID,
            'contact_first' => $row[0],
            'contact_last' => $row[1],
            'contact_company' => $row[2],
            'contact_phone_cell' => preg_replace('/(^1-|[\(\)\-\s\.]+)/', '', $row[3]),
            'contact_phone_home' => preg_replace('/(^1-|[\(\)\-\s\.]+)/', '', $row[4]),
            'contact_email' => $row[5],
            'contact_street' => $row[6],
            'contact_city' => $row[7],
            'contact_state' => $row[8],
            'contact_zip' => $row[9]

        ]);
    }
}
