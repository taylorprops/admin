<div class="row animated fadeIn">

    <div class="col-12">

        <div class="list-group" id="checklist_list_group">

            @foreach($checklist_groups as $checklist_group)

                {{-- <div class="list-group-item d-flex justify-content-start align-items-center border-left-0 border-right-0 bg-blue-light @if($loop -> first) mt-3 @else mt-4 @endif"> --}}
                <div class="list-group-item border border-primary border-2 d-flex justify-content-start align-items-center @if($loop -> first) mt-3 @else mt-5 @endif py-0">
                    <div>
                        <div class="h5-responsive text-primary">{{ $checklist_group -> resource_name }}</div>
                    </div>
                    <div class="ml-3">
                        <button type="button" class="btn btn-success btn-sm add-checklist-item-button" data-toggle="tooltip" data-group-id="{{ $checklist_group -> resource_id }}" title="Add Checklist Item"><i class="fal fa-plus"></i></button>
                    </div>
                </div>

                @foreach($checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id) as $checklist_item)

                    @php

                    $checklist_id = $checklist_item -> checklist_id;

                    if($checklist_item -> checklist_form_id > 0) {
                        $checklist_item_name = $files -> GetFormName($checklist_item -> checklist_form_id);
                    } else {
                        $checklist_item_name = $checklist_item -> checklist_item_added_name;
                    }

                    $status_details = $transaction_checklist_items_model -> GetStatus($checklist_item -> id);
                    $status = $status_details -> status;
                    $admin_classes = $status_details -> admin_classes;
                    $fa = $status_details -> fa;

                    $show_mark_required = $status_details -> show_mark_required;
                    $show_mark_not_required = $status_details -> show_mark_not_required;

                    $checklist_item_id = $checklist_item -> id;

                    $notes = $transaction_checklist_item_notes -> where('checklist_item_id', $checklist_item_id) -> get();
                    $notes_unread_count = $notes -> where('note_status', 'unread') -> where('Agent_ID', '>', '0') -> count();
                    if(count($notes) == 0) {
                        $notes = null;
                    }
                    $notes_tooltip = null;
                    $notes_unread = '';
                    if($notes) {
                        if($notes_unread_count > 0) {
                            $notes_tooltip = $notes_unread_count.' Unread Comment'.($notes_unread_count != 1 ? 's' : '');
                            $notes_unread = 'notes-unread';
                        } else {
                            $notes_tooltip = count($notes).' Total Comment'.(count($notes) != 1 ? 's' : '');
                        }
                    }

                    $unused_status_class = null;
                    if($status == 'Required' || $status == 'If Applicable') {
                        $unused_status_class = 'checklist-item-unused';
                    }


                    @endphp

                    <div class="list-group-item px-1 py-1 checklist-item-div {{ $notes_unread }} @if($status == 'Pending') pending @elseif($status == 'Required') required @endif" id="checklist_item_{{ $checklist_item_id }}">

                        <div class="d-flex justify-content-between align-content-around mb-1">

                            <div class="d-flex justify-content-start align-items-center w-100">

                                <div class="dropdown">

                                    <button class="btn btn-primary dropdown-toggle checklist-item-dropdown py-0 px-2" type="button" id="checklist_item_options_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>

                                    <div class="dropdown-menu dropdown-primary">

                                        <a class="dropdown-item mark-required no @if(!$show_mark_not_required) d-none @else d-block @endif" href="javascript: void(0)" data-checklist-item-id="{{ $checklist_item_id }}" data-required="no">Make If Applicable</a>

                                        <a class="dropdown-item mark-required yes @if(!$show_mark_required) d-none @else d-block @endif" href="javascript: void(0)" data-checklist-item-id="{{ $checklist_item_id }}" data-required="yes">Make Required</a>

                                        <a class="dropdown-item remove-checklist-item" href="javascript: void(0)" data-checklist-item-id="{{ $checklist_item_id }}">Remove</a>

                                    </div>

                                </div>

                                <div class="mx-2 w-100 h-100 d-block">
                                    <a href="javascript:void(0)" class="d-flex align-items-center checklist-item-name text-gray w-100 h-100 {{ $unused_status_class }}" data-checklist-item-id="{{ $checklist_item_id }}" data-checklist-item-name="{{ $checklist_item_name }}">{{ $checklist_item_name }}</a>
                                </div>
                            </div>

                            <div>

                                <div class="status-badge badge {{ $admin_classes }} {{ $unused_status_class }} p-2 w-100">
                                    {!! $status !!}
                                </div>

                                <div class="w-100 text-center" data-toggle="tooltip" data-html="true" title="{!! $notes_tooltip !!}">
                                    <a class="notes-toggle" data-toggle="collapse" href="#notes_{{ $checklist_item_id }}" role="button" aria-expanded="false" aria-controls="notes_{{ $checklist_item_id }}">
                                        <span class="fa-stack fa-2x mt-1">
                                            <i class="fa fa-comment fa-stack-1x @if($notes_unread_count > 0) text-orange @else @if($notes) text-primary @else text-blue-light @endif @endif"></i>
                                            @if($notes_unread_count > 0) <span class="fa-stack-1x notes-count text-white">{{ $notes_unread_count }}</span> @endif
                                        </span>
                                    </a>
                                </div>

                            </div>

                        </div>

                        <div id="notes_{{ $checklist_item_id }}" class="collapse checklist-item-notes-div bg-white mb-2" data-parent="#checklist_list_group">
                            <div class="mt-1 p-2 bg-white text-gray">
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                    <div class="font-weight-bold text-primary">Comments</div>
                                    <a data-toggle="collapse" href="#notes_{{ $checklist_item_id }}" role="button" aria-expanded="false" aria-controls="notes_{{ $checklist_item_id }}">
                                        <i class="fad fa-times-circle text-danger fa-lg"></i>
                                    </a>
                                </div>

                                <div class="notes-div my-2" data-checklist-item-id="{{ $checklist_item_id }}">
                                    <div class="text-gray">No Comments</div>
                                </div>

                            </div>
                            <div class="container">
                                <div class="row d-flex align-items-center bg-blue-light">
                                    <div class="col-10">
                                        <input type="text" class="custom-form-element form-input notes-input-{{ $checklist_item_id }}" data-label="Add Comment">
                                    </div>
                                    <div class="col-2 pl-0 mt-1">
                                        <a href="javascript: void(0)" class="btn btn-success btn-block save-notes-button" data-checklist-id="{{ $checklist_id }}" data-checklist-item-id="{{ $checklist_item_id }}"><i class="fa fa-save"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="documents-list bg-white p-2"></div>

                    </div>

                @endforeach


            @endforeach

        </div>

    </div>

</div>

<input type="hidden" id="Listing_ID" value="{{ $property -> Listing_ID }}">
<input type="hidden" id="Contract_ID" value="{{ $property -> Contract_ID }}">
<input type="hidden" id="Referral_ID" value="{{ $property -> Referral_ID }}">
<input type="hidden" id="Agent_ID" value="{{ $property -> Agent_ID }}">
<input type="hidden" id="transaction_type" value="{{ $transaction_type }}">
<input type="hidden" id="transaction_checklist_id" value="{{ $transaction_checklist_id }}">
<input type="hidden" id="property_id">
<input type="hidden" id="property_type">


@include('/agents/doc_management/transactions/details/shared/checklist_review_modals')
