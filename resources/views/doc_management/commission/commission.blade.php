@extends('layouts.main')
@section('title', 'Commission Breakdowns')

@section('content')
<div class="container page-container page-commission-breakdowns">
    <div class="row">
        <div class="col-12">
            <h2>Commission Breakdowns and Checks</h2>
        </div>
    </div>

    <div class="container-1300 mx-auto">

        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <a class="btn btn-lg btn-success" href="javascript: void(0)" id="add_check_button">
                            Add Check <i class="fal fa-plus ml-2"></i>
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="commissions-pending animate__animated animate__fadeIn"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="commission-checks-queue animate__animated animate__fadeIn"></div>
                    </div>
                </div>

                <div class="row my-5">
                    <div class="col-12">
                        <h4 class="text-orange">Deleted Checks</h4>

                        <div class="row">
                            <div class="col-4">
                                <input type="text" class="custom-form-element form-input" id="search_deleted_checks" data-label="Search by Address or Agent">
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-12">

                                <div class="collapse" id="deleted_checks_div">

                                    <table class="table table-hover table-bordered table-sm" id="deleted_checks_table">
                                        <thead>
                                            <tr>
                                                <th class="text-primary">View</th>
                                                <th>Received</th>
                                                <th>Agent</th>
                                                <th>Client</th>
                                                <th>Address</th>
                                                <th>Check #</th>
                                                <th>Check Date</th>
                                                <th>Check Amount</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="deleted_checks">
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

@include('doc_management/commission/commission_and_commission_other_modal_shared');
@endsection
