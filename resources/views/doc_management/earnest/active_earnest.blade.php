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

<div class="modal fade draggable" id="email_agents_missing_earnest_modal" tabindex="-1" role="dialog" aria-labelledby="email_agents_missing_earnest_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="email_agents_missing_earnest_modal_title">Email Agents Missing Earnest</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body py-4 px-5">

                <div class="mb-4">
                    <span class="text-orange font-11">Send email to all agents of selected transactions</span>
                    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Mail Merge Fields" data-content="Any fields enclosed in %% such as %%PropertyAddress%% will be replaced with the actual value when sent."><i class="fad fa-question-circle ml-2"></i></a>
                </div>

                <form id="email_agents_missing_earnest_form">

                    <div class="mb-4">
                        <input type="text" class="custom-form-element form-input required" id="email_agent_earnest_subject" data-label="Subject" value="Missing Earnest Deposit - %%PropertyAddress%%">
                    </div>

                    <div id="email_agent_earnest_message" class="text-editor">
                        Hello %%FirstName%%,<br><br>
                        Our records inidcate that we are holding the earnest deposit for %%PropertyAddress%% however we have not received the deposit yet.
                        <br><br>
                        <span style="color: #900">Earnest Deposits must be received at the main office within 48 hours of contract acceptance date. Failure to comply will result in a fine of $25 per day.</span>
                        <br><br>
                        Please contact the office immediately to help us resolve this isssue.
                        <br><br>
                        {!! session('admin_details') -> signature !!}
                    </div>

                    <input type="hidden" id="contract_ids">

                </form>

            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary btn-lg p-3" id="send_email_agents_missing_earnest_button" data-dismiss"modal"><i class="fad fa-share mr-2"></i> Send Emails</a>
            </div>
        </div>
    </div>
</div>
@endsection
