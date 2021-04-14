<?php

namespace App\Models\BrightMLS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBrightOffices extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'company_bright_offices';
    protected $primaryKey = 'id';
    protected $guarded = [];

}
