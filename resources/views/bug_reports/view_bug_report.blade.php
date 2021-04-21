@extends('layouts.main')
@section('title', 'View Bug Report')

@section('content')
<div class="container page-container page-view-bug-report">

    <div class="row my-5">
        <div class="col-12">
            <div class="h2 text-primary">Bug Report</div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-start align-items-center mb-3">
                <div>
                    <button class="btn btn-primary" id="email_response_button" data-user-name="{{ $bug_report -> user -> name }}" data-user-first-name="{{ $bug_report -> user -> first_name }}" data-user-email="{{ $bug_report -> user -> email }}" data-message="{{ $bug_report -> message }}"><i class="fal fa-envelope mr-2"></i> Email User</button>
                </div>
                <div>

                    @php
                    $hidden_active = $bug_report -> active == 'yes' ? '' : 'hidden';
                    $hidden_not_active = $bug_report -> active == 'yes' ? 'hidden' : '';
                    @endphp

                    <button class="btn btn-success mark-resolved-button active-option {{ $hidden_active }}" data-action="no" data-id="{{ $bug_report -> id }}"><i class="fal fa-check mr-2"></i> Mark Resolved</button>

                    <span class="text-success font-11 ml-3 not-active-option {{ $hidden_not_active }}">Resolved</span>

                    <a href="javascript:void(0)" class="mark-resolved-button not-active-option ml-3 {{ $hidden_not_active }}" data-action="yes" data-id="{{ $bug_report -> id }}"><i class="fal fa-undo mr-1"></i> Undo</a>

                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">

        <div class="col-12">

            <table id="" class="table table-hover table-sm text-gray">

                <tbody>
                    <tr>
                        <td width="140">Date</td>
                        <td>{{ $bug_report -> created_at }}</td>
                    </tr>
                    <tr>
                        <td>Reported By</td>
                        <td>{{ $bug_report -> user -> name }}</td>
                    </tr>
                    <tr>
                        <td>Message</td>
                        <td>{{ nl2br($bug_report -> message) }}</td>
                    </tr>
                    <tr>
                        <td>URL</td>
                        <td><a href="{{ $bug_report -> url }}" target="_blank">{{ $bug_report -> url }}</a></td>
                    </tr>

                    <tr>
                        <td colspan="2" class="font-10 pt-5">Device Details</td>
                    </tr>

                    @foreach($browser_info as $key => $value)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ $value }}</td>
                        </tr>
                    @endforeach


                </body>

            </table>

        </div>

    </div>

    <div class="row mt-3">
        <div class="col-12">

            <div class="w-100">
                <img src="{{ $bug_report -> image_location }}" width="100%">
            </div>

        </div>
    </div>

</div>
@endsection
