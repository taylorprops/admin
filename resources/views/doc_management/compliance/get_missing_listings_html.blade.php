<div class="container-1200">

    <div class="row">

        <div class="col-12">

            <div class="no-wrap">

                <table id="listings_table" class="table table-hover table-sm missing-transactions-table" width="100%">

                    <thead>
                        <tr>
                            <th><span class="d-none">Check All</span><input type="checkbox" class="custom-form-element form-checkbox check-all" data-label=""></th>
                            <th>List Date</th>
                            <th>Property</th>
                            <th>Agent</th>
                            <th>Email Agent</th>
                            <th>Notes</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($properties as $property)

                            @php
                                $agent_name = $property -> agent ? $property -> agent -> full_name : $property -> ListAgentFirstName.' '.$property -> ListAgentLastName;
                                $agent_phone = $property -> agent ? $property -> agent -> cell_phone : $property -> ListAgentPreferredPhone;
                                $agent_email = $property -> agent ? $property -> agent -> email : $property -> ListAgentEmail;
                                $notes = $property -> notes;
                                $ListingKey = $property -> ListingKey;
                            @endphp

                            <tr>
                                <td><input type="checkbox" class="custom-form-element form-checkbox transaction-checkbox" data-listing-key="{{ $property -> ListingKey }}" data-label=""></td>
                                <td data-sort="{{ $property -> MLSListDate }}">
                                    {{ date_mdy($property -> MLSListDate) }}
                                    <br>
                                    <span class="font-8">{{ $property -> MlsStatus }}</span>
                                </td>
                                <td>
                                    {{ ucwords(strtolower($property -> FullStreetAddress.' '.$property -> City)).', '.$property -> StateOrProvince.' '.$property -> PostalCode }}
                                    <br>
                                    <span class="font-8">{{ $property -> ListingId }}</span>
                                </td>
                                <td>
                                    {!! $agent_name !!}
                                    <br>
                                    <span class="font-8">{{ format_phone($agent_phone) }}</span>
                                </td>
                                <td>
                                    @if($agent_email)
                                    <button class="btn btn-primary btn-sm email-agent single ml-0" data-email="{{ $agent_email }}"><i class="fal fa-envelope mr-2"></i> Email Agent</button>
                                    @endif
                                    @if($property -> last_emailed_date != '' && $property -> last_emailed_date != '0000-00-00')
                                        <br>
                                        <div class="small">
                                            Last Emailed:
                                            @if($property -> last_emailed_date == date('Y-m-d'))
                                            <span class="text-success ml-2"><i class="fal fa-check mr-1"></i> Today</span>
                                            @else
                                                {{ date_mdy($property -> last_emailed_date) }}
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>

                                    @if(count($notes) > 0)

                                        <a class="btn btn-primary btn-sm ml-0" data-toggle="collapse" href="#notes_section_{{ $property -> ListingKey }}" role="button" aria-expanded="false" aria-controls="notes_section_{{ $property -> ListingKey }}">
                                            <i class="fad fa-eye mr-2"></i> View/Add
                                        </a>

                                    @else

                                        <a class="btn btn-primary btn-sm ml-0" data-toggle="collapse" href="#notes_section_{{ $property -> ListingKey }}" role="button" aria-expanded="false" aria-controls="notes_section_{{ $property -> ListingKey }}">
                                            <i class="fal fa-plus mr-2"></i> Add
                                        </a>

                                    @endif

                                    <div class="relative">

                                        <div class="collapse transaction-notes-section bg-white border shadow" id="notes_section_{{ $property -> ListingKey }}" data-parent="#listings_table">

                                            <div class="p-2">

                                                <div class="d-flex justify-content-between align-items-center">

                                                    <div class="font-10">{{ $property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode }}</div>

                                                    <a class="btn btn-danger" data-toggle="collapse" href="#notes_section_{{ $property -> ListingKey }}" role="button" aria-expanded="false" aria-controls="notes_section_{{ $property -> ListingKey }}">
                                                        <i class="fa fa-times fa-lg"></i>
                                                    </a>

                                                </div>

                                                <div class="px-3 pb-3 border-bottom">
                                                    <div class="text-orange font-10 mb-2"><i class="fal fa-plus mr-2"></i> Add Notes</div>
                                                    <textarea class="custom-form-element form-textarea transaction-notes-{{ $property -> ListingKey }}" data-label="Enter Notes"></textarea>
                                                    <button class="btn btn-success save-transaction-notes-button ml-0" data-listing-key="{{ $property -> ListingKey }}"><i class="fad fa-save mr-2"></i> Save Notes </button>
                                                </div>

                                                <div id="transaction_notes_div_{{ $property -> ListingKey }}" class="transaction-notes-div">

                                                    @foreach($notes as $note)

                                                        @php $user_name = $note -> user -> name; @endphp

                                                        <div class="mb-2 border-bottom">
                                                            <div class="d-flex justify-content-between small">
                                                                <div class="font-italic">{{ $user_name }}</div>
                                                                <div class="d-flex justify-content-start align-items-center">
                                                                    <div class="mr-2">
                                                                        {{ date_mdy($note -> created_at) }}
                                                                    </div>
                                                                    @if($note -> user_id = auth() -> user() -> id)
                                                                        <a href="javascript: void(0)" class="text-danger delete-transaction-note-button" data-note-id="{{ $note -> id }}" data-listing-key="{{ $note -> ListingKey }}"><i class="fad fa-trash"></i></a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="m-1 p-2 text-gray">
                                                                {!! nl2br($note -> notes) !!}
                                                            </div>
                                                        </div>

                                                    @endforeach

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </body>

                </table>

            </div>

        </div>

    </div>

</div>
