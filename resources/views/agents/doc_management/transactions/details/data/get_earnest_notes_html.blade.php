<div class="list-group">

    @foreach($notes as $note)

        @php $user_name = $note -> user -> name; @endphp

        <div class="list-group-item border-top mb-2">
            <div class="d-flex justify-content-between small">
                <div class="font-italic">{{ $user_name }}</div>
                <div>{{ date_mdy($note -> created_at) }}</div>
            </div>
            <div class="m-1 p-2 border text-gray">
                {!! nl2br($note -> notes) !!}
            </div>
            @if($note -> user_id = auth() -> user() -> id)
            <div class="d-flex justify-content-end">
                <a href="javascript: void(0)" class="text-danger delete-earnest-note-button" data-note-id="{{ $note -> id }}" data-earnest-id="{{ $note -> Earnest_ID }}"><i class="fad fa-trash"></i></a>
            </div>
            @endif
        </div>

    @endforeach

</div>
