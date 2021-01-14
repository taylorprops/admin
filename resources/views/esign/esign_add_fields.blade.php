@extends('layouts.main')
@section('title', 'Esign - Add Fields')

@section('content')
<div class="container-1350 mx-auto page-esign-add-fields">

    <div class="row border-bottom">

        <div class="col-12">

            <div class="form-options-container w-100 d-flex justify-content-between align-items-center">

                <div class="d-flex justify-content-start align-items-center">

                    <div class="form-options-div border-left border-right">
                        <a class="text-primary-dark fill-form-option edit-form-action" href="javascript: void(0)" id="add_signature_button" data-field-type="signature"><i class="fal fa-signature fa-lg"></i><br>Add Signature</a>
                    </div>

                    <div class="form-options-div border-right ">
                        <a class="text-primary-dark fill-form-option edit-form-action" href="javascript: void(0)" id="add_date_button" data-field-type="date"><i class="fal fa-calendar fa-lg"></i><br>Add Date</a>
                    </div>

                    <div class="form-options-div border-right">
                        <a class="text-primary-dark fill-form-option edit-form-action relative" href="javascript: void(0)" id="add_highlight_button" data-field-type="initials">
                            <span class="initials-one">R</span>
                            <span class="initials-two">T</span>
                            <br>
                            Add Initials
                        </a>
                    </div>

                </div>

                <div class="mr-4">
                    <button class="btn btn-success fill-form-option font-11" id="send_for_signatures_button">Send for Signatures <i class="fal fa-share-all ml-2"></i></button>
                </div>

            </div>

        </div>

    </div>

    <div class="container-1350 mx-auto animate__animated animate__fadeIn">

        <div class="row">

            <div class="col-12 col-xl-10 pr-xl-0 mx-auto">

                <div class="file-viewer-container border-right w-100">

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
                                @endphp

                                <div class="h5 bg-primary p-2 text-center mb-0" id="page_{{ $page_id }}">
                                    <span class="badge text-white font-10">Page <?php echo $c.' of '.$total_pages; ?></span>
                                </div>
                                <div class="file-view-page-container border border-primary w-100 {{ $active }}" data-page="{{ $c }}" data-id="{{ $page_id }}" data-document-id="{{ $document -> id }}">
                                    <div class="fields-container w-100 h-100">

                                        <img class="file-image-bg w-100 h-100" src="{{ $image -> image_location }}?r={{ date('YmdHis') }}">

                                    </div>
                                </div>
                                <div class="h5 text-white bg-primary p-2 mb-1 text-center">
                                    <span class="badge">End Page {{ $c }}</span>
                                </div>

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

<input type="hidden" id="envelope_id" value="{{ $envelope_id }}">
<input type="hidden" id="active_page" value="1">
<input type="hidden" id="active_signer" value="">

<div class="modal fade draggable" id="send_for_signatures_modal" tabindex="-1" role="dialog" aria-labelledby="send_for_signatures_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="send_for_signatures_modal_title">Send For Signatures</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="send_for_signatures_form">

                    <div class="h5 text-gray mb-4">Email Details</div>

                    <input type="text" class="custom-form-element form-input required mb-3" id="envelope_subject" data-label="Subject">

                    <textarea class="custom-form-element form-textarea required" rows="8" id="envelope_message" data-label="Message"></textarea>

                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-lg btn-success" id="save_send_for_signatures_button" data-dismiss"modal">Send <i class="fal fa-share-all ml-2"></i></a>
            </div>
        </div>
    </div>
</div>
@endsection
