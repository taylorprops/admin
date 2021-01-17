@php
$checks_count = count($checks_out -> where('active', 'yes'));
$checks_total = 0;
$deleted = 0;
@endphp
@foreach($checks_out as $check)
    @php
    $classes = '';
    if($check -> active == 'yes') {
        $checks_total += $check -> check_amount;
    } else {
        $classes = $check -> active == 'no' ? 'inactive hidden' : '';
    }
    @endphp
    <div class="p-2 mr-2 border bg-white mb-3 shadow rounded check-image-container out {{ $classes }}">
        <div class="row">
            <div class="col-12 col-md-3">
                <div class="list-group font-8 text-gray">
                    <div class="list-group-item check-details">
                        {{ Str::of($check -> check_recipient) -> limit(30) }}
                    </div>
                    <div class="list-group-item check-details">
                        #{{ $check -> check_number }}
                    </div>
                    <div class="list-group-item check-details">
                        {{ $check -> check_date }}
                    </div>
                    <div class="list-group-item check-details">
                        ${{ number_format($check -> check_amount, 2) }}
                    </div>
                </div>
                <div class="list-group font-8 mt-2">
                    <div class="list-group-item check-details">
                        <div class="d-flex justify-content-between">
                            <div class="text-gray">Ready:</div>
                            <div class="text-primary">{{ $check -> check_date_ready != '' ? date('n/j/Y', strtotime($check -> check_date_ready)) : '---' }}</div>
                        </div>
                    </div>
                    <div class="list-group-item check-details">
                        <div class="d-flex justify-content-between">
                            <div class="text-gray">By:</div>
                            <div class="text-primary">{{ ucwords($check -> check_delivery_method ?? '---') }}</div>
                        </div>
                        @if($check -> check_delivery_method == 'mail' || $check -> check_delivery_method == 'fedex')
                            <div class="text-primary">{!! $check -> check_mail_to_street.'<br>'.$check -> check_mail_to_city.', '.$check -> check_mail_to_state.' '.$check -> check_mail_to_zip !!}</div>
                        @endif
                    </div>

                </div>
            </div>
            <div class="col-10 col-md-7">
                <div class="check-image-div">
                    <img src="{{ $check -> image_location }}">
                </div>
            </div>
            @if($check -> active == 'yes')
                <div class="col-2">
                    <div class="pr-2">
                        <a href="{{ $check -> file_location }}" target="_blank" class="btn btn-block btn-primary"><i class="fad fa-eye mr-1"></i> View</a>
                        <a href="javascript: void(0)"
                        class="btn btn-block btn-default edit-check-out-button"
                        data-check-id="{{ $check -> id }}"
                        data-check-date="{{ $check -> check_date }}"
                        data-check-number="{{ $check -> check_number }}"
                        data-check-amount="{{ $check -> check_amount }}"
                        data-image-location="{{ $check -> image_location }}"
                        data-recipient="{{ $check -> check_recipient }}"
                        data-recipient-agent-id="{{ $check -> check_recipient_agent_id }}"
                        data-delivery-method="{{ $check -> check_delivery_method }}"
                        data-date-ready="{{ $check -> check_date_ready }}"
                        data-mail-to-street="{{ $check -> check_mail_to_street }}"
                        data-mail-to-city="{{ $check -> check_mail_to_city }}"
                        data-mail-to-state="{{ $check -> check_mail_to_state }}"
                        data-mail-to-zip="{{ $check -> check_mail_to_zip }}"
                        ><i class="fad fa-edit mr-1"></i> Edit</a>
                        <a href="javascript: void(0)" class="btn btn-block btn-danger delete-check-out-button" data-check-id="{{ $check -> id }}"><i class="fad fa-trash mr-1"></i> Delete</a>
                    </div>
                </div>
            @else
                @php $deleted += 1; @endphp
                <div class="col-2 text-center">
                    <span class="text-danger font-weight-bold"><i class="fal fa-ban mr-2"></i> Deleted</span>
                    <a href="javascript: void(0)" class="btn btn-sm btn-default undo-delete-check-out-button" data-check-id="{{ $check -> id }}"><i class="fal fa-undo-alt mr-1"></i> Undo</a>
                </div>
            @endif
        </div>
    </div>

@endforeach

@if($deleted > 0)
<a href="javascript: void(0)" class="btn btn-sm btn-primary show-deleted-out-button">Show Deleted Checks</a>
@endif

<input type="hidden" id="checks_out_total_amount" value="{{ $checks_total }}">
<input type="hidden" id="checks_out_total_count" value="{{ $checks_count }}">
