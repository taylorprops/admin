@extends('layouts.main')
@section('title', 'E-Sign - Add Template Fields')

@section('content')

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

                <div class="d-flex justify-content-start align-items-center w-100">

                    <div class="mr-3">
                        <button class="btn btn-primary edit-signers-button" href="javascript:void(0)"><i class="fad fa-users mr-2"></i> Edit Signers</button>
                    </div>
                    <div class="mr-3">
                        <button class="btn btn-success fill-form-option font-11" id="save_template_button">Save Template <i class="fad fa-save ml-2"></i></button>
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

                        @php
                        $total_pages = count($images);
                        @endphp

                        @foreach($images as $image)

                            @php
                            $c = $image -> page_number;
                            $page_id = $image -> id.'_'.$c;
                            $active = $loop -> first ? 'active' : '';
                            $height = $image -> height;
                            $width = $image -> width;
                            if($height > 792) {
                                $perc = 792 / $height;
                                $height = $height * $perc;
                                $width = $width * $perc;
                            }
                            @endphp

                            <div class="text-primary bg-blue-light pl-4 p-2" id="page_{{ $page_id }}">
                                <div class="d-flex justify-content-around align-items-center font-10">
                                    <div>
                                        {{ $template_name }}
                                    </div>
                                    <div class="ml-5 no-wrap">
                                        Page <?php echo $c.' of '.$total_pages; ?>
                                    </div>
                                </div>

                            </div>

                            <div class="file-view-page-container border border mx-auto {{ $active }}" data-page="{{ $c }}" data-page-id="{{ $page_id }}" data-image-id="{{ $image -> id }}" style="height: {{ $height * 1.2 }}pt; width: {{ $width * 1.2 }}pt" data-height="{{ $height }}" data-width="{{ $width }}">
                                <div class="fields-container w-100 h-100">

                                    <img class="file-image-bg w-100 h-100" src="{{ $image -> file_location }}?r={{ date('YmdHis') }}">

                                    @foreach($fields as $field)

                                        @if($field -> page == $c)

                                            @php
                                            // get signer from relationship
                                            $field_type = $field -> field_type;
                                            $signer_role = '';
                                            $field_value = '';
                                            $text_class = '';
                                            if($field_type != 'text') {
                                                $signer_role = $field -> signer_role;
                                            } else {
                                                $field_value = $field -> field_value;
                                            }


                                            if($field_type == 'signature') {
                                                $field_div_html = '<div class="field-div-details"><i class="fad fa-signature mr-2"></i> <span class="field-div-name">'.$signer_role.'</span></div>';
                                            } else if($field_type == 'initials') {
                                                $initials = get_initials($signer_role);
                                                $field_div_html = '<span class="field-div-name">'.$initials.'</span>';
                                            } else if($field_type == 'date') {
                                                $field_div_html = '<div class="field-div-details"><i class="fad fa-calendar ml-1 mr-2"></i>  <span class="field-div-name">'.$signer_role.'</span></div>';
                                            } else if($field_type == 'name') {
                                                $field_div_html = '<div class="field-div-details"><span class="field-div-name">'.$signer_role.'</span></div>';
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
                                                data-template-id="{{ $field -> template_id }}">
                                                <div class="field-html {{ $text_class }} w-100">{!! $field_div_html !!}</div>
                                                <div class="field-options-holder">
                                                    <div class="d-flex justify-content-around">
                                                        <div class="btn-group field-options-group" role="group" aria-label="Field Options">
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
                                                            @foreach($signers as $signer)
                                                                <option class="signer-select-option"
                                                                value="{{ $signer -> signer_role }}"
                                                                @if($signer -> signer_role == $field -> signer_role) selected @endif
                                                                >{{ $signer -> signer_role }}
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


                        @endforeach

                    </div> <!-- ende file_viewer -->

                </div> <!-- end file-viewer-container -->

            </div>



            <div class="col-2 thumbs-div">

                <div class="file-view animate__animated animate__fadeIn pt-2 border-right" id="thumb_viewer">

                    <div class="text-primary small border-top py-2 text-center">{{ $template_name }}</div>

                    @foreach($images as $image)

                        @php
                        $c = $image -> page_number;
                        $page_id = $image -> id.'_'.$c;
                        $active = $loop -> first ? 'active' : '';
                        @endphp
                        <div class="file-view-thumb-container mb-2 mx-auto {{ $active }}" id="thumb_{{ $page_id }}" data-id="{{ $page_id }}">
                            <div class="file-view-thumb">
                                <a href="javascript: void(0)"><img class="file-thumb w-100 h-100" src="{{ $image -> file_location }}?r={{ date('YmdHis') }}"></a>
                            </div>
                            <div class="file-view-thumb-footer text-center mb-1">
                                Page {{ $c }}
                            </div>
                        </div>
                    @endforeach

                </div>

            </div>

        </div>

    </div>

</div>

<div class="hidden" id="signer_options_template_html">{!! implode(' ', $signer_options_template) !!}</div>
<input type="hidden" id="active_signer" value="">
<input type="hidden" id="template_id" value="{{ $template_id }}">
<input type="hidden" id="active_page" value="1">




@endsection
