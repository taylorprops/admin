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

            <div class="no-wrap">

                <table id="" class="table table-hover table-sm" width="100%">

                    <tbody>
                        <tr>
                            <td width="120">Date</td>
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
                            <td colspan="2"><img src="{{ $bug_report -> image_location }}" width="100%"></td>
                        </tr>

                    </body>

                </table>

            </div>

        </div>

    </div>

</div>
@endsection
