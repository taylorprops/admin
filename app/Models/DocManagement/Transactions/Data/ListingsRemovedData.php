<?php

namespace App\Models\DocManagement\Transactions\Data;

use Illuminate\Database\Eloquent\Model;

class ListingsRemovedData extends Model
{
    protected $connection = 'mysql_taylorproperties';
    public $table = 'listings_removed';
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query->where('CloseDate', '>', date('Y-m-d', strtotime('-6 month')))->where('MlsStatus', 'Closed')->whereNotNull('CloseDate')->where('CloseDate', '!=', '0000-00-00');
        });
    }

    public function scopeListingSearch($query, $state, $zip, $street_number, $street_name, $unit, $street_dir_suffix, $street_dir_suffix_alt)
    {
        $listings = $query->where('StateOrProvince', $state)->where('PostalCode', $zip)
        ->where('StreetNumber', $street_number)
        ->where('StreetName', 'LIKE', $street_name.'%')
        ->where('UnitNumber', 'LIKE', '%'.$unit.'%')
        ->whereRaw('((StreetDirSuffix = \''.$street_dir_suffix.'\' or StreetDirSuffix = \''.$street_dir_suffix_alt.'\') or (StreetDirPrefix = \''.$street_dir_suffix.'\' or StreetDirPrefix = \''.$street_dir_suffix_alt.'\'))')
        ->orderBy('MLSListDate', 'DESC')
        ->get()->toArray();

        return $listings;
    }
}
