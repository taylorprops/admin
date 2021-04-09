@extends('layouts.main')
@section('title', 'E-Sign')

@section('content')

<div class="container-1200 page-container mt-5 mx-auto page-esign">

    <div class="d-flex justify-content-between align-items-center">

        <div class="h2 text-primary">E-Sign</div>

        <div>
            <a href="/esign/esign_add_documents" class="btn btn-success btn-lg create-envelope-button">
                <i class="fad fa-envelope text-white mr-2"></i> New Envelope <i class="fal fa-plus ml-2 text-white"></i>
            </a>
        </div>

    </div>

    <div class="row mt-5">

        <div class="col-12">

            <ul class="nav nav-tabs" id="esign_tabs" role="tablist">

                <li class="nav-item">
                    <a class="nav-link active" id="in_process_tab" data-tab="in_process" data-toggle="tab" href="#in_process_content" role="tab" aria-controls="in_process_content" aria-selected="true">In Process</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="completed_tab" data-tab="completed" data-toggle="tab" href="#completed_content" role="tab" aria-controls="completed_content" aria-selected="false">Completed</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="drafts_tab" data-tab="drafts" data-toggle="tab" href="#drafts_content" role="tab" aria-controls="drafts_content" aria-selected="false">Drafts</a>
                </li>



                @if(auth() -> user() -> group == 'admin')

                <li class="nav-item">
                    <a class="nav-link" id="templates_tab" data-tab="templates" data-toggle="tab" href="#templates_content" role="tab" aria-controls="templates_content" aria-selected="false">Templates</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="system_templates_tab" data-tab="system_templates" data-toggle="tab" href="#system_templates_content" role="tab" aria-controls="system_templates_content" aria-selected="false">System Templates</a>
                </li>

                @endif

                <li class="nav-item">
                    <a class="nav-link" id="canceled_tab" data-tab="canceled" data-toggle="tab" href="#canceled_content" role="tab" aria-controls="canceled_content" aria-selected="false">Canceled</a>
                </li>

            </ul>

            <div class="tab-content mt-4" id="esign_tabs_content">

                <div class="tab-pane fade show active" id="in_process_content" role="tabpanel" aria-labelledby="in_process_tab">

                    <div id="in_process_div"></div>

                </div>

                <div class="tab-pane fade" id="completed_content" role="tabpanel" aria-labelledby="completed_tab">

                    <div id="completed_div"></div>

                </div>

                <div class="tab-pane fade" id="drafts_content" role="tabpanel" aria-labelledby="drafts_tab">

                    <div id="drafts_div"></div>
                    <div class="collapse" id="deleted_drafts_div"></div>

                </div>

                <div class="tab-pane fade" id="templates_content" role="tabpanel" aria-labelledby="templates_tab">

                    <div id="templates_div"></div>
                    <div class="collapse" id="deleted_templates_div"></div>

                </div>

                @if(auth() -> user() -> group == 'admin')
                <div class="tab-pane fade" id="system_templates_content" role="tabpanel" aria-labelledby="system_templates_tab">

                    <div id="system_templates_div"></div>
                    <div class="collapse" id="deleted_system_templates_div"></div>

                </div>
                @endif

                <div class="tab-pane fade" id="canceled_content" role="tabpanel" aria-labelledby="canceled_tab">

                    <div id="canceled_div"></div>

                </div>

            </div>

        </div>

    </div>

</div>

<div class="modal fade draggable" id="confirm_cancel_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_cancel_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="confirm_cancel_title">Confirm Cancellation</h4>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="d-flex justify-content-around align-items-center">
                        Are you sure you want to cancel this signature request?
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success modal-confirm-button" id="confirm_cancel_button" data-dismiss"modal"><i class="fal fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade draggable" id="resend_envelope_modal" tabindex="-1" role="dialog" aria-labelledby="resend_envelope_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="resend_envelope_title">Resend Envelope</h4>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="d-flex justify-content-around align-items-center">
                        Are you sure you want to resend this signature request?
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success modal-confirm-button" id="resend_envelope_button" data-dismiss"modal"><i class="fal fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>

@endsection
