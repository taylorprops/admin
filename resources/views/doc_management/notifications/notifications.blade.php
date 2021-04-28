@extends('layouts.main')
@section('title', 'Notifications')

@section('content')

<div class="container page-container page-notifications">

    <div class="row">

        <div class="col-12">

            <div class="h2 text-orange mt-4 mb-2">Notification Settings</div>

            <hr class="bg-primary p-1">

            @foreach($categories as $category)

                <div class="font-12 text-orange mb-1">{{ $category }}</div>

                <div class="list-group">

                    @foreach($config_options -> where('category', $category) as $notification)

                        <div class="list-group-item p-0 d-flex justify-content-between align-items-center notification-container" data-config-id="{{ $notification -> id }}">

                            <div class="d-flex justify-content-start align-items-center w-60">

                                <div class="list-group-handle text-primary mx-3"><i class="fal fa-bars"></i></div>

                                <div class="font-10 text-primary p-2 @if(session('super_user') == true) notification-text-editor @endif w-35" data-field="title">
                                    {!! $notification -> title !!}
                                </div>

                                <div class="w-65 ml-3">
                                    <div class="@if(session('super_user') == true) notification-text-editor @endif p-2" data-field="description">
                                        {!! $notification -> description !!}
                                    </div>
                                    @if(session('super_user'))
                                    <span class="small ml-2">{{ $notification -> config_key }}</span>
                                    @endif
                                </div>

                            </div>

                            <div class="d-flex justify-content-end align-items-center w-40">

                                @if($notification -> config_role == 'notification')

                                    <div class="w-20 notify-by-options text-gray">
                                        <input type="checkbox" class="custom-form-element form-checkbox notify-checkbox-email" data-label="Email" value="yes" @if($notification -> notify_by_email == 'yes') checked @endif>
                                        <input type="checkbox" class="custom-form-element form-checkbox notify-checkbox-text" data-label="Text SMS" value="yes" @if($notification -> notify_by_text == 'yes') checked @endif>
                                    </div>

                                @endif

                                @if($notification -> config_type == 'notification')

                                    <div class="w-60 ml-3">
                                        <select class="custom-form-element form-select emails" multiple data-label="Select Recipients">
                                            <option value=""></option>
                                            @foreach($in_house_employees as $employee)
                                                @php
                                                $user_emails = explode(',', $notification -> config_value);
                                                @endphp
                                                <option value="{{ $employee -> user_account -> email }}" @if(in_array($employee -> user_account -> email, $user_emails)) selected @endif >{{ ucwords($employee -> emp_type).' - '.$employee -> first_name.' '.$employee -> last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                @elseif($notification -> config_type == 'on_off')

                                    <div class="w-20">
                                        <select class="custom-form-element form-select form-select-no-cancel on-off" data-label="Select Option">
                                            <option value="on" @if($notification -> config_value == 'on') selected @endif >On</option>
                                            <option value="off" @if($notification -> config_value == 'off') selected @endif >Off</option>
                                        </select>
                                    </div>

                                @elseif($notification -> config_type == 'number')

                                    <div class="w-20">
                                        <input type="text" class="custom-form-element form-input number text-center pr-1" value="{{ $notification -> config_value }}" data-label="">
                                    </div>

                                @endif

                                <div>
                                    <button class="btn btn-primary save-config-button" data-config-id="{{ $notification -> id }}" data-type="{{ $notification -> config_type }}"><i class="fa fa-save mr-2"></i> Save</button>
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
