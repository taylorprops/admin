@extends('layouts.main')
@section('title', 'View Bug Report')

@section('content')
<div class="container page-container page-view-bug-report">
    <div class="row">
        <div class="col-12">
            {{ dd($bug_report) }}
        </div>
    </div>
</div>
@endsection
