@extends('layouts.main')
@section('title', 'E-Sign - Add Documents')

@section('content')

<div class="container-1200 mt-5 mx-auto page-container page-esign-add-documents">

    <div class="h2 text-primary">E-Sign</div>

    <div class="row mt-5">

        <div class="col-12 col-sm-6">

            <div class="h5 text-orange">Upload and Reorder Documents</div>

            <form id="upload_form" enctype="multipart/form-data">

                <input type="file" class="custom-form-element form-input-file" id="esign_file_upload" name="esign_file_upload[]" multiple data-label="Select Documents">

            </form>

        </div>

    </div>

    <div class="row">

        <div class="col-12 col-sm-9">

            <div id="uploads_container" class="@if(!$docs_to_display) hidden @endif">

                <div class="p-2 border mt-4">

                    <ul class="list-group" id="uploads_div">

                        @if($docs_to_display)

                            @foreach($docs_to_display as $doc)

                                <li class="list-group-item upload-li" data-file-location="{{ $doc['file_location'] }}" data-document-id="{{ $doc['document_id'] }}" data-file-type="{{ $doc['file_type'] }}" data-file-name="{{ $doc['file_name'] }}">

                                    <div class="d-flex justify-content-between align-items-center">

                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="file-preview mr-4 file-handle">
                                                <i class="fal fa-bars text-primary fa-lg"></i>
                                            </div>
                                            <div class="file-preview mr-2 file-handle">
                                                <img class="file-image" src="{{ $doc['image_location'] }}">
                                            </div>
                                            <div>
                                                <a href="{{ $doc['file_location'] }}" target="_blank">{{ $doc['file_name'] }}</a>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end align-items-center">
                                            <div class="ml-3 mr-4">
                                                @if($doc['file_type'] == 'user')
                                                    <a href="javascript: void(0)" class="btn btn-sm btn-primary apply-template-button"><i class="fal fa-plus mr-2 fa-lg"></i> Add Template</a>
                                                @else
                                                    <span class="text-success"><i class="fal fa-check mr-2"></i> <span class="font-8">Template Applied</span></span>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="javascript: void(0)" class="remove-upload-button"><i class="fal fa-times text-danger fa-2x"></i></a>
                                            </div>
                                        </div>

                                    </div>

                                </li>

                            @endforeach

                        @endif

                    </ul>

                </div>

            </div>

        </div>

        <div class="col-12 col-sm-3">
            <div class="mt-4 next-div @if(!$docs_to_display) hidden @endif">
                <a href="javascript: void(0)" class="btn btn-primary btn-lg p-3" id="create_envelope_button">Next <i class="fal fa-arrow-right ml-2"></i></a>
            </div>
        </div>
    </div>

</div>

<input type="hidden" id="Listing_ID" value="{{ $Listing_ID }}">
<input type="hidden" id="Contract_ID" value="{{ $Contract_ID }}">
<input type="hidden" id="Referral_ID" value="{{ $Referral_ID }}">
<input type="hidden" id="transaction_type" value="{{ $transaction_type }}">
<input type="hidden" id="User_ID" value="{{ $User_ID }}">
<input type="hidden" id="Agent_ID" value="{{ $Agent_ID }}">
<input type="hidden" id="document_ids" value="{{ implode(',', $document_ids) }}">

@endsection
