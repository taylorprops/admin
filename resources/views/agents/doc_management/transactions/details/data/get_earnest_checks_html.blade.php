@php
$cleared_total = 0;
$pending_total = 0;
$deleted = 0;
@endphp
@foreach ($checks as $check)

    @php
        $status_class_span = '<span class="text-success font-9">Cleared <i class="fad fa-thumbs-up ml-2"></i></span>';
        if($check -> check_status == 'pending') {
            $status_class_span = '<span class="text-primary font-9">Pending <i class="fal fa-hourglass ml-2"></i></span>';
        } else if($check -> check_status == 'bounced') {
            $status_class_span = '<span class="text-danger font-9">Bounced <i class="fad fa-thumbs-down ml-2"></i></span>';
        }

        $classes = '';
        if($check -> active == 'yes') {
            if($check -> check_status == 'cleared') {
                $cleared_total += $check -> check_amount;
            } else if($check -> check_status == 'pending') {
                $pending_total += $check -> check_amount;
            }
        } else {
            $classes = $check -> active == 'no' ? 'inactive hidden' : '';
        }
    @endphp

    <div class="earnest-check-div mb-4 p-2 border rounded {{ $check -> check_status }} {{ $check_type}} {{ $classes }}">

        <div class="row">

            <div class="col-12 col-md-4">

                <div class="row no-gutters">

                    <div class="col-12 col-md-6 pr-2">

                        <ul class="list-group earnest-check-details">

                            <li class="list-group-item d-flex justify-content-between">
                                @if($check_type == 'in')
                                    <span>Deposited</span>
                                    <span class="font-weight-bold">{{ $check -> date_deposited }}</span>
                                @else
                                    <span>Processed</span>
                                    <span class="font-weight-bold">{{ $check -> date_sent }}</span>
                                @endif
                            </li>

                            <li class="list-group-item d-flex justify-content-between">
                                @if($check_type == 'in')
                                    <span>Name</span>
                                    <span class="font-weight-bold">{{ shorten_text($check -> check_name, 15) }}</span>
                                @else
                                    <span>Payable To</span>
                                    <span class="font-weight-bold">{{ shorten_text($check -> payable_to, 12) }}</span>
                                @endif
                            </li>

                            <li class="list-group-item d-flex justify-content-between">
                                <span>Date</span>
                                <span class="font-weight-bold">{{ $check -> check_date }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between">
                                <span>Number</span>
                                <span class="font-weight-bold">{{ $check -> check_number }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between">
                                <span>Amount</span>
                                <span class="font-weight-bold">${{ number_format($check -> check_amount, 2) }}</span>
                            </li>

                        </ul>

                    </div>

                    @if($check -> active == 'yes')

                        <div class="col-12 col-md-6">

                            <ul class="list-group earnest-check-details">

                                <li class="d-flex justify-content-between">
                                    <span class="font-9">Status</span>
                                    {!! $status_class_span !!}
                                </li>

                                <li class="checkbox-li">
                                    <div class="d-flex justify-content-between align-items-center">

                                        @if(!$transferred)
                                            <div class="text-success">
                                                <input type="checkbox" class="custom-form-element form-checkbox cleared-checkbox" value="cleared" data-check-id="{{ $check -> id }}" data-check-type="{{ $check_type }}" @if($check -> check_status == 'cleared') checked @endif data-label="Cleared">
                                            </div>

                                            @if($check_type == 'in')
                                                <div class="text-danger">
                                                    <input type="checkbox" class="custom-form-element form-checkbox cleared-checkbox" value="bounced" data-check-id="{{ $check -> id }}" data-check-type="{{ $check_type }}" @if($check -> check_status == 'bounced') checked @endif data-label="Bounced" @if($transferred) disabled @endif>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </li>

                                @if($check -> check_status == 'cleared')
                                    <li class="d-flex justify-content-start text-success">
                                        <span class="mr-2">Cleared</span>
                                        <span class="font-weight-bold">{{ $check -> date_cleared }}</span>
                                    </li>
                                @endif

                                @if($check_type == 'out')
                                    <li>
                                        Mail To:<br>
                                        <span class="font-weight-bold">{!! nl2br($check -> mail_to_address) !!}</span>
                                    </li>
                                @endif
                            </ul>

                        </div>

                    @endif

                </div>

            </div>

            <div class="col-12 col-md-6">
                <div class="earnest-check-image-div border border-primary"><img src="{{ $check -> image_location }}" class="w-100"></div>
            </div>

            <div class="col-12 col-md-2">

                <div class="d-flex justify-content-around align-items-center h-100">

                    @if($check -> active == 'yes')
                        <div>

                            <a href="{{ $check -> file_location }}" class="btn btn-primary btn-block" target="_blank"><i class="fad fa-eye mr-2"></i> View</a>

                            <button class="btn btn-primary btn-block edit-earnest-check-button"
                                data-check-id="{{ $check -> id }}"
                                data-check-type="{{ $check_type }}"
                                data-file-location="{{ $check -> file_location }}"
                                data-image-location="{{ $check -> image_location }}"
                                data-check-name="{{ $check -> check_name }}"
                                data-payable-to="{{ $check -> payable_to }}"
                                data-check-date="{{ $check -> check_date }}"
                                data-check-number="{{ $check -> check_number }}"
                                data-check-amount="{{ $check -> check_amount }}"
                                data-date-deposited="{{ $check -> date_deposited }}"
                                data-mail-to-address="{{ $check -> mail_to_address }}"
                                data-date-sent="{{ $check -> date_sent }}"
                                @if($transferred) disabled @endif
                                >
                                <i class="fad fa-pencil mr-2"></i> Edit
                            </button>

                            @if($check -> check_status == 'pending')
                                <button class="btn btn-danger btn-block delete-earnest-check-button" data-check-id="{{ $check -> id }}" data-check-type="{{ $check_type }}" @if($transferred) disabled @endif><i class="fal fa-ban mr-2"></i> Delete</button>
                            @endif

                        </div>
                    @else
                        @php $deleted += 1; @endphp
                        <div class="text-center">
                            <span class="text-danger mb-3"><i class="fal fa-ban mr-2"></i> Deleted</span>
                            <button class="btn btn-block btn-primary undo-delete-earnest-check-button ml-0" data-check-id="{{ $check -> id }}" data-check-type="{{ $check_type }}" @if($transferred) disabled @endif><i class="fal fa-undo-alt mr-2"></i> Undo</button>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
@endforeach

@if($deleted > 0)
    <div class="row">
        <div class="col-12 mb-4">
            <a href="javascript: void(0)" class="btn btn-sm btn-primary show-deleted-earnest-checks-button" data-check-type="{{ $check_type}}">Show Deleted Checks</a>
        </div>
    </div>
@endif

<input type="hidden" id="earnest_checks_{{ $check_type }}_cleared_total" value="{{ $cleared_total }}">
<input type="hidden" id="earnest_checks_{{ $check_type }}_pending_total" value="{{ $pending_total }}">

