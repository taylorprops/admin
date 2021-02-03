@extends('layouts.main')
@section('title', 'E-Sign - Add Signers')

@section('content')

<div class="container-1000 page-container mt-5 mx-auto page-esign-add-signers">

    <div class="h2 text-primary">E-Sign</div>

    @if($template_name != '')
    <div class="h4 text-orange mt-3">{{ $template_name }}</div>
    @endif

    <div class="row mt-3">

        <div class="col-12 col-sm-10">

            <div class="d-flex justify-content-between align-items-center">
                <div class="h5 text-gray">Select Signers and Order To Sign</div>
                <div class="text-gray font-10 mt-3">
                    Use the handles <i class="fal fa-bars text-primary fa-lg mx-2"></i> to reorder
                </div>
            </div>


            {{-- Signers --}}

            <div class="d-flex justify-content-start align-items-center">
                <div>
                    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Signers" data-content="Add everyone required to sign the documents. You can reorder the signing order by using the handles and dragging them.<br><br>Quick Add will add Seller One, Seller Two, Buyer One and Buyer Two with one click"><i class="fad fa-question-circle ml-2"></i></a>

                    <button class="btn btn-primary my-4" type="button" data-toggle="collapse" data-target="#add_signer_div" aria-expanded="false" aria-controls="add_signer_div">
                        <i class="fal fa-plus mr-2"></i> Add Signer
                    </button>
                </div>
                @if($is_template == 'yes')
                    <div>
                        <button class="btn btn-primary my-4 quick-add" type="button">
                            <i class="fal fa-plus mr-2"></i> Quick Add
                        </button>
                    </div>
                @endif
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

                        @if($is_template == 'no')

                            @if($members)

                                <div class="col-12 col-sm-5 select-signer-div">

                                    <span class="text-gray">Select From Transaction Members</span>
                                    <select class="custom-form-element form-select form-select-no-search signer-select add-signer-field signer-recipient-select" data-type="signer" data-label="Select Member">
                                        <option value=""></option>
                                        @foreach($members as $member)
                                            @php $member_type = $resource_items -> getResourceName($member -> member_type_id); @endphp
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
                                </select>
                            </div>

                        @elseif($is_template == 'yes')

                            <div class="col-12 col-sm-6 add-template-signer-fields">
                                <select class="custom-form-element form-select form-select-no-search add-signer-role add-signer-field required" data-label="Role">
                                    <option value=""></option>
                                    <option value="Seller One">Seller One</option>
                                    <option value="Seller Two">Seller Two</option>
                                    <option value="Buyer One">Buyer One</option>
                                    <option value="Buyer Two">Buyer Two</option>
                                    <option value="Listing Agent">Listing Agent</option>
                                    <option value="Co Listing Agent">Co Listing Agent</option>
                                    <option value="Buyer Agent">Buyer Agent</option>
                                    <option value="Co Buyer Agent">Co Buyer Agent</option>
                                    <option value="Listing Broker">Listing Broker</option>
                                    <option value="Selling Broker">Selling Broker</option>
                                    <option value="Loan Officer">Loan Officer</option>
                                    <option value="Title Rep">Title Rep</option>
                                    <option value="Attorney">Attorney</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                        @endif

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
                            <div class="col-3 @if($is_template == 'yes') hidden @endif font-weight-bold">{{ $signer -> signer_name }}</div>
                            <div class="col-2">@if($is_template == 'no') {{ $signer -> signer_role }} @else {{ $signer -> template_role }} @endif</div>
                            <div class="col-4 @if($is_template == 'yes') hidden @endif">{{ $signer -> signer_email }}</div>
                        </div>
                        <div><a href="javascript: void(0)"class="text-danger remove-user" data-type="signer"><i class="fal fa-times fa-lg"></i></a></div>
                    </div>

                @endforeach

            </div>

            {{-- Recipients --}}

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

                        @if($is_template == 'no')

                            @if($members)

                                <div class="col-12 col-sm-5 select-recipient-div">

                                    <span class="text-gray">Select From Transaction Members</span>
                                    <select class="custom-form-element form-select form-select-no-search recipient-select add-recipient-field signer-recipient-select" data-type="recipient" data-label="Select Member">
                                        <option value=""></option>
                                        @foreach($members as $member)
                                            @php $member_type = $resource_items -> getResourceName($member -> member_type_id); @endphp
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

                        @elseif($is_template == 'yes')

                            <div class="col-12 col-sm-6 add-template-recipient-fields">
                                <select class="custom-form-element form-select form-select-no-search add-recipient-role add-recipient-field required" data-label="Role">
                                    <option value=""></option>
                                    <option value="Seller One">Seller One</option>
                                    <option value="Seller Two">Seller Two</option>
                                    <option value="Buyer One">Buyer One</option>
                                    <option value="Buyer Two">Buyer Two</option>
                                    <option value="Listing Agent">Listing Agent</option>
                                    <option value="Co Listing Agent">Co Listing Agent</option>
                                    <option value="Buyer Agent">Buyer Agent</option>
                                    <option value="Co Buyer Agent">Co Buyer Agent</option>
                                    <option value="Listing Broker">Listing Broker</option>
                                    <option value="Selling Broker">Selling Broker</option>
                                    <option value="Loan Officer">Loan Officer</option>
                                    <option value="Title Rep">Title Rep</option>
                                    <option value="Attorney">Attorney</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                        @endif
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
                            <div class="col-3 '+hidden+' font-weight-bold">{{ $recipient -> signer_name }}</div>
                            <div class="col-2">{{ $recipient -> signer_role }}</div>
                            <div class="col-4 '+hidden+'">{{ $recipient -> signer_email }}</div>
                        </div>
                        <div><a href="javascript: void(0)"class="text-danger remove-user" data-type="signer"><i class="fal fa-times fa-lg"></i></a></div>
                    </div>

                @endforeach

            </div>

        </div>

        <div class="col-12 col-sm-2">

            <div class="mt-4 pt-4 next-div @if(!count($signers) > 0) hidden @endif">
                <button class="btn btn-primary btn-lg p-3" id="add_fields_button">Next <i class="fal fa-arrow-right ml-2"></i></button>
            </div>

        </div>

    </div>

</div>

<input type="hidden" id="envelope_id" value="{{ $envelope_id }}">
<input type="hidden" id="template_id" value="{{ $template_id }}">
<input type="hidden" id="is_template" value="{{ $is_template }}">
@endsection
