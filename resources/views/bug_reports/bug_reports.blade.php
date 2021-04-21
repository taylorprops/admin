@extends('layouts.main')
@section('title', 'Bug Reports')

@section('content')

<div class="container page-container page-bug-reports">

    <div class="row">
        <div class="col-12">
            <div class="h2 text-primary my-5">Bug Reports</div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="no-wrap">

                <table id="bug_report_table" class="table table-hover table-sm" width="100%">

                    <thead>
                        <tr>
                            <th></th>
                            <th>Reported By</th>
                            <th>Message</th>
                            <th>Submitted</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($bug_reports as $bug_report)

                        <tr>
                            <td width="120">
                                <a href="/bug_reports/view_bug_report/{{ $bug_report -> id }}" class="btn btn-primary">View Bug Report</a>
                            </td>
                            <td>{{ $bug_report -> user -> name }}</td>
                            <td>{{ $bug_report -> message }}</td>
                            <td>{{ $bug_report -> created_at }}</td>
                            <td>{!! $bug_report -> active == 'yes' ? '<span class="text-danger"><i class="fal fa-exclamation-circle mr-2"></i> Active</span>' : '<span class="text-green"><i class="fal fa-check mr-2"></i> Resolved</span>' !!}</td>
                        </tr>

                        @endforeach
                    </body>

                </table>

            </div>
        </div>
    </div>

</div>
@endsection
