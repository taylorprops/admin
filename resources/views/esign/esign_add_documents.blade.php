@extends('layouts.main')
@section('title', 'E-Sign - Add Documents')

@section('content')

<div class="container-1000 mt-5 mx-auto page-container page-esign-add-documents @if($from_upload == 'yes') hidden @endif">

    <div class="h2 text-primary">E-Sign</div>

    <div class="row">
        <div class="col-12">

            <div class="d-flex justify-content-between align-items-center">
                <div class="font-12 text-primary">{{ $address }}</div>
                <div class="d-flex justify-content-end next-div @if(!$docs_to_display) hidden @endif">
                    <div>
                        <a href="javascript: void(0)" class="btn btn-primary btn-lg p-3" id="create_envelope_button">Next <i class="fal fa-arrow-right ml-2"></i></a>

                        <div class="notification text-orange font-8 hidden">Please be patient, this may take a moment...</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">

        <div class="col-12">

            <div class="mt-5">

                <div class="row mt-5">

                    <div class="col-12 col-sm-6">

                        <div class="h5 text-orange">Upload And Reorder Documents</div>

                        <form id="upload_form" enctype="multipart/form-data">

                            <input type="file" class="custom-form-element form-input-file" id="esign_file_upload" name="esign_file_upload[]" multiple accept="image/*,.pdf" data-label="Select Documents">

                        </form>

                    </div>

                </div>

            </div>

        </div>


    </div>


    <div class="row">

        <div class="col-12">

            <div id="uploads_container" class="@if(!$docs_to_display) hidden @endif">

                <div class="p-0 p-sm-2 border-sm mt-4">

                    <ul class="list-group" id="uploads_div">

                        @if($docs_to_display)

                            @foreach($docs_to_display as $doc)

                                <li class="list-group-item upload-li" data-file-location="{{ $doc['file_location'] }}" data-document-id="{{ $doc['document_id'] }}" data-file-type="{{ $doc['file_type'] }}" data-file-name="{{ $doc['file_name'] }}" data-template-id="" data-template-applied-id="{{ $doc['template_id'] }}" data-upload-id="{{ $doc['data_upload_id'] }}">

                                    <div class="d-sm-flex justify-content-between align-items-center">

                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="file-preview mr-4 file-handle">
                                                <i class="fal fa-bars text-primary fa-lg"></i>
                                            </div>
                                            <div class="file-preview mr-2 file-handle d-none d-sm-inline-block">
                                                <img class="file-image" src="{{ $doc['image_location'] }}">
                                            </div>
                                            <div>
                                                <a href="{{ $doc['file_location'] }}" class="d-block d-sm-none" target="_blank">{{ shorten_text($doc['file_name'], 35) }}</a>
                                                <a href="{{ $doc['file_location'] }}" class="d-none d-sm-block" target="_blank">{{ $doc['file_name'] }}</a>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end align-items-center mt-2 mt-sm-0 ml-sm-3">
                                            <div class="mr-4 template-status">
                                                @if($doc['file_type'] == 'system' && $doc['template_id'] > 0 && !$doc['signed'])
                                                <div class="no-wrap template-applied">
                                                    <span class="text-success"><i class="fal fa-check mr-2"></i> <span class="font-8">Template Applied</span></span>
                                                </div>
                                                <div class="d-flex justify-content-end edit-template-options">
                                                    <a href="javascript: void(0)" class="show-apply-template-button small">Edit <i class="fad fa-pencil ml-1"></i></a>
                                                    <a href="javascript: void(0)" class="remove-template-button small text-danger ml-3">Remove <i class="fal fa-times ml-1"></i></a>
                                                </div>
                                                @else
                                                <a href="javascript: void(0)" class="btn btn-sm btn-primary show-apply-template-button"><i class="fal fa-plus mr-2 fa-lg"></i> Add Template</a>
                                                @endif
                                            </div>
                                            <div class="ml-2 pl-3 border-left">
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


    </div>

</div>

<input type="hidden" id="from_upload" value="{{ $from_upload }}">
<input type="hidden" id="Listing_ID" value="{{ $Listing_ID }}">
<input type="hidden" id="Contract_ID" value="{{ $Contract_ID }}">
<input type="hidden" id="Referral_ID" value="{{ $Referral_ID }}">
<input type="hidden" id="transaction_type" value="{{ $transaction_type }}">
<input type="hidden" id="User_ID" value="{{ $User_ID }}">
<input type="hidden" id="Agent_ID" value="{{ $Agent_ID }}">
<input type="hidden" id="document_ids" value="{{ implode(',', $document_ids) }}">

<div class="modal fade draggable" id="add_template_modal" tabindex="-1" role="dialog" aria-labelledby="add_template_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="add_template_modal_title">Add Template</h4>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">

                        <div class="font-10 text-gray my-3">Select the Template you want to apply</div>

                        <div class="no-wrap">

                            <table id="templates_table" class="table table-hover table-bordered table-sm" width="100%">

                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Template Name</th>
                                        <th>Signers</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($templates as $template)

                                        @php
                                        $signers = $template -> signers;
                                        $recipients = [];
                                        foreach($signers as $signer) {
                                            $recipients[] = $signer -> signer_role;
                                        }
                                        @endphp
                                        <tr>
                                            <td><button type="button" class="btn btn-sm btn-primary apply-template-button" data-template-id="{{ $template -> id }}"><i class="fa fa-plus mr-sm-2"></i> <span class="d-none d-sm-inline-block">Apply</span></button></td>
                                            <td>{{ $template -> template_name }}</td>
                                            <td>{!! implode(', ', $recipients) !!}</td>
                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
