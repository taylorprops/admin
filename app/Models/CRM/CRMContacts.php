<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CRMContacts extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    public $table = 'crm_contacts';
    protected $guarded = [];

    /* public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if(config('app.env') == 'development') {
                if(auth() -> user() -> group != 'admin') {
                    $query -> where('Agent_ID', auth() -> user() -> user_id);
                }
            } else {
                $query -> where('Agent_ID', auth() -> user() -> user_id);
            }
        });
    } */

    public function members() {
        return $this -> hasMany(\App\Models\DocManagement\Transactions\Members\Members::class, 'CRMContact_ID', 'id');
    }

}
