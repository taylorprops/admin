<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EsignEnvelopes extends Model
{
    use SoftDeletes;

    public $table = 'esign_envelopes';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if (auth()->user()) {
                if (stristr(auth()->user()->group, 'agent')) {
                    $query->where(function ($query) {
                        $query->where('Agent_ID', auth()->user()->user_id)
                        ->orWhere('User_ID', auth()->user()->id);
                    });
                } elseif (auth()->user()->group == 'admin') {
                    $query->where('User_ID', auth()->user()->id)
                        ->orWhere('is_system_template', 'yes')
                        ->orWhere('is_system_template', 'yes');
                }
            }
        });
    }

    public function ScopeGetEsignDetails($request, $transaction_type, $id)
    {
        if (is_array($id)) {
            $id = max($id);
        }
        $esign = null;
        if ($transaction_type == 'listing') {
            $esign = $this->where('Listing_ID', $id)->first();
        } elseif ($transaction_type == 'contract') {
            $esign = $this->where('Contract_ID', $id)->first();
        } elseif ($transaction_type == 'referral') {
            $esign = $this->where('Referral_ID', $id)->first();
        }

        return $esign;
    }

    public function documents()
    {
        return $this->hasMany('App\Models\Esign\EsignDocuments', 'envelope_id', 'id');
    }

    public function signers()
    {
        return $this->hasMany('App\Models\Esign\EsignSigners', 'envelope_id', 'id');
    }

    public function fields()
    {
        return $this->hasMany('App\Models\Esign\EsignFields', 'envelope_id', 'id');
    }

    public function callbacks()
    {
        return $this->hasMany('App\Models\Esign\EsignCallbacks', 'related_document_hash', 'document_hash');
    }

    public function listing()
    {
        return $this->hasOne('App\Models\DocManagement\Transactions\Listings\Listings', 'Listing_ID', 'Listing_ID')->where('Listing_ID', '>', 0);
    }

    public function contract()
    {
        return $this->hasOne('App\Models\DocManagement\Transactions\Contracts\Contracts', 'Contract_ID', 'Contract_ID')->where('Contract_ID', '>', 0);
    }

    public function referral()
    {
        return $this->hasOne('App\Models\DocManagement\Transactions\Referrals\Referrals', 'Referral_ID', 'Referral_ID')->where('Referral_ID', '>', 0);
    }
}
