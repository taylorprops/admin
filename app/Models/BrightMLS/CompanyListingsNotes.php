<?php

namespace App\Models\BrightMLS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyListingsNotes extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'company_listings_notes';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function user() {
        return $this -> hasOne(\App\User::class, 'id', 'user_id');
    }

}
