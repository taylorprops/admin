

<div class="list-group font-8">

    @if(count($notifications) == 0)

    <div class="text-gray font-10 text-center p-2">
        <i class="fal fa-check mr-2"></i> No New Notifications
    </div>

    @else

        <div class="w-100 d-flex justify-content-end">
            <a href="javascript:void(0)" class="notifications-mark-all font-8 float-right text-danger">
                <i class="fal fa-check mr-2"></i> Mark all read
            </a>
        </div>

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

            <div class="my-2">

                <div class="text-primary border-top-primary pl-1 pt-1 d-flex justify-content-between align-items-center">
                    <div class="font-italic font-weight-bold">
                        {{ $date }}
                    </div>

                    <div>
                        <a href="javascript:void(0)" class="notifications-mark-as-read mr-2" data-id="{{ $notification -> id }}">
                            <i class="fal fa-check mr-2"></i> Mark read
                        </a>
                    </div>
                </div>


                <div class="d-flex justify-content-between align-items-end px-1 py-2">
                    <div>{!! $notification -> data['message'] !!}</div>
                    <div> <a href="{{ $notification -> data['link_url'] }}" target="_blank">{{ $notification -> data['link_text'] }}</a></div>
                </div>

            </div>

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

            <div class="my-2">

                <div class="text-gray border-top-gray pl-1 pt-1 d-flex justify-content-between align-items-center">
                    <div class="font-italic font-weight-bold">
                        {{ $date }}
                    </div>

                    <div>
                        <a href="javascript:void(0)" class="notifications-mark-unread mr-2" data-id="{{ $read_notification -> id }}">
                            <i class="fal fa-undo mr-2"></i> Mark Unread
                        </a>
                    </div>
                </div>


                <div class="d-flex justify-content-between align-items-end border-bottom-gray text-gray px-1 py-2">
                    <div>{!! $read_notification -> data['message'] !!}</div>
                    <div> <a href="{{ $read_notification -> data['link_url'] }}" class="text-gray" target="_blank">{{ $read_notification -> data['link_text'] }}</a></div>
                </div>

            </div>


        @endforeach

    @endif

</div>

<span class="global-notifications-count hidden">{{ count($notifications) }}</span>
