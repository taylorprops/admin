<?php

namespace App\Models\Resources;

use Illuminate\Database\Eloquent\Model;

class LocationData extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_zips';
    protected $guarded = [];

    public function scopeActiveStates() {
        return config('global.active_states');
    }

    public function scopeAllStates() {
        return self::select('state') -> groupBy('state') -> orderBy('state') -> get();
    }

    public function scopeGetStateName($query, $state_abbr) {
        return self::select('state_name') -> where('state', $state_abbr) -> first();
    }

    public function scopeCounties() {
        return self::select('county', 'state') -> whereIn('state', config('global.active_states')) -> orderBy('state') -> orderBy('county') -> groupBy('state') -> groupBy('county') -> get() -> toArray();
    }

    public function scopeCountiesByState($query, $state) {
        return self::select('county') -> where('state', $state) -> groupBy('county') -> get();
    }

    public function scopeCities() {
        return self::select('city', 'state') -> whereIn('state', config('global.active_states')) -> orderBy('state') -> orderBy('city') -> groupBy('state') -> groupBy('city') -> get() -> toArray();
    }
}
