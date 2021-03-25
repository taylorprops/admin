<div class="no-wrap">

    <table class="table table-hover table-bordered table-sm earnest-table {{ $tab }}" id="earnest_table_{{ $tab}}" width="100%">

        <thead>
            <tr>
                @if($tab == 'missing')
                    <th class="text-center">
                        <input type="checkbox" class="custom-form-element form-checkbox check-all" data-label="">
                    </th>
                @endif
                <th></th>
                <th>Account</th>
                <th>Agent</th>
                <th>Property</th>
                <th>Received</th>
                <th>Contract Date</th>
                <th>CloseDate</th>
                <th>Status</th>
                @if($tab == 'missing')
                    <th>Last Emailed</th>
                    <th></th>
                @endif
                @if($tab == 'missing' || $tab == 'waiting')
                    <th>Notes</th>
                @endif
            </tr>
        </thead>

        <tbody>

            @foreach($contracts as $contract)

                @php
                $agent = $contract -> agent -> full_name;
                $status = $contract -> status -> resource_name;
                $earnest = $contract -> earnest;
                $earnest_account = $earnest -> earnest_account;
                $notes = $earnest -> notes;
                $Earnest_ID = $earnest -> id;
                @endphp

                <tr>
                    @if($tab == 'missing')
                        <td class="text-center">
                            <input type="checkbox" class="custom-form-element form-checkbox deposit-input" data-contract-id="{{ $contract -> Contract_ID }}" data-label="">
                        </td>
                    @endif
                    <td>
                        <a class="btn btn-primary" href="/agents/doc_management/transactions/transaction_details/{{ $contract -> Contract_ID }}/contract?tab=earnest" target="_blank"><i class="fal fa-eye mr-2"></i> View</a>
                    </td>
                    <td>{{ $earnest_account -> resource_state.' - '.get_initials($earnest_account -> resource_name) }}</td>
                    <td>{{ $agent }}</td>
                    <td>{{ $contract -> FullStreetAddress.' '.$contract -> City.', '.$contract -> StateOrProvince.' '.$contract -> PostalCode }}</td>
                    <td>${{ number_format($earnest -> amount_received) }}</td>
                    <td>{{ date_mdy($contract -> ContractDate) }}</td>
                    <td>{{ date_mdy($contract -> CloseDate) }}</td>
                    <td>{{ $status }}</td>
                    @if($tab == 'missing')

                        <td>

                            @if($earnest -> last_emailed_date != '' && $earnest -> last_emailed_date != '0000-00-00')
                                @if($earnest -> last_emailed_date == date('Y-m-d'))
                                <span class="text-success"><i class="fal fa-check mr-2"></i> Today</span>
                                @else
                                    {{ date_mdy($earnest -> last_emailed_date) }}
                                @endif
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary email-agent single"><i class="fal fa-envelope mr-2"></i> Email Agent</button>
                        </td>

                    @endif

                    @if($tab == 'missing' || $tab == 'waiting')

                        <td>

                            @if(count($notes) > 0)

                                <a class="btn btn-primary" data-toggle="collapse" href="#notes_section_{{ $contract -> Contract_ID }}" role="button" aria-expanded="false" aria-controls="notes_section_{{ $contract -> Contract_ID }}">
                                    <i class="fad fa-eye mr-2"></i> View/Add
                                </a>

                            @else

                            <a class="btn btn-primary" data-toggle="collapse" href="#notes_section_{{ $contract -> Contract_ID }}" role="button" aria-expanded="false" aria-controls="notes_section_{{ $contract -> Contract_ID }}">
                                <i class="fal fa-plus mr-2"></i> Add
                            </a>

                            @endif

                            <div class="relative">

                                <div class="collapse earnest-notes-section bg-white border shadow" id="notes_section_{{ $contract -> Contract_ID }}" data-parent="#earnest_table_{{ $tab }}">

                                    <div class="p-2">

                                        <div class="d-flex justify-content-between align-items-center">

                                            <div class="font-10">{{ $contract -> FullStreetAddress.' '.$contract -> City.', '.$contract -> StateOrProvince.' '.$contract -> PostalCode }}</div>

                                            <a class="btn btn-danger" data-toggle="collapse" href="#notes_section_{{ $contract -> Contract_ID }}" role="button" aria-expanded="false" aria-controls="notes_section_{{ $contract -> Contract_ID }}">
                                                <i class="fa fa-times fa-lg"></i>
                                            </a>

                                        </div>

                                        <div class="px-3 pb-3 border-bottom">
                                            <div class="text-orange font-10 mb-2"><i class="fal fa-plus mr-2"></i> Add Notes</div>
                                            <textarea class="custom-form-element form-textarea earnest-notes-{{ $Earnest_ID }}" data-label="Enter Notes"></textarea>
                                            <button class="btn btn-success save-earnest-notes-button ml-0" data-earnest-id="{{ $Earnest_ID }}"><i class="fad fa-save mr-2"></i> Save Notes </button>
                                        </div>

                                        <div id="earnest_notes_div_{{ $Earnest_ID }}" class="earnest-notes-div">

                                            @foreach($notes as $note)

                                                @php $user_name = $note -> user -> name; @endphp

                                                <div class="list-group-item border-top mb-2">
                                                    <div class="d-flex justify-content-between small">
                                                        <div class="font-italic">{{ $user_name }}</div>
                                                        <div>{{ date_mdy($note -> created_at) }}</div>
                                                    </div>
                                                    <div class="m-1 p-2 border text-gray">
                                                        {!! nl2br($note -> notes) !!}
                                                    </div>
                                                    @if($note -> user_id = auth() -> user() -> id)
                                                        <div class="d-flex justify-content-end">
                                                            <a href="javascript: void(0)" class="text-danger delete-earnest-note-button" data-note-id="{{ $note -> id }}" data-earnest-id="{{ $note -> Earnest_ID }}"><i class="fad fa-trash"></i></a>
                                                        </div>
                                                    @endif
                                                </div>

                                            @endforeach

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </td>

                    @endif
                </tr>

            @endforeach

        </tbody>

    </table>

</div>
