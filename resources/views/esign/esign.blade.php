@extends('layouts.main')
@section('title', 'E-Sign')

@section('content')

<div class="container-1000 page-container mt-5 mx-auto page-esign">

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
                <li class="nav-item">
                    <a class="nav-link" id="templates_tab" data-tab="templates" data-toggle="tab" href="#templates_content" role="tab" aria-controls="templates_content" aria-selected="false">Templates</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="system_templates_tab" data-tab="system_templates" data-toggle="tab" href="#system_templates_content" role="tab" aria-controls="system_templates_content" aria-selected="false">System Templates</a>
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

                <div class="tab-pane fade" id="system_templates_content" role="tabpanel" aria-labelledby="system_templates_tab">

                    <div id="system_templates_div"></div>
                    <div class="collapse" id="deleted_system_templates_div"></div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
