

<div class="list-group font-8">

    @if(count($notifications) == 0)

    <div class="text-gray font-10 text-center p-2">
        <i class="fal fa-check mr-2"></i> No New Notifications
    </div>

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

            <div class="alert bg-blue-light p-1 border" role="alert" data-id="{{ $notification -> id }}">

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
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-end bg-white px-1 py-2 rounded">
                    <div>{!! $notification -> data['message'] !!}</div>
                    <div> <a href="{{ $notification -> data['link_url'] }}" target="_blank">{{ $notification -> data['link_text'] }}</a></div>
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


    @if(count($read_notifications) > 0)

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
            $link = '<a href="/agents/doc_management/transactions/transaction_details/'.$read_notification -> data['transaction_id'].'/'.$read_notification -> data['transaction_type'].'?tab=commission" target="_blank">View Commission</a>';
            @endphp
            <div class="alert alert-notification-read bg-blue-light p-1" role="alert">

                <div class="d-flex justify-content-between align-items-end">
                    <div class="font-weight-bold font-italic">
                        {{ $date }}
                    </div>
                    <div>
                        <a href="javascript:void(0)" class="float-right notifications-mark-unread" data-id="{{ $read_notification -> id }}">
                            <i class="fal fa-check mr-2"></i> Mark Unread
                        </a>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div>{!! $read_notification -> data['message'] !!}</div>
                    <div>{!! $link !!}</div>
                </div>

            </div>


        @endforeach

    @endif

</div>

<span class="global-notifications-count hidden">{{ count($notifications) }}</span>
