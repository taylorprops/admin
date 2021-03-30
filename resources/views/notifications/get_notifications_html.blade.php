

<div class="list-group mt-3 font-8">

    @if(count($notifications) == 0)

        <div class="text-white bg-success w-100 text-center font-10 p-4 rounded"><i class="fal fa-check mr-2"></i> No new notifications</div>

    @else

        @foreach($notifications as $notification)

            @php
            $date = date('Y-m-d', strtotime($notification -> created_at));
            if($date == date('Y-m-d')) {
                $date = 'Today at '.date('g:i a', strtotime($notification -> created_at));
            } else if($date == date('Y-m-d', strtotime('-1 day'))) {
                $date = 'Yesterday';
            } else {
                $date = date('M jS, Y', strtotime($notification -> created_at));
            }
            @endphp

            <div class="alert bg-blue-light p-1" role="alert" data-id="{{ $notification -> id }}">

                <div class="d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold font-italic">
                        {{ $date }}
                    </div>
                    <div>
                        <a href="javascript:void(0)" class="float-right notifications-mark-as-read" data-id="{{ $notification -> id }}">
                            <i class="fal fa-check mr-2"></i> Mark read
                        </a>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        {!! $notification -> data['message'] !!}
                        <a href="{{ $notification -> data['link_url'] }}" target="_blank">{{ $notification -> data['link_text'] }}</a>
                    </div>
                </div>

            </div>

            @if($loop -> last)
                <div>
                    <a href="javascript:void(0)" class="btn btn-sm btn-danger notifications-mark-all float-right">
                        <i class="fal fa-times mr-2"></i> Mark all as read
                    </a>
                </div>
            @endif

        @endforeach

    @endif


    <div class="text-gray font-9 pt-3 mt-3 mb-2 border-top">Read Notifications</div>

    @foreach($read_notifications as $read_notification)

        @php
        $date = date('Y-m-d', strtotime($read_notification -> created_at));
        if($date == date('Y-m-d')) {
            $date = 'Today';
        } else if($date == date('Y-m-d', strtotime('-1 day'))) {
            $date = 'Yesterday';
        } else {
            $date = date('M jS, Y', strtotime($read_notification -> created_at));
        }

        // create link details to item
        $link = ' - <a href="/agents/doc_management/transactions/transaction_details/'.$read_notification -> data['transaction_id'].'/'.$read_notification -> data['transaction_type'].'?tab=commission" target="_blank">View Commission</a>';
        @endphp
        <div class="alert bg-red-light p-1" role="alert">

            <div class="d-flex justify-content-between align-items-center">
                <div class="font-weight-bold font-italic">
                    {{ $date }}
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    {!! $read_notification -> data['message'] !!}
                    {!! $link !!}
                </div>
            </div>

        </div>


    @endforeach


</div>

<span class="global-notifications-count hidden">{{ count($notifications) }}</span>