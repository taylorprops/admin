<div class="container-1100 earnest-container p-1 p-md-4 mx-auto">

    <form id="earnest_form">

        <div class="d-flex justify-content-between align-items-center">

            <div class="d-flex justify-content-start align-items-center bg-blue-light p-3 rounded earnest-questions">

                <div class="mr-4 wpx-300">

                    <select class="custom-form-element form-select" id="earnest_held_by" name="earnest_held_by" data-label="Earnest Held By">
                        <option value=""></option>
                        <option value="us" @if($earnest_held_by == 'us') selected @endif>Taylor/Anne Arundel Properties</option>
                        <option value="other_company" @if($earnest_held_by == 'other_company') selected @endif>Other Real Estate Company</option>
                        <option value="title" @if($earnest_held_by == 'title') selected @endif>Title Company/Attorney</option>
                        <option value="heritage_title" @if($earnest_held_by == 'heritage_title') selected @endif>Heritage Title</option>
                        <option value="builder" @if($earnest_held_by == 'builder') selected @endif>Builder</option>
                    </select>

                </div>

                <div class="mr-2 wpx-350">

                    <select class="custom-form-element form-select" id="earnest_account_id" name="earnest_account_id" data-label="Earnest Account">
                        <option value=""></option>
                        @foreach($earnest_accounts as $earnest_account)
                            <option value="{{ $earnest_account -> resource_id }}" @if($earnest_account -> resource_id == $suggested_earnest_account) selected @endif>{{ $earnest_account -> resource_state }} - {{ $earnest_account -> resource_account_number }} - {{ $earnest_account -> resource_name }}</option>
                        @endforeach
                    </select>

                </div>

                <div class="">
                    <btn class="btn btn-primary" id="save_earnest_button"><i class="fad fa-save mr-2"></i> Save</btn>
                </div>

            </div>

            <div>

                <div class="alert alert-info mb-0 font-12 in-escrow-alert">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>In Escrow</div>
                        <div class="ml-3" id="in_escrow"></div>
                    </div>
                </div>

                <div class="mt-3 status-waiting">
                    @if($hide_set_status_to_waiting)
                    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Set Status" data-content="Use this if the contract has been canceled but we have not received the release agreement."><i class="fad fa-question-circle ml-2"></i></a>
                    <a href="javascript: void(0)" class="text-orange" id="show_set_status_to_waiting_button">Set Status to Waiting For Release</a>
                    @endif
                </div>

            </div>

        </div>

    </form>


    <div class="row">
        <div class="col-12 my-4">
            <hr>
        </div>
    </div>

    <div class="checks-in-container">

        <div class="row">

            <div class="col-12">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="h4 text-success"><i class="fad fa-money-check-alt mr-2"></i> Checks In</div>
                    </div>

                    <div class="font-12 text-success">Total: <span id="earnest_checks_in_total"></span></div>

                    <div>
                        <button class="btn btn-success wpx-150 add-earnest-check-button" data-check-type="in"><i class="fal fa-plus mr-2"></i> Add Check In</button>
                    </div>
                </div>

                <div id="earnest_checks_in_div" class="mt-3"></div>

            </div>

        </div>

        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>

        <div class="row">

            <div class="col-12">

                <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                    <div>
                        <div class="h4 text-danger"><i class="fad fa-money-check-alt mr-2"></i> Checks Out</div>
                    </div>

                    <div class="font-12 text-danger">Total: <span id="earnest_checks_out_total"></span></div>

                    <div>
                        <button class="btn btn-success wpx-150 add-earnest-check-button" data-check-type="out"><i class="fal fa-plus mr-2"></i> Add Check Out</button>
                    </div>
                </div>

                <div id="earnest_checks_out_div"></div>

            </div>

        </div>

        <hr>

        <div class="row">

            <div class="col-12">

                <div class="h4 text-primary mt-4 mb-3"><i class="fa fa-file-alt mr-3"></i> Notes</div>

                <div class="row">

                    <div class="col-12 col-sm-6">

                        <div class="mt-4">

                            <span class="text-orange font-10"><i class="fal fa-plus mr-2"></i> Add Notes</span>

                            <textarea class="custom-form-element form-textarea earnest-notes" data-label="Enter Notes"></textarea>

                            <button class="btn btn-success save-earnest-notes-button ml-0"><i class="fad fa-save mr-2"></i> Save Notes </button>

                        </div>

                    </div>

                    <div class="col-12 col-sm-6">

                        <div id="earnest_notes_div"></div>

                    </div>

                </div>



            </div>
        </div>

    </div>

</div>

<input type="hidden" id="Earnest_ID" name="Earnest_ID" value="{{ $earnest -> id }}" >
<input type="hidden" id="earnest_mail_to_address" value="{{ $earnest_mail_to_address }}">
