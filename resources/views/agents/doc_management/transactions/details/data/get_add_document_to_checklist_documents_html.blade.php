@if(count($documents_available) > 0)
    @foreach($folders as $folder)
        @if(count($documents_available -> where('folder', $folder -> id)) > 0)
            <div class="h5 text-orange">{{ $folder -> folder_name }}</div>
            @foreach($documents_available -> where('folder', $folder -> id) as $document_available)
                <div class="d-flex justify-content-start align-items-center border-bottom">
                    <div>
                        <button type="button" class="btn btn-sm btn-primary select-document-button" data-dismiss="modal" data-document-id="{{ $document_available -> id }}">Add</button>
                    </div>
                    <div class="ml-2">{{ $document_available -> file_name_display }}</div>
                </div>
            @endforeach
        @endif
    @endforeach
@else
    <div class="row">
        <div class="col-12 col-sm-8 mx-auto">
            <div class="d-flex justify-content-start align-items-center">
                <i class="fad fa-exclamation-triangle text-danger fa-2x mr-2"></i>
                <div class="text-danger font-10 text-center">You do not have any available documents yet. Add documents in the "Documents" tab.</div>
            </div>
        </div>
    </div>
@endif
