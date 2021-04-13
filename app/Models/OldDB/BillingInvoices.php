<?php

namespace App\Models\OldDB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingInvoices extends Model
{
    use HasFactory;

    protected $connection = 'mysql_company';
    protected $table = 'billing_invoices';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

}
