@php
$checks_count = count($checks_in -> where('active', 'yes'));
$checks_total = 0;
$deleted = 0;
@endphp
@foreach($checks_in as $check)
    @php
    $classes = '';
    if($check -> active == 'yes') {
        $checks_total += $check -> check_amount;
    } else {
        $classes = $check -> active == 'no' ? 'inactive hidden' : '';
    }
    @endphp
    <div class="p-2 mr-2 border bg-white mb-3 shadow rounded check-image-container in {{ $classes }}">
        <div class="row ">
            <div class="col-12 col-md-3">
                <div class="list-group font-8 text-gray">
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
                <div class="list-group font-8 text-gray mt-2">
                    <div class="list-group-item check-details">
                        <div class="d-flex justify-content-between">
                            <div>Deposited:</div>
                            <div class="text-primary">{{ $check -> date_deposited != '' ? date('n/j/Y', strtotime($check -> date_deposited)) : '---' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-7">
                <div class="check-image-div">
                    <img src="{{ $check -> image_location }}">
                </div>
            </div>
            @if($check -> active == 'yes')
                <div class="col-2 pl-0">
                    <div class="pr-2">
                        <a href="{{ $check -> file_location }}" target="_blank" class="btn btn-block btn-primary"><i class="fad fa-eye mr-2"></i> View</a>
                        <a href="javascript: void(0)"
                        class="btn btn-block btn-default edit-check-in-button"
                        data-check-id="{{ $check -> id }}"
                        data-date-received="{{ $check -> date_received }}"
                        data-date-deposited="{{ $check -> date_deposited }}"
                        data-check-date="{{ $check -> check_date }}"
                        data-check-number="{{ $check -> check_number }}"
                        data-check-amount="{{ $check -> check_amount }}"
                        data-image-location="{{ $check -> image_location }}">
                            <i class="fad fa-edit mr-2"></i> Edit
                        </a>
                        <a href="javascript: void(0)" class="btn btn-block btn-danger delete-check-in-button" data-check-id="{{ $check -> id }}" data-type="other"><i class="fad fa-trash mr-2"></i> Delete</a>

                        @if($check -> queue_id > 0)
                            <a href="javascript: void(0)" class="btn btn-block btn-sm btn-danger re-queue-check-button" data-check-id="{{ $check -> id }}"><i class="fad fa-recycle mr-2"></i> Re Queue</a>
                        @endif
                    </div>
                </div>
            @else
                @php $deleted += 1; @endphp
                <div class="col-2 text-center">
                    <span class="text-danger font-weight-bold"><i class="fad fa-ban mr-2"></i> Deleted</span>
                    <a href="javascript: void(0)" class="btn btn-sm btn-default undo-delete-check-in-button" data-check-id="{{ $check -> id }}"><i class="fad fa-undo-alt mr-2"></i> Undo</a>
                </div>
            @endif
        </div>
    </div>

@endforeach

@if($deleted > 0)
<a href="javascript: void(0)" class="btn btn-sm btn-primary show-deleted-in-button">Show Deleted Checks</a>
@endif

<input type="hidden" id="checks_in_total_amount" value="{{ $checks_total }}">
<input type="hidden" id="checks_in_total_count" value="{{ $checks_count }}">
