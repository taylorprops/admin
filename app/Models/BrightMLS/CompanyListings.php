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

    public function agent() {
        return $this -> hasOne(\App\Models\Employees\Agents::class, 'id', 'Agent_ID');
    }

    public function notes() {
        return $this -> hasMany(\App\Models\BrightMLS\CompanyListingsNotes::class, 'ListingKey', 'ListingKey');
    }

}
