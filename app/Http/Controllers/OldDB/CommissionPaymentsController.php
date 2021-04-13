<?php

namespace App\Http\Controllers\OldDB;

use Illuminate\Http\Request;
use App\Models\OldDB\OldAgents;
use App\Models\Employees\Agents;
use App\Http\Controllers\Controller;
use App\Models\OldDB\BillingInvoices;
use App\Models\OldDB\BillingInvoicesItems;
use App\Models\DocManagement\Transactions\Contracts\Contracts;

class CommissionPaymentsController extends Controller
{

    public function make_payment_from_commission(Request $request) {

        $Agent_ID = $request -> Agent_ID;
        $Contract_ID = $request -> Contract_ID;
        $payments = json_decode($request -> payments, true);
        $paid_with = 'commission';

        $contract = Contracts::find($Contract_ID);
        $address = $contract -> FullStreetAddress.' '.$contract -> City.', '.$contract -> StateOrProvince.' '.$contract -> PostalCode;

        $agent = Agents::find($Agent_ID);
        $old_agent = OldAgents::find($Agent_ID);

        $class_dues = '';
        $class_eno = '';

        foreach($payments as $payment) {

            $description = $payment['description'];
            $amount = preg_replace('/[,\$]+/', '', $payment['amount']);
            $type = $payment['type'];

            $invoice = new BillingInvoices();
            $invoice -> Contract_ID = $Contract_ID;
            $invoice -> in_agent_id = $Agent_ID;
            $invoice -> in_amount = $amount;
            $invoice -> in_date_sent = date('Y-m-d');
            $invoice -> in_agent_fullname = $agent -> full_name;
            $invoice -> in_agent_email = $agent -> email;
            $invoice -> in_paid = 'yes';
            $invoice -> in_notes = 'Commission from '.$address;
            $invoice -> in_company = $agent -> company;
            $invoice -> in_date_paid = date('Y-m-d');
            $invoice -> payment_type = $paid_with;
            $invoice -> py_desc = 'Commission from '.$address;
            $invoice -> prop_address = $address;
            $invoice -> created_by = auth() -> user() -> name;

            if($type == 'dues') {

                $prev_balance = $old_agent -> balance;
                $new_balance = $prev_balance - $amount;

                $invoice -> in_agent_balance = $new_balance;
                $invoice -> in_agent_prev_balance = $prev_balance;
                $invoice -> in_type = 'payment';

                $old_agent -> update([
                    'balance' => $new_balance
                ]);

                $class_dues = 'text-success';

            } else if($type == 'eno') {

                $prev_balance_eno = $old_agent -> balance_eno;
                $new_balance_eno = $prev_balance_eno - $amount;

                $invoice -> in_agent_balance_eno = $new_balance_eno;
                $invoice -> in_agent_prev_balance_eno = $prev_balance_eno;
                $invoice -> in_type = 'paymentEno';

                $old_agent -> update([
                    'balance_eno' => $new_balance_eno
                ]);

                $class_eno = 'text-success';

            }
            $invoice -> save();
            $invoice_id = $invoice -> id;


            $invoice_items = new BillingInvoicesItems();

            $invoice_items -> in_item_quantity = 1;
            $invoice_items -> in_item_desc = $description;
            $invoice_items -> in_item_amount = $amount;
            $invoice_items ->  in_item_total = $amount;
            $invoice_items -> in_invoice_id = $invoice_id;
            $invoice_items -> in_item_agent_id = $Agent_ID;
            $invoice_items -> in_item_agent = $agent -> full_name;
            $invoice_items -> save();

        }

        $balances = '
        <table class="table table-hover table-sm">
            <thead>
                <th></th>
                <th>Previous Balance</th>
                <th>Current Balance</th>
            </thead>
            <tbody>
                <tr>
                    <td>Dues</td>
                    <td>$'.number_format($prev_balance, 2).'</td>
                    <td class="'.$class_dues.'">$'.number_format($new_balance, 2).'</td>
                </tr>
                <tr>
                    <td>E&O</td>
                    <td>$'.number_format($prev_balance_eno, 2).'</td>
                    <td class="'.$class_eno.'">$'.number_format($new_balance_eno, 2).'</td>
                </tr>
            </tbody>
        </table>
        ';


        return compact('balances');


    }

}
