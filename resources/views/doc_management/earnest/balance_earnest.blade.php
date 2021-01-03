@extends('layouts.main')
@section('title', 'title')

@section('content')

<div class="container page-balance-earnest pt-5">

    <div class="h2 text-orange mb-4">Pending Earnest Deposits</div>

    <div class="row">

        <div class="col-12 col-md-3">

            <div class="earnest-totals">

                <h4 class="text-primary mb-3">Account Balances</h4>

                <div class="list-group earnest-totals-container" role="tablist"></div>

            </div>

        </div>

        <div class="col-12 col-md-9">

            <div class="earnest-checks-container tab-content">

            </div>

        </div>

    </div>

</div>

@endsection
