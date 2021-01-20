@extends('layouts.main')
@section('title', 'E-Sign - Add Documents')

@section('content')

<div class="container-1200 mt-5 mx-auto page-container page-esign-add-documents">

    <div class="h2 text-primary">E-Sign</div>

    <div class="row">

        <div class="col-9">

            <div class="mt-5">

                @if($is_template == 'yes')

                    <div class="row">

                        <div class="col-12 col-sm-6">

                            <div class="h5 text-orange">Enter A Name For Your Template</div>

                            <div class="template-name-div">
                                <input type="text" class="custom-form-element form-input required" id="template_name" data-label="Template Name">
                            </div>

                        </div>

                    </div>

                @endif

                <div class="row mt-5">

                    <div class="col-12 col-sm-6">

                        <div class="h5 text-orange">Upload And Reorder Documents</div>

                        <form id="upload_form" enctype="multipart/form-data">

                            <input type="file" class="custom-form-element form-input-file" id="esign_file_upload" name="esign_file_upload[]" multiple data-label="Select Documents">

                        </form>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-12 col-sm-3">
            <div class="mt-4 next-div @if(!$docs_to_display) hidden @endif">
                <a href="javascript: void(0)" class="btn btn-primary btn-lg p-3" id="create_envelope_button">Next <i class="fal fa-arrow-right ml-2"></i></a>
            </div>
        </div>

    </div>




    <div class="row">

        <div class="col-12 col-sm-9">

            <div id="uploads_container" class="@if(!$docs_to_display) hidden @endif">

                <div class="p-2 border mt-4">

                    <ul class="list-group" id="uploads_div">

                        @if($docs_to_display)

                            @foreach($docs_to_display as $doc)

                                <li class="list-group-item upload-li" data-file-location="{{ $doc['file_location'] }}" data-document-id="{{ $doc['document_id'] }}" data-file-type="{{ $doc['file_type'] }}" data-file-name="{{ $doc['file_name'] }}" data-template-id="" data-template-applied-id="{{ $doc['template_id'] }}">

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
                                            <div class="ml-3 mr-4 template-status">
                                                @if($doc['file_type'] == 'user')
                                                    <a href="javascript: void(0)" class="btn btn-sm btn-primary show-apply-template-button"><i class="fal fa-plus mr-2 fa-lg"></i> Add Template</a>
                                                @else
                                                    <div class="no-wrap">
                                                        <span class="text-success"><i class="fal fa-check mr-2"></i> <span class="font-8">Template Applied</span></span>
                                                    </div>
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


    </div>

</div>

<input type="hidden" id="is_template" value="{{ $is_template }}">
<input type="hidden" id="Listing_ID" value="{{ $Listing_ID }}">
<input type="hidden" id="Contract_ID" value="{{ $Contract_ID }}">
<input type="hidden" id="Referral_ID" value="{{ $Referral_ID }}">
<input type="hidden" id="transaction_type" value="{{ $transaction_type }}">
<input type="hidden" id="User_ID" value="{{ $User_ID }}">
<input type="hidden" id="Agent_ID" value="{{ $Agent_ID }}">
<input type="hidden" id="document_ids" value="{{ implode(',', $document_ids) }}">

<div class="modal fade draggable" id="add_template_modal" tabindex="-1" role="dialog" aria-labelledby="add_template_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="add_template_modal_title">Add Template</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">

                        <div class="h5 text-gray mb-3">Select the Template you want to apply</div>

                        <div class="table-responsive text-nowrap">

                            <table id="templates_table" class="table table-hover table-bordered table-sm" width="100%">

                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Template Name</th>
                                        <th>Signers</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($templates as $template)

                                        @php
                                        $signers = $template -> signers;
                                        $recipients = [];
                                        foreach($signers as $signer) {
                                            $recipients[] = $signer -> template_role;
                                        }
                                        @endphp
                                        <tr>
                                            <td><button type="button" class="btn btn-sm btn-primary apply-template-button" data-template-id="{{ $template -> id }}"><i class="fa fa-plus mr-2"></i> Apply</button></td>
                                            <td>{{ $template -> template_name }}</td>
                                            <td>{!! implode(', ', $recipients) !!}</td>
                                            <td>{{ date('M jS, Y', strtotime($template -> created_at)) }}</td>
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
