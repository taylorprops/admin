@foreach($notes as $note)

        @php $user_name = $note -> user -> name; @endphp

        <div class="border-bottom mb-2">
            <div class="d-flex justify-content-between small">
                <div class="font-italic">{{ $user_name }}</div>
                <div class="d-flex justify-content-start align-items-center">
                    <div class="mr-2">{{ date_mdy($note -> created_at) }}</div>
                    <div>
                        @if($note -> user_id = auth() -> user() -> id)
                            <a href="javascript: void(0)" class="text-danger delete-transaction-note-button" data-note-id="{{ $note -> id }}" data-listing-key="{{ $note -> ListingKey }}"><i class="fad fa-trash"></i></a>
                        @endif
                    </div>
                </div>

            </div>
            <div class="m-1 p-2 text-gray">
                {!! nl2br($note -> notes) !!}
            </div>

        </div>

    @endforeach
