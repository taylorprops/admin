<?php

namespace App\Models\OldDB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingInvoicesItems extends Model
{
    use HasFactory;

    protected $connection = 'mysql_company';
    protected $table = 'billing_invoices_items';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

}
