@extends('layouts.main')
@section('title', 'Earnest Deposits')

@section('content')
<div class="container page-container page-active-earnest pt-5">

    <div class="h2 text-orange mb-4">Earnest Deposits</div>

    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel earnest-deposit-account" data-label="Select Account">
                <option value="all">All</option>
                @foreach($earnest_accounts as $earnest_account)
                    <option value="{{ $earnest_account -> resource_id }}">{{ $earnest_account -> resource_state.' - '.$earnest_account -> resource_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <ul class="nav nav-tabs" id="earnest_tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="active_tab" data-toggle="tab" href="#active_content" role="tab" aria-controls="active_content" aria-selected="true">Active Deposits</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="missing_tab" data-toggle="tab" href="#missing_content" role="tab" aria-controls="missing_content" aria-selected="false">Missing Deposits</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="waiting_tab" data-toggle="tab" href="#waiting_content" role="tab" aria-controls="waiting_content" aria-selected="false">Waiting For Release</a>
        </li>
    </ul>

    <div class="tab-content pt-5" id="earnest_tabs_content">

        <div class="mb-4 email-agents-div d-none">
            <a href="javascript:void(0)" class="btn btn-primary btn-lg p-3 email-agent"><i class="fal fa-envelope mr-2"></i> Email Selected Agents</a>
        </div>

        <div class="tab-pane fade show active" id="active_content" role="tabpanel" aria-labelledby="active_tab"></div>

        <div class="tab-pane fade" id="missing_content" role="tabpanel" aria-labelledby="missing_tab"></div>

        <div class="tab-pane fade" id="waiting_content" role="tabpanel" aria-labelledby="waiting_tab"></div>

    </div>

</div>
@endsection
