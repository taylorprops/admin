<?php

namespace App\Models\BrightMLS;

use Illuminate\Database\Eloquent\Model;

class AgentRoster extends Model
{
    protected $connection = 'mysql';
    public $table = 'bright_agent_roster';
    protected $primaryKey = 'MemberKey';
    public $timestamps = false;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query->join('bright_offices', 'bright_agent_roster.OfficeKey', '=', 'bright_offices.OfficeKey')
                ->select('bright_agent_roster.MemberFirstName',
                'bright_agent_roster.MemberFullName',
                'bright_agent_roster.MemberKey',
                'bright_agent_roster.MemberLastName',
                'bright_agent_roster.MemberMlsId',
                'bright_agent_roster.MemberNickname',
                'bright_agent_roster.MemberPreferredPhone',
                'bright_agent_roster.MemberEmail',
                'bright_agent_roster.MemberType',
                'bright_agent_roster.MemberSubType',
                'bright_agent_roster.OfficeKey',
                'bright_agent_roster.OfficeMlsId',
                'bright_agent_roster.OfficeBrokerKey',
                'bright_agent_roster.OfficeName',
                'bright_agent_roster.OfficeBrokerMlsId',
                'bright_offices.OfficeAddress1',
                'bright_offices.OfficeCity',
                'bright_offices.OfficeStateOrProvince',
                'bright_offices.OfficePostalCode',
                'bright_offices.OfficeCounty',
                'bright_offices.OfficeMlsId',
                'bright_offices.OfficeName',
                'bright_offices.OfficePhone')
                ->where('bright_agent_roster.MemberSubType', 'like', '%salesperson%');
        });
    }
}
