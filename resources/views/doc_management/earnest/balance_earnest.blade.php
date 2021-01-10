@extends('layouts.main')
@section('title', 'Balance Earnest Deposits')

@section('content')

<div class="container page-container page-balance-earnest pt-5">

    <div class="h2 text-orange mb-4">Pending Earnest Deposits</div>

    <div class="row">

        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">

            <div class="earnest-key">

                <div class="earnest-search">

                    <h4 class="text-primary mb-3">Search All Deposits</h4>

                    <div class="earnest-search-div">

                        <input type="text" class="custom-form-element form-input" id="earnest_search_input" data-label="Search by Address, Agent or Check Details">

                        <div class="relative">
                            <div id="earnest_search_results" class="shadow"></div>
                        </div>

                    </div>

                </div>

                <div class="earnest-totals mt-5">

                    <h4 class="text-primary mb-3">Account Balances</h4>

                    <div class="list-group earnest-totals-container" role="tablist"></div>

                </div>

            </div>

        </div>

        <div class="col-12 col-xl-9">

            <div class="earnest-checks-container tab-content">

            </div>

        </div>

    </div>

</div>

@endsection
