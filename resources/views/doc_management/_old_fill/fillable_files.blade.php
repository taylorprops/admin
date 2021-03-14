@extends('layouts.main')
@section('title', 'Fillable Files')
@section('content')

<div class="container page-container page-files mt-5">
    <div class="row">
        <div class="col-12">
            @foreach ($files as $file)
            <div class="alert alert-primary" role="alert">
                {{ $file -> file_name_orig }}
                <div class="float-right">
                    <a href="/doc_management/create/fill_fields/{{ $file -> file_id }}">Fill Fields</a>
                </div>
            </div>

            @endforeach
        </div>
    </div><!-- ./ .row -->
</div><!-- ./ .container -->

@endsection
