@extends('layouts.main')
@section('title', 'E-Sign - Add Signers')

@section('content')

<div class="container-1000 page-container mt-5 mx-auto page-esign-add-signers">

    <div class="h2 text-primary">E-Sign</div>


    <div class="row mt-3">

        <div class="col-12">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    @if($address)
                    <div class="h4 text-primary mb-3">{{ $address }}</div>
                    @endif
                    <div class="h5 text-gray">Select Signers and Order To Sign</div>
                </div>

                <div class="next-div @if(!count($signers) > 0) hidden @endif">
                    <button class="btn btn-primary btn-lg p-3" id="save_signers_button">Next <i class="fal fa-arrow-right ml-2"></i></button>
                </div>

            </div>

        </div>

    </div>

    <div class="row mt-3">

        <div class="col-12 col-md-4">

            @if($members)

                <div class="select-signer-div">

                    <div class="h5 text-primary mb-4">Add Signer/Recipient <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Signers/Recipients" data-content="Signers are anyone required to sign the documents and Recipients will just recieve a completed copy once all parites have signed"><i class="fad fa-question-circle ml-2"></i></a></div>

                    <div class="btn-group mb-4 w-100" role="group">
                        <button type="button" class="btn btn-primary envelope-role ml-0 w-50 active" data-role="Signer"><i class="fal fa-plus mr-2"></i> Signer</button>
                        <button type="button" class="btn btn-primary envelope-role w-50" data-role="Recipient"><i class="fal fa-plus mr-2"></i> Recipient</button>
                    </div>

                    <div class="text-orange mb-2 font-10">Add  <span id="header_text">Signer</div>
                    <div class="p-2 bg-blue-light rounded">

                        <div class="text-gray">Select From Transaction Members</div>
                        <select class="custom-form-element form-select form-select-no-search signer-select add-signer-field" id="add_signer_member_id" data-type="signer" data-label="Select Member">
                            <option value=""></option>
                            @foreach($members as $member)
                                @php $member_type = $member -> member_type; @endphp
                                <option value="{{ $member -> id }}"
                                    data-name="{{ $member -> first_name.' '.$member -> last_name }}"
                                    data-email="{{ $member -> email }}"
                                    data-member-type="{{ $member_type }}">{{ $member_type }} - @if($member -> first_name != ''){{ $member -> first_name.' '.$member -> last_name }}@else{{ $member -> company }}@endif</option>
                            @endforeach
                        </select>

                    </div>

                </div>

            @endif


            <div class="add-signer-fields mt-2">

                <div class="p-2 bg-blue-light rounded">

                    @if($members)<span class="text-gray">Or Add New</span>@endif

                    <div class="row">

                        <div class="col-12">
                            <input type="text" class="custom-form-element form-input add-signer-field" id="add_signer_name" data-label="Name">
                        </div>

                        <div class="col-12">
                            <input type="email" class="custom-form-element form-input add-signer-field" id="add_signer_email" data-label="Email">
                        </div>

                        <div class="col-12">
                            <select class="custom-form-element form-select form-select-no-search add-signer-field" id="add_signer_role" data-label="Role">
                                <option value=""></option>
                                <option value="Other" selected>Other</option>
                                <option value="Seller">Seller</option>
                                <option value="Buyer">Buyer</option>
                                <option value="Listing Agent">Listing Agent</option>
                                <option value="Buyer Agent">Buyer Agent</option>
                                <option value="Broker">Broker</option>
                                <option value="Co Agent">Co Agent</option>
                                <option value="Loan Officer">Loan Officer</option>
                                <option value="Title Rep">Title Rep</option>
                                <option value="Attorney">Attorney</option>
                                <option value="Referring Agent">Referring Agent</option>
                                <option value="Referring Broker">Referring Broker</option>
                                <option value="Receiving Agent">Receiving Agent</option>
                                <option value="Receiving Broker">Receiving Broker</option>
                            </select>
                        </div>

                        <div class="col-12">

                            <div class="d-flex justify-content-around w-100 mt-4">
                                <button class="btn btn-primary" id="save_add_signer" data-type="signer" type="button">
                                    <i class="fal fa-plus mr-2"></i> Add <span id="save_type">Signer</span>
                                </button>
                            </div>

                            <input type="hidden" id="envelope_role" value="Signer">

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-12 col-md-8">

            <div class="ml-0 ml-md-5">

                <div class="text-gray font-10 mb-4">
                    Use the handles <i class="fal fa-bars text-primary fa-lg mx-2"></i> to reorder
                </div>

                <div class="h5 text-primary mb-2">Selected Signers</div>

                <div class="signers-container list-group mt-4">

                    @foreach($signers as $signer)

                        <div class="list-group-item signer-item d-flex justify-content-between align-items-center text-gray w-100 py-0 px-0"
                            data-id="{{ $signer -> id }}"
                            data-name="{{ $signer -> signer_name }}"
                            data-email="{{ $signer -> signer_email }}"
                            data-role="{{ $signer -> signer_role }}"">

                            <div class="ml-2 w-8 text-center">
                                <a href="javascript: void(0)" class="user-handle"><i class="fal fa-bars text-primary fa-lg"></i></a>
                            </div>

                            <div class="w-6">
                                <span class="signer-count font-11 text-orange"></span>
                            </div>

                            <div class="w-30">
                                <span class="font-10">{{ $signer -> signer_name }}</span>
                                <br>
                                <span class="font-9 font-italic">{{ $signer -> signer_role }}</span>
                            </div>

                            <div class="w-40">
                                <input type="text" class="custom-form-element form-input signer-email required" data-type="signer" value="{{ $signer -> signer_email }}" data-label="Email">
                            </div>

                            <div class="w-8 text-center">
                                <button type="button" class="btn btn-danger remove-user" data-type="signer"><i class="fal fa-times fa-lg"></i></button>
                            </div>


                        </div>

                    @endforeach

                    <div class="w-100 text-center mt-3 text-gray no-signers">No Signers Added Yet</div>

                </div>

                <div class="row">

                    <div class="col-12">

                        <hr class="my-5">

                    </div>

                </div>

                <div class="h5 text-primary mb-2">Selected Recipients</div>

                <div class="recipients-container list-group mt-4">

                    @foreach($recipients as $recipient)

                        <div class="list-group-item signer-item d-flex justify-content-between align-items-center text-gray w-100 py-0 px-0"
                            data-id="{{ $recipient -> id }}"
                            data-name="{{ $recipient -> signer_name }}"
                            data-email="{{ $recipient -> signer_email }}"
                            data-role="{{ $recipient -> signer_role }}"">

                            <div class="ml-2 w-8 text-center">
                                <a href="javascript: void(0)" class="user-handle"><i class="fal fa-bars text-primary fa-lg"></i></a>
                            </div>

                            <div class="w-6">
                                <span class="recipient-count font-11 text-orange"></span>
                            </div>

                            <div class="w-34">
                                <span class="font-10">{{ $recipient -> signer_name }}</span>
                                <br>
                                <span class="font-9 font-italic">{{ $recipient -> signer_role }}</span>
                            </div>

                            <div class="w-34">
                                <input type="text" class="custom-form-element form-input signer-email required" data-type="recipient" value="{{ $recipient -> signer_email }}" data-label="Email">
                            </div>

                            <div class="w-8 text-center">
                                <button type="button" class="btn btn-danger remove-user" data-type="recipient"><i class="fal fa-times fa-lg"></i></button>
                            </div>


                        </div>

                    @endforeach

                    <div class="w-100 text-center mt-3 text-gray no-recipients">No Recipients Added Yet</div>

                </div>

            </div>

        </div>

    </div>







    {{-- <div class="row mt-3">

        <div class="col-12">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <div class="h5 text-gray">Select Signers and Order To Sign</div>
                    <div class="text-gray font-10 mt-3">
                        Use the handles <i class="fal fa-bars text-primary fa-lg mx-2"></i> to reorder
                    </div>
                </div>

                <div class="next-div @if(!count($signers) > 0) hidden @endif">
                    <button class="btn btn-primary btn-lg p-3" id="add_fields_button">Next <i class="fal fa-arrow-right ml-2"></i></button>
                </div>

            </div>



            <div class="d-flex justify-content-start align-items-center">
                <div>
                    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Signers" data-content="Add everyone required to sign the documents. You can reorder the signing order by using the handles and dragging them.<br><br>Quick Add will add Seller One, Seller Two, Buyer One and Buyer Two with one click"><i class="fad fa-question-circle ml-2"></i></a>

                    <button class="btn btn-primary my-4" type="button" data-toggle="collapse" data-target="#add_signer_div" aria-expanded="false" aria-controls="add_signer_div">
                        <i class="fal fa-plus mr-2"></i> Add Signer
                    </button>
                </div>
            </div>



            <div class="collapse" id="add_signer_div">

                <div class="p-3 border rounded">

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="h5 text-primary">Add Signer</div>
                        <div>
                            <button class="btn btn-sm btn-danger" data-toggle="collapse" data-target="#add_signer_div" aria-expanded="false" aria-controls="add_signer_div">
                                <i class="fal fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">

                        @if($members)

                            <div class="col-12 col-sm-5 select-signer-div">

                                <span class="text-gray">Select From Transaction Members</span>
                                <select class="custom-form-element form-select form-select-no-search signer-select add-signer-field signer-recipient-select" data-type="signer" data-label="Select Member">
                                    <option value=""></option>
                                    @foreach($members as $member)
                                        @php $member_type = $member -> member_type; @endphp
                                        <option value="{{ $member -> id }}"
                                            data-name="{{ $member -> first_name.' '.$member -> last_name }}"
                                            data-email="{{ $member -> email }}"
                                            data-member-type="{{ $member_type }}">{{ $member_type }} - @if($member -> first_name != ''){{ $member -> first_name.' '.$member -> last_name }}@else{{ $member -> company }}@endif</option>
                                    @endforeach
                                </select>

                                <div class="signer-select-fields hidden">
                                    <input type="hidden" class="add-signer-name">
                                    <input type="email" class="custom-form-element form-input add-signer-email add-signer-field" data-label="Email">
                                    <select class="custom-form-element form-select form-select-no-search add-signer-role add-signer-field" data-label="Role">
                                        <option value=""></option>
                                        <option value="Other">Other</option>
                                        <option value="Seller">Seller</option>
                                        <option value="Buyer">Buyer</option>
                                        <option value="Listing Agent">Listing Agent</option>
                                        <option value="Buyer Agent">Buyer Agent</option>
                                        <option value="Broker">Broker</option>
                                        <option value="Co Agent">Co Agent</option>
                                        <option value="Loan Officer">Loan Officer</option>
                                        <option value="Title Rep">Title Rep</option>
                                        <option value="Attorney">Attorney</option>
                                        <option value="Referring Agent">Referring Agent</option>
                                        <option value="Referring Broker">Referring Broker</option>
                                        <option value="Receiving Agent">Receiving Agent</option>
                                        <option value="Receiving Broker">Receiving Broker</option>
                                    </select>
                                </div>

                            </div>

                            <div class="col-12 col-sm-2 mt-3 text-primary font-12 font-weight-bold text-center">Or</div>

                        @endif


                        <div class="col-12 col-sm-5 add-signer-fields">

                            @if($members)<span class="text-gray">Add New</span>@endif

                            <input type="text" class="custom-form-element form-input add-signer-name add-signer-field" data-label="Name">
                            <input type="email" class="custom-form-element form-input add-signer-email add-signer-field" data-label="Email">
                            <select class="custom-form-element form-select form-select-no-search add-signer-role add-signer-field" data-label="Role">
                                <option value=""></option>
                                <option value="Other" selected>Other</option>
                                <option value="Seller">Seller</option>
                                <option value="Buyer">Buyer</option>
                                <option value="Listing Agent">Listing Agent</option>
                                <option value="Buyer Agent">Buyer Agent</option>
                                <option value="Broker">Broker</option>
                                <option value="Co Agent">Co Agent</option>
                                <option value="Loan Officer">Loan Officer</option>
                                <option value="Title Rep">Title Rep</option>
                                <option value="Attorney">Attorney</option>
                                <option value="Referring Agent">Referring Agent</option>
                                <option value="Referring Broker">Referring Broker</option>
                                <option value="Receiving Agent">Receiving Agent</option>
                                <option value="Receiving Broker">Receiving Broker</option>
                            </select>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-12">
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-around w-100">

                                <button class="btn btn-primary save-add-user" data-type="signer" type="button">
                                    <i class="fad fa-save mr-2"></i> Save Signer
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="signers-container list-group p-4 mt-2 border rounded">

                <div class="h4 text-primary">Selected Signers</div>

                @foreach($signers as $signer)

                    <div class="list-group-item signer-item d-flex justify-content-between align-items-center text-gray w-100" data-id="{{ $signer -> id }}" data-name="{{ $signer -> signer_name }}" data-email="{{ $signer -> signer_email }}" data-role="{{ $signer -> signer_role }}" data-template-role="{{ $signer -> template_role }}">
                        <div class="row d-flex align-items-center w-100">
                            <div class="col-1 user-handle"><i class="fal fa-bars text-primary fa-lg"></i></div>
                            <div class="col-1"><span class="signer-count font-11 text-orange">{{ $loop -> iteration }}</span></div>
                            <div class="col-3 font-weight-bold">{{ $signer -> signer_name }}</div>
                            <div class="col-3">{{ $signer -> signer_role }}</div>
                            <div class="col-4"><input type="text" class="custom-form-element form-input signer-email required" data-type="signer" value="{{ $signer -> signer_email }}" data-label="Email"></div>
                        </div>
                        <div class="pl-3"><button type="button" class="btn btn-danger remove-user" data-type="signer"><i class="fal fa-times fa-lg"></i></button></div>
                    </div>

                @endforeach

            </div>


            <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Recipients" data-content="Add anyone who just needs a copy of the documents after they have been signed by all parties."><i class="fad fa-question-circle ml-2"></i></a>

            <button class="btn btn-primary my-4" type="button" data-toggle="collapse" data-target="#add_recipient_div" aria-expanded="false" aria-controls="add_recipient_div">
                <i class="fal fa-plus mr-2"></i> Add Recipient
            </button>

            <div class="collapse" id="add_recipient_div">

                <div class="p-3 border rounded">

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="h5 text-primary">Add Recipient</div>
                        <div>
                            <button class="btn btn-sm btn-danger" data-toggle="collapse" data-target="#add_recipient_div" aria-expanded="false" aria-controls="add_recipient_div">
                                <i class="fal fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">

                        @if($members)

                            <div class="col-12 col-sm-5 select-recipient-div">

                                <span class="text-gray">Select From Transaction Members</span>
                                <select class="custom-form-element form-select form-select-no-search recipient-select add-recipient-field signer-recipient-select" data-type="recipient" data-label="Select Member">
                                    <option value=""></option>
                                    @foreach($members as $member)
                                        @php $member_type = $member -> member_type; @endphp
                                        <option value="{{ $member -> id }}"
                                            data-name="{{ $member -> first_name.' '.$member -> last_name }}"
                                            data-email="{{ $member -> email }}"
                                            data-member-type="{{ $member_type }}">{{ $member_type }} - @if($member -> first_name != ''){{ $member -> first_name.' '.$member -> last_name }}@else{{ $member -> company }}@endif</option>
                                    @endforeach
                                </select>

                                <div class="recipient-select-fields hidden">
                                    <input type="hidden" class="add-recipient-name">
                                    <input type="email" class="custom-form-element form-input add-recipient-email add-recipient-field" data-label="Email">
                                    <select class="custom-form-element form-select form-select-no-search add-recipient-role add-recipient-field" data-label="Role">
                                        <option value=""></option>
                                        <option value="Other" selected>Other</option>
                                        <option value="Seller">Seller</option>
                                        <option value="Buyer">Buyer</option>
                                        <option value="Listing Agent">Listing Agent</option>
                                        <option value="Buyer Agent">Buyer Agent</option>
                                        <option value="Broker">Broker</option>
                                        <option value="Co Agent">Co Agent</option>
                                        <option value="Loan Officer">Loan Officer</option>
                                        <option value="Title Rep">Title Rep</option>
                                        <option value="Attorney">Attorney</option>
                                    </select>
                                </div>

                            </div>

                            <div class="col-12 col-sm-2 mt-3 text-primary font-12 font-weight-bold text-center">Or</div>

                        @endif


                        <div class="col-12 col-sm-5 add-recipient-fields">

                            @if($members)<span class="text-gray">Add New</span>@endif

                            <input type="text" class="custom-form-element form-input add-recipient-name add-recipient-field" data-label="Name">
                            <input type="email" class="custom-form-element form-input add-recipient-email add-recipient-field" data-label="Email">
                            <select class="custom-form-element form-select form-select-no-search add-recipient-role add-recipient-field" data-label="Role">
                                <option value=""></option>
                                <option value="Other">Other</option>
                                <option value="Seller">Seller</option>
                                <option value="Buyer">Buyer</option>
                                <option value="Listing Agent">Listing Agent</option>
                                <option value="Buyer Agent">Buyer Agent</option>
                                <option value="Broker">Broker</option>
                                <option value="Co Agent">Co Agent</option>
                                <option value="Loan Officer">Loan Officer</option>
                                <option value="Title Rep">Title Rep</option>
                                <option value="Attorney">Attorney</option>
                            </select>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-12">
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-around w-100">
                                <button class="btn btn-primary save-add-user" data-type="recipient" type="button">
                                    <i class="fad fa-save mr-2"></i> Save Recipient
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="recipients-container list-group p-4 mt-2 border rounded">

                <div class="h4 text-primary">Selected Recipients</div>

                @foreach($recipients as $recipient)

                    <div class="list-group-item recipient-item d-flex justify-content-between align-items-center text-gray w-100" data-id="{{ $recipient -> id }}" data-name="{{ $recipient -> signer_name }}" data-email="{{ $recipient -> signer_email }}" data-role="{{ $recipient -> signer_role }}" data-template-role="{{ $recipient -> template_role }}">
                        <div class="row d-flex align-items-center w-100">
                            <div class="col-1 user-handle"><i class="fal fa-bars text-primary fa-lg"></i></div>
                            <div class="col-1"><span class="signer-count font-11 text-orange">{{ $loop -> iteration }}</span></div>
                            <div class="col-3 font-weight-bold ">{{ $recipient -> signer_name }}</div>
                            <div class="col-3">{{ $recipient -> signer_role }}</div>
                            <div class="col-4"><input type="text" class="custom-form-element form-input signer-email required" value="{{ $recipient -> signer_email }}" data-type="recipient" data-label="Email"></div>
                        </div>
                        <div class="pl-3"><button type="button" class="btn btn-danger remove-user" data-type="recipient"><i class="fal fa-times fa-lg"></i></button></div>
                    </div>

                @endforeach

            </div>

        </div>


    </div> --}}

</div>

<input type="hidden" id="envelope_id" value="{{ $envelope_id }}">
@endsection
