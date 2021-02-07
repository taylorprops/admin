@extends('layouts.main')
@section('title', 'Edit - '.$file_name)
@section('content')

<div class="container page-container page-fill-fields file-view-container mx-auto p-0">

    <div class="container-1200 mx-auto">

        <div class="row border-bottom">

            <div class="col-12 pl-0">

                <div class="form-options-container w-100 d-flex justify-content-between">

                    <div class="d-flex justify-content-start align-items-center">

                        @if($file_type == 'user')
                        <div class="form-options-div border-left border-right">
                            <a class="text-primary-dark fill-form-option dropdown-toggle" href="javascript: void(0)" id="rotate_form_button" role="button" data-toggle="dropdown"><i class="fad fa-sync-alt fa-lg"></i><br>Rotate</a>
                            <div class="dropdown-menu" aria-labelledby="rotate_form_button">
                                <a class="dropdown-item rotate-form-option" href="javascript: void(0)" data-degrees="90">90&#176; Clockwise</a>
                                <a class="dropdown-item rotate-form-option" href="javascript: void(0)" data-degrees="180">180&#176; Clockwise</a>
                                <a class="dropdown-item rotate-form-option" href="javascript: void(0)" data-degrees="270">270&#176; Clockwise</a>
                            </div>
                        </div>
                        @endif

                        <div class="form-options-div border-left border-right">
                            <a class="text-primary-dark fill-form-option edit-form-action" href="javascript: void(0)" id="add_text_button" data-field-type="user_text"><i class="fad fa-rectangle-wide fa-lg"></i><br>Add Text</a>
                        </div>

                        <div class="form-options-div border-right ">
                            <a class="text-primary-dark fill-form-option edit-form-action" href="javascript: void(0)" id="add_strikeout_button" data-field-type="strikeout"><i class="fad fa-horizontal-rule fa-lg"></i><br>Add Strikeout</a>
                        </div>

                        <div class="form-options-div border-right ">
                            <a class="text-primary-dark fill-form-option edit-form-action" href="javascript: void(0)" id="add_highlight_button" data-field-type="highlight"><i class="fad fa-highlighter fa-lg"></i><br>Highlight</a>
                        </div>

                        <div class="form-options-div ml-5">
                            <button class="btn btn-success fill-form-option" id="save_file_button"><i class="fad fa-save fa-lg"></i><br>Save</button>
                        </div>

                    </div>

                    <div class="alert alert-warning ml-4 mb-0" id="in_process_div">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="mr-4">
                                <i class="fad fa-cog fa-spin fa-lg"></i>
                            </div>
                            <div>
                                Saving disabled unitl the current save process completes in the background. You can continue to edit the fields or close the page. This process takes about 5 seconds per page.
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div id="files_div"></div>

</div><!-- ./ .container -->

<input type="hidden" id="file_id" value="{{ $file_id }}">
<input type="hidden" id="document_id" value="{{ $document_id }}">
<input type="hidden" id="file_type" value="{{ $file_type }}">
<input type="hidden" id="file_name" value="{{ $file -> file_name_display }}">
<input type="hidden" id="Listing_ID" value="{{ $Listing_ID }}">
<input type="hidden" id="Contract_ID" value="{{ $Contract_ID }}">
<input type="hidden" id="Referral_ID" value="{{ $Referral_ID }}">
<input type="hidden" id="transaction_type" value="{{ $transaction_type }}">
<input type="hidden" id="Agent_ID" value="{{ $Agent_ID }}">
<input type="hidden" id="page_width" value="{{ $page_width }}">
<input type="hidden" id="page_height" value="{{ $page_height }}">
<input type="hidden" id="page_size" value="{{ $page_size }}">
<input type="hidden" id="active_page" value="1">
@endsection


