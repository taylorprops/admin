@extends('layouts.main')
@section('title', 'Permissions')

@section('content')

<div class="container page-container page-permissions">

    <div class="row">

        <div class="col-12">

            <div class="h2 text-orange mt-4 mb-2">Permissions</div>

            <hr class="bg-primary p-1">

            @foreach($categories as $category)

                <div class="font-12 text-orange mb-1">{{ $category }}</div>

                <div class="list-group">

                    @foreach($config_options -> where('category', $category) as $permission)

                        <div class="list-group-item p-0 d-flex justify-content-between align-items-center permission-container" data-config-id="{{ $permission -> id }}">

                            <div class="d-flex justify-content-start align-items-center w-70">

                                <div class="list-group-handle text-primary mx-3"><i class="fal fa-bars"></i></div>

                                <div class="font-10 text-primary p-2 @if(session('super_user') == true) permission-text-editor @endif w-35" data-field="title">
                                    {!! $permission -> title !!}
                                </div>
                                <div class="@if(session('super_user') == true) permission-text-editor @endif ml-3 p-2 w-65" data-field="description">
                                    {!! $permission -> description !!}
                                </div>

                            </div>

                            <div class="d-flex justify-content-end align-items-center w-30">

                                <div class="w-80 ml-3">
                                    <select class="custom-form-element form-select emails" multiple data-label="Select Employees">
                                        <option value=""></option>
                                        @foreach($in_house_employees as $employee)
                                            <option value="{{ $employee -> email }}" @if(stristr($permission -> config_value, $employee -> email)) selected @endif >{{ ucwords($employee -> emp_type).' - '.$employee -> first_name.' '.$employee -> last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <button class="btn btn-primary save-config-button" data-config-id="{{ $permission -> id }}" data-type="{{ $permission -> config_type }}"><i class="fa fa-save mr-2"></i> Save</button>
                                </div>

                            </div>

                        </div>

                    @endforeach


                </div>

                <hr class="bg-primary p-1">

            @endforeach

        </div>

    </div>

</div>

@endsection
