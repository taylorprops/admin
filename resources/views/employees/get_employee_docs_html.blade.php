@foreach($docs -> where('active', 'yes') as $doc)
    <div class="d-flex justify-content-between align-items-center list-group-item">
        <div>
            <a href="{{ $doc -> file_location }}" target="_blank">{{ $doc -> file_name }}</a>
        </div>
        <div>
            <a href="javascript:void(0)" class="delete-doc-button" data-doc-id="{{ $doc -> id }}"><i class="fa fa-trash text-danger"></i></a>
        </div>
    </div>
@endforeach

@if(count($docs -> where('active', 'no')) > 0)


    <a class="text-danger mt-3 mb-2" data-toggle="collapse" href="#deleted_docs" role="button" aria-expanded="false" aria-controls="deleted_docs">
        Show Deleted
    </a>

    <div class="collapse" id="deleted_docs">
        <div class="text-gray mb-3">Deleted</div>
        @foreach($docs -> where('active', 'no') as $doc)
            <div class="d-flex justify-content-between align-items-center list-group-item">
                <div>
                    <a href="{{ $doc -> file_location }}" target="_blank">{{ $doc -> file_name }}</a>
                </div>
                <div>
                    <a href="javascript:void(0)" class="restore-doc-button" data-doc-id="{{ $doc -> id }}">Restore <i class="fa fa-exchange text-primary ml-2"></i></a>
                </div>
            </div>
        @endforeach

    </div>

@endif
