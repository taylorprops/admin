<?php

namespace App\Models\BrightMLS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyListings extends Model
{
    use HasFactory;

    use \Awobaz\Compoships\Compoships;

    protected $connection = 'mysql';
    protected $table = 'company_listings';
    protected $primaryKey = 'ListingKey';
    protected $guarded = [];

    public function agent() {
        return $this -> hasOne(\App\Models\Employees\Agents::class, 'id', 'Agent_ID');
    }

}
