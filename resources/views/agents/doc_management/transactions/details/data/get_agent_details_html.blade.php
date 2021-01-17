<div class="row">
    <div class="col-5">

        <div class="font-weight-bold font-11 text-primary">{{ $agent_details -> full_name }}</div>

        @if($agent_details -> team_id > 0)
        <div class="font-italic font-8">{{ $teams -> GetTeamName($agent_details -> team_id) }}</div>
        @endif

        <div class="font-weight-bold font-8 mt-2">
            @if($agent_details -> llc_name != '')
                LLC - {{ $agent_details -> llc_name }}<br>
                EIN - {{ $agent_details -> ein }}<br>
            @endif
            <div class="d-flex justify-content-start align-items-center">
                <div class="mr-3">Soc Sec </div>
                <div><span class="soc-sec mr-3">{{ $agent_details -> social_security }}</span><span class="soc-sec mr-4">***-**-****</span></div>
                <div><a href="javascript:void(0)" class="show-soc-sec"><i class="fad fa-eye text-primary"></i></a></div>
            </div>
        </div>

        <div class="text-gray mt-2">
            {{ $agent_details -> address_street }}<br>
            {{ $agent_details -> address_city.', '.$agent_details -> address_state.' '.$agent_details -> address_zip }}<br>
            {{ $agent_details -> cell_phone }}<br>
            <a href="mailto:{{ $agent_details -> email }}">{{ $agent_details -> email }}</a>
        </div>

    </div>

    <div class="col-7">

        <div class="font-weight-bold text-primary">Agent Account Notes</div>

        <div class="notes-container border rounded p-2">
            @foreach($agent_notes as $agent_note)
                <div class="note-div border-top">
                    <div class="font-7 text-gray">{{ date('Y-m-d', strtotime($agent_note -> created_at)) }} - <span class="font-italic">{{ $agent_note -> created_by }}</span></div>
                    {!! nl2br($agent_note -> notes) !!}
                </div>
            @endforeach
        </div>

    </div>

</div>
