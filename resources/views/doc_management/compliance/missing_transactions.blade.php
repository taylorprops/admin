@extends('layouts.main')
@section('title', 'Missing Transactions')

@section('content')
<div class="container page-container page-missing-transactions">

    <div class="h2 text-primary my-5">Missing Transactions</div>

    <ul class="nav nav-tabs" id="missing_transactions_tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link missing-nav-link active" id="missing_listings_tab" data-type="listings" data-toggle="tab" href="#missing_listings" role="tab" aria-controls="missing_listings" aria-selected="true">Missing Listings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link missing-nav-link" id="missing_contracts_tab" data-type="contracts" data-toggle="tab" href="#missing_contracts" role="tab" aria-controls="missing_contracts" aria-selected="false">Missing Contracts</a>
        </li>
        <li class="nav-item">
            <a class="nav-link missing-nav-link" id="missing_contracts_our_listing_tab" data-type="contracts_our_listing" data-toggle="tab" href="#missing_contracts_our_listing" role="tab" aria-controls="missing_contracts_our_listing" aria-selected="false">Missing Contracts - Our Listing</a>
        </li>
    </ul>

    <div class="tab-content pt-5" id="missing_transactions_content">

        <div class="mb-4 email-agents-div hide-me">
            <a href="javascript:void(0)" class="btn btn-primary btn-lg p-3 email-agent"><i class="fal fa-envelope mr-2"></i> Email Selected Agents</a>
        </div>

        <div class="tab-pane fade show active" id="missing_listings"></div>
        <div class="tab-pane fade" id="missing_contracts"></div>
        <div class="tab-pane fade" id="missing_contracts_our_listing"></div>

    </div>


</div>

<div class="modal fade draggable" id="email_agents_missing_transactions_modal" tabindex="-1" role="dialog" aria-labelledby="email_agents_missing_transactions_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="email_agents_missing_transactions_modal_title">Email Agents Missing Earnest</h4>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body py-4 px-5">

                <div class="mb-4">
                    <span class="text-orange font-11">Send email to all agents of selected transactions</span>
                    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Mail Merge Fields" data-content="Any fields enclosed in %% such as %%PropertyAddress%% will be replaced with the actual value when sent."><i class="fad fa-question-circle ml-2"></i></a>
                </div>

                <form id="email_agents_missing_transactions_form">

                    <div class="mb-4">
                        <input type="text" class="custom-form-element form-input required" id="email_agent_earnest_subject" data-label="Subject" value="Missing %%DocumentsType%% - %%PropertyAddress%%">
                    </div>

                    <div id="email_agent_missing_transaction_message" class="text-editor">
                        Hello %%FirstName%%,<br><br>
                        We have not received your %%DocumentsType%% for %%PropertyAddress%%.
                        <br><br>
                        %%Message%%
                        <br>
                        {!! auth() -> user() -> signature !!}
                    </div>

                    <input type="hidden" id="listing_keys">

                </form>

            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary btn-lg p-3" id="send_email_agents_missing_transactions_button" data-dismiss="modal"><i class="fad fa-share mr-2"></i> Send Emails</a>
            </div>
        </div>
    </div>
</div>
@endsection
