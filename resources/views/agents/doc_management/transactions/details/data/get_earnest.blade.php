<div class="container-1200 earnest-container p-1 p-md-4 mx-auto">

    <form id="earnest_form">

        <div class="row d-flex align-items-center">

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">

                <select class="custom-form-element form-select" id="earnest_held_by" name="earnest_held_by" data-label="Earnest Held By">
                    <option value=""></option>
                    <option value="us" @if($earnest_held_by == 'us') selected @endif>Taylor/Anne Arundel Properties</option>
                    <option value="other_company" @if($earnest_held_by == 'other_company') selected @endif>Other Real Estate Company</option>
                    <option value="title" @if($earnest_held_by == 'title') selected @endif>Title Company/Attorney</option>
                    <option value="heritage_title" @if($earnest_held_by == 'heritage_title') selected @endif>Heritage Title</option>
                    <option value="builder" @if($earnest_held_by == 'builder') selected @endif>Builder</option>
                </select>

            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">

                <select class="custom-form-element form-select" id="earnest_account" name="earnest_account" data-label="Earnest Account">
                    <option value=""></option>
                    @foreach($earnest_accounts as $earnest_account)
                        <option value="{{ $earnest_account -> resource_id }}" @if($earnest_account -> resource_id == $suggested_earnest_account) selected @endif>{{ $earnest_account -> resource_state }} - {{ $earnest_account -> resource_account_number }} - {{ $earnest_account -> resource_name }}</option>
                    @endforeach
                </select>

            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <btn class="btn btn-success" id="save_earnest_button"><i class="fa fa-save mr-2"></i> Save</btn>
            </div>

            <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                <div class="alert alert-info mb-0 font-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>In Escrow</div>
                        <div>${{ number_format($earnest -> amount_total, 2) ?? '0.00' }}</div>
                    </div>
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

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="h4 text-success"><i class="fad fa-money-check-alt mr-2"></i> Checks In</div>
                    </div>
                    <div>
                        <button class="btn btn-success wp-150 add-check-button" data-type="in"><i class="fal fa-plus mr-2"></i> Add Check In</button>
                    </div>
                </div>

                <div id="earnest_checks_in_div"></div>

            </div>

        </div>

        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>

        <div class="row">

            <div class="col-12">

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <div class="h4 text-danger"><i class="fad fa-money-check-alt mr-2"></i> Checks Out</div>
                    </div>
                    <div>
                        <button class="btn btn-success wp-150 add-check-button" data-type="out"><i class="fal fa-plus mr-2"></i> Add Check Out</button>
                    </div>
                </div>

                <div id="earnest_checks_out_div"></div>

            </div>

        </div>

    </div>

</div>

<input type="hidden" id="Earnest_ID" name="Earnest_ID" value="{{ $earnest -> id }}" />
