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
    </div>

@endforeach
