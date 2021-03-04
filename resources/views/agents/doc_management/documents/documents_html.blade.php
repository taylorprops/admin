@extends('layouts.main')
@section('title', 'Forms')

@section('content')

<div class="container-1100 page-container page-documents mx-auto">

    <div class="h2 text-orange my-4">Forms</div>

    <div class="row mb-3">
        <div class="col-12 col-sm-6 col-lg-4">
            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel form-group-select" data-label="Select Form Group">
                <option value="0">All</option>
                @foreach($form_groups as $form_group)
                    <option value="{{ $form_group -> resource_id }}">{{ $form_group -> resource_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">

        <div class="col-12">

            <div id="forms_table_div"></div>

        </div>

    </div>

</div>

@endsection
