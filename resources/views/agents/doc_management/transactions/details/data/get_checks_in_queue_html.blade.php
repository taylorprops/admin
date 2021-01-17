@if(count($checks_in_queue) > 0)

    <div class="row no-gutters">
        <div class="col-6 col-md-3"> </div>
        <div class="col-6 col-md-3">Address</div>
        <div class="col-6 col-md-2">Date Received</div>
        <div class="col-6 col-md-2">Amount</div>
        <div class="col-6 col-md-2"></div>
    </div>

    @foreach($checks_in_queue as $check_in_queue)

        @php $id = $check_in_queue -> id; @endphp

        <div class="border rounded p-2 mb-4 check-in-queue">
            <div class="row no-gutters d-flex align-items-center text-gray font-8">
                <div class="col-6 col-sm-3">
                    <a href="javascript: void(0)" class="btn btn-primary import-check-button" data-check-id="{{ $check_in_queue -> id }}"><i class="fad fa-file-import mr-2"></i> Import</a>
                </div>
                <div class="col-6 col-sm-3">
                    {{ $check_in_queue -> street }}<br>{{ $check_in_queue -> city }}, {{ $check_in_queue -> state }} {{ $check_in_queue -> zip }}
                </div>
                <div class="col-6 col-sm-2">
                    {{ $check_in_queue -> date_received }}
                </div>
                <div class="col-6 col-sm-2">
                    ${{ number_format($check_in_queue -> check_amount, 2) }}
                </div>
                <div class="col-6 col-sm-2">
                    <a class="btn btn-primary" data-toggle="collapse" href="#view_check_div_{{ $id }}" role="button" aria-expanded="false" aria-controls="view_check_div_{{ $id }}">
                        <i class="fad fa-eye mr-2"></i> View
                    </a>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="collapse view-queue-check-div" id="view_check_div_{{ $id }}" data-parent=".checks-queue-div">
                        <div class="view-queue-check-holder">
                            <img src="{{ $check_in_queue -> image_location }}">
                        </div>

                    </div>
                </div>
            </div>
        </div>


    @endforeach

@else
    <h5 class="text-gray w-100 text-center mt-5">No Checks in the Queue</h5>
@endif
