@if(count($upcoming_events) > 0)

    <div class="list-group">

        @foreach($upcoming_events as $event)

            @php
            $event_time = null;
            if($event -> event_type == 'reminder') {
                $event_time = $event -> start_time;
            }


            if($event -> event_type == 'event') {
                $icon = '<i class="fal fa-calendar text-primary fa-lg"></i>';
            } else if($event -> event_type == 'reminder') {
                $icon = '<i class="fal fa-clock text-gray fa-lg"></i>';
            } else if($event -> event_type == 'task') {
                $icon = '<i class="fal fa-tasks text-primary fa-lg"></i>';
            }

            $link = null;
            $transction_type = $event -> transaction_type;
            if($transction_type) {
                $id = $transction_type == 'listing' ? $event -> Listing_ID : $event -> Contract_ID;
                $link = '/agents/doc_management/transactions/transaction_details/'.$id.'/'.$transction_type;
            }
            @endphp

            <div class="list-group-item">

                <div class="d-flex justify-content-start align-items-center">

                    <div class="mr-4">
                        {!! $icon !!}
                    </div>

                    <div class="font-9 mr-4">
                        {{ date('D - M jS', strtotime($event -> start_date)) }}
                        @if($event_time)
                            <br>
                            <span class="font-8">{{ date('g:ia', strtotime($event -> start_time)) }}</span>
                        @endif
                    </div>

                    <div>
                        {{ $event -> event_title }}
                        @if($link)
                        <div class="font-8"><a href="{{ $link }}" target="_blank">View Transaction</a></div>
                        @endif
                    </div>

                </div>

            </div>

        @endforeach

    </div>

@else

    <div class="text-gray font-10 text-center bg-white rounded p-2">
        <i class="fal fa-check mr-2"></i> No Upcoming Events
    </div>

@endif
