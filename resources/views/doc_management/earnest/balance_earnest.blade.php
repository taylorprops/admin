@extends('layouts.main')
@section('title', 'Balance Earnest Deposits')

@section('content')

<div class="container page-container page-balance-earnest pt-5">

    <div class="h2 text-orange mb-4">Pending Earnest Deposits</div>

    <div class="row">

        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">

            <div class="earnest-key">

                <div class="earnest-totals">

                    <h4 class="text-primary mb-3">Accounts</h4>

                    <div class="list-group earnest-totals-container animate__animated animate__fadeIn" role="tablist">
                        <div class="list-group-item p-5 text-center text-gray"><i class="fas fa-spinner fa-pulse fa-2x"></i></div>
                    </div>

                </div>

                <div class="earnest-search mt-5">

                    <h4 class="text-primary mb-3">Search All Deposits</h4>

                    <div class="earnest-search-div">

                        <input type="text" class="custom-form-element form-input" id="earnest_search_input" data-label="Search by Address, Agent or Check Details">

                        <div class="relative">
                            <div id="earnest_search_results" class="shadow"></div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-12 col-xl-9">

            <div class="earnest-checks-container tab-content animate__animated animate__fadeIn">

            </div>

        </div>

    </div>

</div>

<div class="modal fade draggable" id="bounced_check_notification_modal" tabindex="-1" role="dialog" aria-labelledby="bounced_check_notification_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="bounced_check_notification_modal_title">Notify Agent</h4>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">
                <form id="bounced_check_notification_form">

                    <div class="row">

                        <div class="col-12">

                            <div class="font-10 text-orange mb-2">Notify Agent of Bounced Earnest Deposit</div>

                            <textarea id="bounced_check_message" name="bounced_check_message" class="text-editor"><br>{!! auth() -> user() -> signature !!}</textarea>

                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary" id="send_bounced_check_notification_button" data-dismiss="modal"><i class="fad fa-share mr-2"></i> Send Message</a>
            </div>
        </div>
    </div>
</div>

@endsection
