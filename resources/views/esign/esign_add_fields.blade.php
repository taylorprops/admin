@extends('layouts.main')
@section('title', 'E-Sign - Add Fields')

@section('content')

@if($error && $error == 'sent')
<script>window.location = "/esign";</script>
@endif
<div class="container-full ml-0 page-esign-add-fields">

    <div class="row border-bottom">

        <div class="col-12">

            <div class="form-options-container w-100 d-flex justify-content-start align-items-center pr-5">

                <div class="d-flex justify-content-start align-items-center">

                    <div class="form-options-div border-left border-right">
                        <a class="text-primary-dark fill-form-option edit-form-action" href="javascript: void(0)" id="add_signature_button" data-field-type="signature"><i class="fad fa-signature fa-lg"></i><br>Add Signature</a>
                    </div>

                    <div class="form-options-div border-right">
                        <a class="text-primary-dark fill-form-option edit-form-action relative" href="javascript: void(0)" id="add_highlight_button" data-field-type="initials">
                            <span class="initials-one">R</span>
                            <span class="initials-two">T</span>
                            <br>
                            Add Initials
                        </a>
                    </div>

                    <div class="form-options-div border-right ">
                        <a class="text-primary-dark fill-form-option edit-form-action" href="javascript: void(0)" id="add_name_button" data-field-type="name"><span class="font-italic"> Signer Name</span><br>Add Name</a>
                    </div>

                    <div class="form-options-div border-right ">
                        <a class="text-primary-dark fill-form-option edit-form-action" href="javascript: void(0)" id="add_date_button" data-field-type="date"><i class="fad fa-calendar fa-lg"></i><br>Add Date</a>
                    </div>

                    <div class="form-options-div border-right ">
                        <a class="text-primary-dark fill-form-option edit-form-action" href="javascript: void(0)" id="add_text_button" data-field-type="text"><i class="fad fa-pencil fa-lg"></i><br>Add Text</a>
                    </div>

                </div>

                <div class="d-flex justify-content-between align-items-center w-100">

                    <div>

                        <div class="mr-3">
                            @if($is_draft == 'yes')
                                <button class="btn btn-primary fill-form-option" id="save_as_draft_button">Save Changes To Draft <i class="fad fa-save ml-2"></i></button>
                            @else
                                <button class="btn btn-primary btn-sm fill-form-option" id="save_as_draft_button">Save As Draft <i class="fad fa-file-edit ml-2"></i></button>
                            @endif
                        </div>

                    </div>

                    <div>

                        <div class="mr-5">
                            <button class="btn btn-success fill-form-option font-11" id="send_for_signatures_button">Send for Signatures <i class="fad fa-share-all ml-2"></i></button>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="container w-100 ml-0 pl-0 animate__animated animate__fadeIn">

        <div class="row">

            <div class="col-12 col-xl-10 px-0">

                <div class="file-viewer-container border-right mx-auto">

                    <div class="file-view animate__animated animate__fadeIn" id="file_viewer">

                        @foreach($documents as $document)

                            @php
                            $images = $document -> images;
                            $total_pages = count($images);
                            $active = $loop -> first ? 'active' : '';
                            @endphp

                            @foreach($images as $image)

                                @php
                                $c = $image -> page_number;
                                $page_id = $document -> id.'_'.$c;
                                $fields = $document -> fields;
                                @endphp

                                <div class="text-primary bg-blue-light pl-4 p-2" id="page_{{ $page_id }}">
                                    <div class="d-flex justify-content-around align-items-center">
                                        <div>
                                            {{ $document -> file_name }}
                                        </div>
                                        <div class="ml-5 no-wrap">
                                            Page <?php echo $c.' of '.$total_pages; ?>
                                        </div>
                                    </div>

                                </div>
                                <div class="file-view-page-container border border mx-auto {{ $active }}" data-page="{{ $c }}" data-id="{{ $page_id }}" data-document-id="{{ $document -> id }}" style="height: {{ $document -> height * 1.2 }}pt; width: {{ $document -> width * 1.2 }}pt" data-height="{{ $document -> height }}" data-width="{{ $document -> width }}">
                                    <div class="fields-container w-100 h-100">

                                        <img class="file-image-bg w-100 h-100" src="{{ $image -> image_location }}?r={{ date('YmdHis') }}">

                                        @foreach($fields as $field)

                                            @if($field -> page == $c)

                                                @php
                                                // get signer from relationship
                                                $field_type = $field -> field_type;
                                                $field_signer = '';
                                                $signer_name = '';
                                                $field_value = '';
                                                $text_class = '';
                                                if($field_type != 'text') {
                                                    $field_signer = $field -> signer;
                                                    $signer_name = $field_signer -> signer_name;
                                                } else {
                                                    $field_value = $field -> field_value;
                                                }


                                                if($field_type == 'signature') {
                                                    $field_div_html = '<div class="field-div-details"><i class="fad fa-signature mr-2"></i> <span class="field-div-name">'.$signer_name.'</span></div>';
                                                } else if($field_type == 'initials') {
                                                    $initials = get_initials($signer_name);
                                                    $field_div_html = '<span class="field-div-name">'.$initials.'</span>';
                                                } else if($field_type == 'date') {
                                                    $field_div_html = '<div class="field-div-details"><i class="fad fa-calendar ml-1 mr-2"></i>  <span class="field-div-name">'.$signer_name.'</span></div>';
                                                } else if($field_type == 'name') {
                                                    $field_div_html = '<div class="field-div-details"><span class="field-div-name">'.$signer_name.'</span></div>';
                                                } else if($field_type == 'text') {
                                                    $field_div_html = '<div class="field-div-details"><span class="field-div-name">'.$field_value.'</span></div>';
                                                    $text_class = 'text';
                                                }

                                                @endphp

                                                <div class="field-div @if($field -> required == '1') required @endif" style="position: absolute; top: {{ $field -> top_perc }}%; left: {{ $field -> left_perc }}%; height: {{ $field -> height_perc }}%; width: {{ $field -> width_perc }}%;"
                                                    id="field_{{ $field -> field_id }}"
                                                    data-field-id="{{ $field -> field_id }}"
                                                    data-field-type="{{ $field -> field_type }}"
                                                    data-page="{{ $field -> page }}"
                                                    data-document-id="{{ $field ->  document_id }}">
                                                    <div class="field-html {{ $text_class }} w-100">{!! $field_div_html !!}</div>
                                                    <div class="field-options-holder">
                                                        <div class="d-flex justify-content-around">
                                                            <div class="btn-group" role="group" aria-label="Field Options">
                                                                <a type="button" class="btn btn-primary field-handle ml-0 pt-2"><i class="fal fa-arrows fa-lg"></i></a>
                                                                <a type="button" class="btn btn-danger remove-field pt-2"><i class="fad fa-times-circle fa-lg"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="select-signer-div p-2">

                                                        <div class="font-10 text-yellow">{{ ucwords($field -> field_type) }}</div>

                                                        @if($field -> field_type != 'text')
                                                            <div class="font-9">
                                                                <input type="checkbox" class="custom-form-element form-checkbox signature-required" value="1" @if($field -> required == '1') checked @endif data-label="Required">
                                                            </div>

                                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel signer-select" data-connector-id="{{ $field -> field_id }}">
                                                                @foreach($signers as $signer_option)
                                                                    <option class="signer-select-option"
                                                                    value="{{ $signer_option -> signer_name }}"
                                                                    data-role="{{ $signer_option -> signer_role }}"
                                                                    data-name="{{ $signer_option -> signer_name }}"
                                                                    data-signer-id="{{ $signer_option -> id }}"
                                                                    @if($signer_option -> id == $field_signer -> id) selected @endif
                                                                    >{{ $signer_option -> signer_name }} - {{ $signer_option -> template_role }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                        @else

                                                            <input type="hidden" class="signature-required" value="0">
                                                            <input type="text" class="custom-form-element form-input text-input" value="{{ $field -> field_value }}" data-label="Enter Text">

                                                        @endif

                                                    </div>
                                                    <div class="field-handle ui-resizable-handle ui-resizable-nw"></div>
                                                    <div class="field-handle ui-resizable-handle ui-resizable-ne"></div>
                                                    <div class="field-handle ui-resizable-handle ui-resizable-se"></div>
                                                    <div class="field-handle ui-resizable-handle ui-resizable-sw"></div>
                                                </div>

                                            @endif

                                        @endforeach

                                    </div>
                                </div>
                                {{-- <div class="text-primary bg-blue-light p-2 mb-2 d-flex justify-content-end">
                                    End Page {{ $c }}
                                </div> --}}

                            @endforeach

                        @endforeach

                    </div> <!-- ende file_viewer -->

                </div> <!-- end file-viewer-container -->

            </div>



            <div class="col-2 thumbs-div">

                <div class="file-view animate__animated animate__fadeIn pt-2 border-right" id="thumb_viewer">

                    @foreach($documents as $document)

                    <div class="text-primary small @if(!$loop -> first) border-top @endif py-2 text-center">{{ $document -> file_name }}</div>

                    @php
                    $images = $document -> images;
                    $active_doc = $loop -> first ? 'active' : '';
                    @endphp

                        @foreach($images as $image)

                            @php
                            $c = $image -> page_number;
                            $page_id = $document -> id.'_'.$c;
                            if($active_doc != '') {
                                $active = '';
                                if($loop -> first) {
                                    $active = 'active';
                                }
                            }
                            @endphp
                            <div class="file-view-thumb-container mb-2 mx-auto {{ $active }}" id="thumb_{{ $page_id }}" data-id="{{ $page_id }}">
                                <div class="file-view-thumb">
                                    <a href="javascript: void(0)"><img class="file-thumb w-100 h-100" src="{{ $image -> image_location }}?r={{ date('YmdHis') }}"></a>
                                </div>
                                <div class="file-view-thumb-footer text-center mb-1">
                                    Page {{ $c }}
                                </div>
                            </div>
                        @endforeach

                    @endforeach

                </div>

            </div>

        </div>

    </div>

</div>

<div class="hidden" id="signer_options_html">{!! implode(' ', $signers_options) !!}</div>

<input type="hidden" id="saved_draft_name" value="{{ $draft_name }}">
<input type="hidden" id="envelope_id" value="{{ $envelope_id }}">
<input type="hidden" id="active_page" value="1">
@php
    $active_signer = 'Seller One';
@endphp
<input type="hidden" id="active_signer" value="{{ $active_signer }}">
<input type="hidden" id="property_address" value="{{ $property_address }}">



<div class="modal fade draggable" id="template_modal" tabindex="-1" role="dialog" aria-labelledby="template_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="template_modal_title">Save Template</h4>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">

                <div class="d-flex justify-content-start align-items-center mb-3">
                    <div><i class="fad fa-info-circle fa-lg mr-3 text-primary"></i> </div>
                    <div class="text-8 text-gray">
                        Templates are used to automatically add all signature fields to a particular document. They will include all signer roles and fields you have added.
                    </div>
                </div>
                <hr class="my-4">

                <form id="template_form">
                    <div class="text-gray mb-4">Enter a name for the Template</div>
                    <input type="text" class="custom-form-element form-input required" id="template_name" data-label="Template Name">
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary" id="save_template_button" data-dismiss="modal"><i class="fad fa-save mr-2"></i> Save Template</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="draft_modal" tabindex="-1" role="dialog" aria-labelledby="draft_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="draft_modal_title">Save Draft</h4>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-start align-items-center mb-3">
                    <div><i class="fad fa-info-circle fa-lg mr-3 text-primary"></i> </div>
                    <div class="text-8 text-gray">
                        Drafts can be saved to use at a later time. They will include all documents, signers and fields you have added.
                    </div>
                </div>
                <hr class="my-4">
                <form id="draft_form">
                    <div class="text-gray mb-4">Enter a name for the Draft</div>
                    <input type="text" class="custom-form-element form-input required" id="draft_name" data-label="Draft Name">
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary" id="save_draft_button" data-dismiss="modal"><i class="fad fa-save mr-2"></i> Save Draft</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="send_for_signatures_modal" tabindex="-1" role="dialog" aria-labelledby="send_for_signatures_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="send_for_signatures_modal_title">Send For Signatures</h4>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">
                <form id="send_for_signatures_form">

                    <div class="h5 text-gray mb-4">Email Details</div>

                    <input type="text" class="custom-form-element form-input required mb-3" id="envelope_subject" data-label="Subject">

                    <textarea class="custom-form-element form-textarea required" rows="5" id="envelope_message" data-label="Message"></textarea>

                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-lg btn-primary" id="save_send_for_signatures_button">Send <i class="fad fa-share-all ml-2"></i></a>
            </div>
        </div>
    </div>
</div>
@endsection
