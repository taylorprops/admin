@extends('layouts.main')
@section('title', 'E-Sign - Add Signers')

@section('content')

<div class="container-1000 page-container mt-5 mx-auto page-esign-templates">

    <div class="h2 text-primary">E-Sign - Create Template</div>

    @if($template_type == 'system')
        <div class="h4 text-orange my-4">{{ $template -> template_name }}</div>
    @endif

    @if($template_type != 'system')
        <div class="row my-5">

            <div class="col-12 col-sm-6">

                <form id="upload_form" enctype="multipart/form-data">

                    <div class="font-10 text-orange">Template Name</div>
                    <input type="text" class="custom-form-element form-input" id="template_name" name="template_name" value="{{ $template_name }}" data-label="Enter Template Name">

                    @if(!$template)
                        <div class="font-10 text-orange mt-4">Upload Document For Template</div>
                        <input type="file" class="custom-form-element form-input-file" id="template_upload" name="template_upload" accept=".pdf" data-label="Select Document">
                    @endif

                </form>

            </div>

        </div>
    @endif

    <div class="font-10 text-orange">Add Signers</div>
    <div class="row">

        <div class="col-12 col-sm-5">

            <div class="available-signers font-9">

                <div class="text-gray">Available Signers</div>

                <div class="list-group available-signers-list">

                    @foreach($signer_options as $signer_option)

                        @php
                        $active = '';
                        $checked = '';

                        if($signers) {
                            if(in_array($signer_option -> resource_name, $signers)) {
                                $active = 'active';
                                $checked = 'checked';
                            }
                        }
                        @endphp

                        <div class="list-group-item list-group-item-action d-flex justify-content-start align-items-center {{ $active }}">
                            <input type="checkbox" class="custom-form-input form-checkbox available-signers-checkbox" {{ $checked }} id="signer_{{ $signer_option -> resource_id }}" value="{{ $signer_option -> resource_name }}" data-id="{{ $signer_option -> resource_id }}" data-label="">
                            <label class="custom my-0" for="signer_{{ $signer_option -> resource_id }}">{{ $signer_option -> resource_name }}</label>
                        </div>

                    @endforeach

                </div>

            </div>

        </div>

        <div class="col-0 col-sm-1"></div>

        <div class="col-12 col-sm-5">

            <div class="selected-signers font-9">

                <div class="text-gray">Selected Signers</div>

                <div class="list-group selected-signers-list">

                    @foreach($signer_options as $signer_option)

                        @php
                        $hidden = 'hidden';
                        $selected = '';

                        if($signers) {
                            if(in_array($signer_option -> resource_name, $signers)) {
                                $hidden = '';
                                $selected = 'selected';
                            }
                        }
                        @endphp

                        <div class="list-group-item selected-signer-item border-top {{ $hidden }} {{ $selected }}" data-id="{{ $signer_option -> resource_id }}" data-role="{{ $signer_option -> resource_name }}">

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="mr-3">
                                        <a href="javascript: void(0)" class="signer-handle"><i class="fal fa-bars fa-lg"></i></a>
                                    </div>
                                    <div>
                                        {{ $signer_option -> resource_name }}
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-danger remove-signer-button" data-id="{{ $signer_option -> resource_id }}"><i class="fal fa-times"></i></button>
                                </div>
                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    </div>

    <div class="row">

        <div class="col-12">

            <hr>

        </div>

    </div>

    <div class="row">

        <div class="col-12">

            <div class="d-flex justify-content-around align-items-center mt-3">
                <button class="btn btn-primary btn-lg py-3 px-5" id="save_template_button" disabled>Next <i class="fal fa-arrow-right ml-2"></i></button>
            </div>

        </div>

    </div>


</div>

<input type="hidden" id="template_type" value="{{ $template_type }}">
<input type="hidden" id="template_id" value="{{ $template_id }}">

@endsection
