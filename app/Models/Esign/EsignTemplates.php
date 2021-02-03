<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EsignTemplates extends Model
{

    use SoftDeletes;

    public $table = 'esign_templates';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if(auth() -> user()) {
                if(auth() -> user() -> group == 'agent') {
                    $query -> where('User_ID', auth() -> user() -> id);
                } else if(auth() -> user() -> group == 'admin') {
                    $query -> where('User_ID', auth() -> user() -> id)
                        -> orWhere('is_system_template', 'yes');
                }
            }
        });
    }

    public function envelopes() {
        return $this -> hasMany('App\Models\Esign\EsignEnvelopes', 'template_id', 'id') -> with('documents') -> with('signers');
    }

    public function fields() {
        return $this -> hasMany('App\Models\Esign\EsignFields', 'template_id', 'id');
    }

    public function signers() {
        return $this -> hasMany('App\Models\Esign\EsignSigners', 'template_id', 'id');
    }


}
