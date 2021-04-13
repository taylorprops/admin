<?php

namespace App\Models\OldDB;

use App\Models\OldDB\OldEarnest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OldEarnest extends Model
{
    protected $connection = 'mysql_company';
    protected $table = 'escrow';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    public function ScopeEarnestBalances($query) {
        $earnest = [];

        $tp_md = self::select(
            DB::raw("
        SUM(CASE
            WHEN ck1_in_cleared = 'yes'
                THEN ck1_in_amount
                ELSE 0
            END) AS check1_in_cleared,
        SUM(CASE
            WHEN ck2_in_cleared = 'yes'
                THEN ck2_in_amount
                ELSE 0
            END) AS check2_in_cleared,
        SUM(CASE
            WHEN ck3_in_cleared = 'yes'
                THEN ck3_in_amount
                ELSE 0
            END) AS check3_in_cleared,
        SUM(CASE
            WHEN ck1_out_cleared = 'yes'
                THEN ck1_out_amount
                ELSE 0
            END) AS check1_out_cleared,
        SUM(CASE
            WHEN ck2_out_cleared = 'yes'
                THEN ck2_out_amount
                ELSE 0
            END) AS check2_out_cleared,
        SUM(CASE
            WHEN ck3_out_cleared = 'yes'
                THEN ck3_out_amount
                ELSE 0
            END) AS check3_out_cleared"))
        -> whereRaw("(transfer2_state = 'MD' or (transfer_state = 'MD' and (transfer2_state = '' or transfer2_state is null)) or (state = 'MD' and (transfer_state = '' or transfer_state is null) and (transfer2_state = '' or transfer2_state is null))) and company = 'Taylor Properties'")
        -> get();

        $tp_md = $tp_md[0];

        $tp_md_total = ($tp_md['check1_in_cleared'] + $tp_md['check2_in_cleared'] + $tp_md['check3_in_cleared']) - ($tp_md['check1_out_cleared'] + $tp_md['check2_out_cleared'] + $tp_md['check3_out_cleared']);

        $earnest['TP_MD'] = $tp_md_total;

        $tp_va = self::select(
            DB::raw("
        SUM(CASE
            WHEN ck1_in_cleared = 'yes'
                THEN ck1_in_amount
                ELSE 0
            END) AS check1_in_cleared,
        SUM(CASE
            WHEN ck2_in_cleared = 'yes'
                THEN ck2_in_amount
                ELSE 0
            END) AS check2_in_cleared,
        SUM(CASE
            WHEN ck3_in_cleared = 'yes'
                THEN ck3_in_amount
                ELSE 0
            END) AS check3_in_cleared,
        SUM(CASE
            WHEN ck1_out_cleared = 'yes'
                THEN ck1_out_amount
                ELSE 0
            END) AS check1_out_cleared,
        SUM(CASE
            WHEN ck2_out_cleared = 'yes'
                THEN ck2_out_amount
                ELSE 0
            END) AS check2_out_cleared,
        SUM(CASE
            WHEN ck3_out_cleared = 'yes'
                THEN ck3_out_amount
                ELSE 0
            END) AS check3_out_cleared"))
        -> whereRaw("(transfer2_state = 'VA' or (transfer_state = 'VA' and (transfer2_state = '' or transfer2_state is null)) or (state = 'VA' and (transfer_state = '' or transfer_state is null) and (transfer2_state = '' or transfer2_state is null))) and company = 'Taylor Properties'")
        -> get();

        $tp_va = $tp_va[0];

        $tp_va_total = ($tp_va['check1_in_cleared'] + $tp_va['check2_in_cleared'] + $tp_va['check3_in_cleared']) - ($tp_va['check1_out_cleared'] + $tp_va['check2_out_cleared'] + $tp_va['check3_out_cleared']);

        $earnest['TP_VA'] = $tp_va_total;

        $tp_pa = self::select(
            DB::raw("
        SUM(CASE
            WHEN ck1_in_cleared = 'yes'
                THEN ck1_in_amount
                ELSE 0
            END) AS check1_in_cleared,
        SUM(CASE
            WHEN ck2_in_cleared = 'yes'
                THEN ck2_in_amount
                ELSE 0
            END) AS check2_in_cleared,
        SUM(CASE
            WHEN ck3_in_cleared = 'yes'
                THEN ck3_in_amount
                ELSE 0
            END) AS check3_in_cleared,
        SUM(CASE
            WHEN ck1_out_cleared = 'yes'
                THEN ck1_out_amount
                ELSE 0
            END) AS check1_out_cleared,
        SUM(CASE
            WHEN ck2_out_cleared = 'yes'
                THEN ck2_out_amount
                ELSE 0
            END) AS check2_out_cleared,
        SUM(CASE
            WHEN ck3_out_cleared = 'yes'
                THEN ck3_out_amount
                ELSE 0
            END) AS check3_out_cleared"))
        -> whereRaw("(transfer2_state = 'PA' or (transfer_state = 'PA' and (transfer2_state = '' or transfer2_state is null)) or (state = 'PA' and (transfer_state = '' or transfer_state is null) and (transfer2_state = '' or transfer2_state is null))) and company = 'Taylor Properties'")
        -> get();

        $tp_pa = $tp_pa[0];

        $tp_pa_total = ($tp_pa['check1_in_cleared'] + $tp_pa['check2_in_cleared'] + $tp_pa['check3_in_cleared']) - ($tp_pa['check1_out_cleared'] + $tp_pa['check2_out_cleared'] + $tp_pa['check3_out_cleared']);

        $earnest['TP_PA'] = $tp_pa_total;

        $tp_dc = self::select(
            DB::raw("
        SUM(CASE
            WHEN ck1_in_cleared = 'yes'
                THEN ck1_in_amount
                ELSE 0
            END) AS check1_in_cleared,
        SUM(CASE
            WHEN ck2_in_cleared = 'yes'
                THEN ck2_in_amount
                ELSE 0
            END) AS check2_in_cleared,
        SUM(CASE
            WHEN ck3_in_cleared = 'yes'
                THEN ck3_in_amount
                ELSE 0
            END) AS check3_in_cleared,
        SUM(CASE
            WHEN ck1_out_cleared = 'yes'
                THEN ck1_out_amount
                ELSE 0
            END) AS check1_out_cleared,
        SUM(CASE
            WHEN ck2_out_cleared = 'yes'
                THEN ck2_out_amount
                ELSE 0
            END) AS check2_out_cleared,
        SUM(CASE
            WHEN ck3_out_cleared = 'yes'
                THEN ck3_out_amount
                ELSE 0
            END) AS check3_out_cleared"))
        -> whereRaw("(transfer2_state = 'DC' or (transfer_state = 'DC' and (transfer2_state = '' or transfer2_state is null)) or (state = 'DC' and (transfer_state = '' or transfer_state is null) and (transfer2_state = '' or transfer2_state is null))) and company = 'Taylor Properties'")
        -> get();

        $tp_dc = $tp_dc[0];

        $tp_dc_total = ($tp_dc['check1_in_cleared'] + $tp_dc['check2_in_cleared'] + $tp_dc['check3_in_cleared']) - ($tp_dc['check1_out_cleared'] + $tp_dc['check2_out_cleared'] + $tp_dc['check3_out_cleared']);

        $earnest['TP_DC'] = $tp_dc_total;

        $aap_md = self::select(
            DB::raw("
        SUM(CASE
            WHEN ck1_in_cleared = 'yes'
                THEN ck1_in_amount
                ELSE 0
            END) AS check1_in_cleared,
        SUM(CASE
            WHEN ck2_in_cleared = 'yes'
                THEN ck2_in_amount
                ELSE 0
            END) AS check2_in_cleared,
        SUM(CASE
            WHEN ck3_in_cleared = 'yes'
                THEN ck3_in_amount
                ELSE 0
            END) AS check3_in_cleared,
        SUM(CASE
            WHEN ck1_out_cleared = 'yes'
                THEN ck1_out_amount
                ELSE 0
            END) AS check1_out_cleared,
        SUM(CASE
            WHEN ck2_out_cleared = 'yes'
                THEN ck2_out_amount
                ELSE 0
            END) AS check2_out_cleared,
        SUM(CASE
            WHEN ck3_out_cleared = 'yes'
                THEN ck3_out_amount
                ELSE 0
            END) AS check3_out_cleared"))
        -> whereRaw("(transfer2_state = 'MD' or (transfer_state = 'MD' and (transfer2_state = '' or transfer2_state is null)) or (state = 'MD' and (transfer_state = '' or transfer_state is null) and (transfer2_state = '' or transfer2_state is null))) and company = 'Anne Arundel Properties'")
        -> get();

        $aap_md = $aap_md[0];

        $aap_md_total = ($aap_md['check1_in_cleared'] + $aap_md['check2_in_cleared'] + $aap_md['check3_in_cleared']) - ($aap_md['check1_out_cleared'] + $aap_md['check2_out_cleared'] + $aap_md['check3_out_cleared']);

        $earnest['AAP_MD'] = $aap_md_total;

        return $earnest;
    }
}
