<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CRMContacts extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'crm_contacts';
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query -> where('user_id', auth() -> user() -> id);
        });
    }

    public function members() {
        return $this -> hasMany(\App\Models\DocManagement\Transactions\Members\Members::class, 'CRMContact_ID', 'id');
    }

}
