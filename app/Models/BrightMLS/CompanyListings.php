<?php

namespace App\Models\BrightMLS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyListings extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'company_listings';
    protected $primaryKey = 'ListingKey';
    protected $guarded = [];

}
