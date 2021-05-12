<div class="container checklist-container p-1 p-md-4">
    <div class="row">
        <div class="col-12">
            <div class="mb-5">

                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="font-14 text-primary ml-3"><i class="fad fa-tasks mr-3"></i> {{ $checklist_type }} Checklist</div>
                            @if(auth() -> user() -> group == 'admin')
                            <div class="ml-4">
                                <button type="button" class="btn btn-sm btn-primary email-agent-button"><i class="fad fa-envelope mr-2"></i> Email Agent</button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @if($transaction_type != 'referral')
                    <div class="col-12 col-sm-6">
                        <div class="small text-danger text-right">Wrong Checklist? <a href="javascript: void(0)" class="btn btn-sm btn-primary" id="change_checklist_button" data-checklist-id="{{ $transaction_checklist_id }}"><i class="fad fa-repeat-alt mr-0 mr-sm-2"></i><span class="d-none d-sm-inline-block"> Change Checklist</span></a></div>
                    </div>
                    @endif
                </div>


                <hr class="mx-2 mt-0">

                @foreach($checklist_groups as $checklist_group)

                    @php
                    $group_name = $checklist_group -> resource_name;
                    if($group_name == 'Transaction Docs') {
                        $group_name = 'Listing Docs';
                        if($transaction_type == 'contract') {
                            $group_name = 'Contract Docs';
                            if($for_sale == false) {
                                $group_name = 'Lease Docs';
                            }
                        } else if($transaction_type == 'referral') {
                            $group_name = 'Referral Docs';
                        }
                    }
                    @endphp

                    {{-- Remove release docs for rentals --}}
                    @if($group_name == 'Release Docs' && $for_sale == false)
                    @else

                        <div class="h5 text-orange checklist-group-header pb-2 @if(!$loop -> first) mt-4 @else mt-3 @endif">
                            {{ $group_name }}
                            @if(auth() -> user() -> group == 'admin')
                            <button type="button" class="btn btn-sm btn-primary add-checklist-item-button" data-group-id="{{ $checklist_group -> resource_id }}"><i class="fal fa-plus"></i></button>
                            @endif
                        </div>

                        <div>

                        @if(count($transaction_checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id)) > 0)

                            @foreach($transaction_checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id) as $checklist_item)

                                @php

                                $form_help_html = null;

                                if($checklist_item -> checklist_form_id > 0) {

                                    $upload = $checklist_item -> upload;
                                    $checklist_item_name = $upload -> file_name_display;

                                    // details for helper popup
                                    $form_help_html = $upload -> helper_text;
                                    if($upload -> file_location != '') {
                                        $form_help_html .= '
                                        <hr>View Sample File<br><a href="'.$upload -> file_location.'" class="btn btn-primary" target="_blank">Open File</a>';
                                    }

                                } else {

                                    $checklist_item_name = $checklist_item -> checklist_item_added_name;

                                }

                                // get docs and notes for checklist item
                                $checklist_item_id = $checklist_item -> id;
                                $transaction_documents = $checklist_item -> docs;
                                $transaction_documents_count = count($transaction_documents);

                                $notes = $checklist_item -> notes;
                                $notes_count = count($notes);

                                $notes_count_unread = $notes -> where('note_status', 'unread');
                                if(stristr(auth() -> user() -> group, 'agent') || auth() -> user() -> group == 'transaction_coordinator') {
                                    $notes_count_unread = $notes_count_unread -> where('note_user_id', '!=', auth() -> user() -> id) -> count();
                                } else if(auth() -> user() -> group == 'admin') {
                                    $notes_count_unread = $notes_count_unread -> where('Agent_ID', '>', '0') -> count();
                                }


                                // get status
                                $status_details = $transaction_checklist_items_model -> GetStatus($checklist_item_id);
                                $status = $status_details -> status;
                                $badge_classes = $status_details -> agent_classes;
                                if(auth() -> user() -> group == 'admin') {
                                    $badge_classes = $status_details -> admin_classes;
                                }
                                $fa = $status_details -> fa;
                                $show_mark_required = $status_details -> show_mark_required;
                                $show_mark_not_required = $status_details -> show_mark_not_required;
                                $helper_text = $status_details -> helper_text;

                                // review status
                                $item_review_status = $checklist_item -> checklist_item_status;

                                $text_color = 'text-primary';
                                $rejected = '';
                                if($status != 'Required' && $status != 'Rejected') {
                                    $text_color = 'text-gray';
                                } else if($status == 'Rejected') {
                                    $rejected = 'rejected';
                                }
                                @endphp

                                <div class="checklist-item-div p-1 border-bottom {{ $rejected }}">

                                    <div class="row">

                                        <div class="col-12 @if(auth() -> user() -> group == 'admin') col-xl-5 @else col-xl-7 @endif">
                                            <div class="checklist-item-details d-flex justify-content-start flex-sm-nowrap align-items-center h-100 mb-lg-0">

                                                <div class="my-1 d-flex justify-content-start align-items-center">
                                                    <div class="status-badge badge {{ $badge_classes }} mr-2" title="{{ $helper_text }}">
                                                        {!! $fa . ' ' . $status !!}
                                                    </div>

                                                    @if(auth() -> user() -> group == 'admin')
                                                        <div>
                                                            <div class="dropdown">

                                                                <button class="btn btn-primary dropdown-toggle checklist-item-dropdown" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fal fa-bars"></i></button>

                                                                <div class="dropdown-menu dropdown-primary">

                                                                    <a class="dropdown-item mark-required no @if(!$show_mark_not_required) d-none @else d-block @endif" href="javascript: void(0)" data-checklist-item-id="{{ $checklist_item_id }}" data-required="no">Make If Applicable</a>

                                                                    <a class="dropdown-item mark-required yes @if(!$show_mark_required) d-none @else d-block @endif" href="javascript: void(0)" data-checklist-item-id="{{ $checklist_item_id }}" data-required="yes">Make Required</a>

                                                                    <a class="dropdown-item remove-checklist-item" href="javascript: void(0)" data-checklist-item-id="{{ $checklist_item_id }}">Remove</a>

                                                                </div>

                                                            </div>
                                                        </div>
                                                    @endif

                                                </div>

                                                <div class="mx-2 my-2 helper-wrapper">
                                                    <a href="javascript: void(0)" @if($checklist_item -> checklist_form_id > 0) role="button" class="checklist-item-helper" data-toggle="popover" data-html="true" data-trigger="focus" title="Document Details" data-content="{{ $form_help_html }}" @endif>
                                                        <i class="fad fa-question-circle @if($checklist_item -> checklist_form_id == 0) text-white @endif"></i>
                                                    </a>
                                                </div>

                                                <div class="mx-md-2 my-2 checklist-item-name {{ $text_color }}">{{ $checklist_item_name }}</div>

                                            </div>

                                        </div>



                                        <div class="col-12 @if(auth() -> user() -> group == 'admin') col-xl-7 @else col-xl-5 @endif">

                                            <div class="row mt-1">

                                                <div class="col-12 col-sm-6 @if(auth() -> user() -> group == 'admin') col-md-4 @endif">

                                                    <div class="checklist-item-options d-flex justify-content-between align-items-center p-1 my-1 bg-light">

                                                        <div class="font-weight-bold text-primary checklist-attachment">Docs</div>
                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-primary add-document-button" data-checklist-id="{{ $transaction_checklist_id }}" data-checklist-item-id="{{ $checklist_item_id }}" data-target="documents_div_{{ $checklist_item_id }}"><i class="fal fa-plus"></i></button>
                                                        </div>

                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-primary view-docs-button" data-toggle="collapse" data-target="#documents_div_{{ $checklist_item_id }}" aria-expanded="false" aria-controls="documents_div_{{ $checklist_item_id }}" @if($transaction_documents_count == 0) disabled @endif>View <span class="badge badge-pill bg-white text-danger font-weight-bold py-1 px-2 ml-2 doc-count">{{ $transaction_documents_count }}</span></button>
                                                        </div>

                                                        <div class="collapse documents-collapse mx-4 bg-white shadow" id="documents_div_{{ $checklist_item_id }}">

                                                            <div class="p-2">

                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <div class="h5 text-primary float-left">Submitted Documents</div>
                                                                            <a class="text-danger" data-toggle="collapse" href="#documents_div_{{ $checklist_item_id }}" aria-expanded="false" aria-controls="documents_div_{{ $checklist_item_id }}">
                                                                                <i class="fad fa-times-circle fa-2x"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <hr class="mt-1">

                                                                @foreach($transaction_documents as $transaction_document)
                                                                    @php
                                                                    $document_id = $transaction_document -> document_id;
                                                                    $doc_info = $documents_model -> GetDocInfo($document_id);
                                                                    @endphp

                                                                    <div class="d-flex justify-content-between align-items-center border-bottom document-row mb-2">
                                                                        <div class="d-flex justify-content-start align-items-center">

                                                                            <div class="mx-2"><a href="{{ $doc_info['file_location_converted'] }}" target="_blank" class="btn btn-sm btn-primary">View</a></div>

                                                                            <div>
                                                                                {{ $doc_info['file_name'] }}
                                                                                <br>
                                                                                <span class="small text-gray">Added: {{ date('n/j/Y g:i:sA', strtotime($transaction_document -> created_at)) }} </span>
                                                                            </div>

                                                                        </div>
                                                                        <div>
                                                                            <button type="button" class="btn btn-sm btn-danger float-right delete-doc-button" data-document-id="{{ $document_id }}" data-target="#documents_div_{{ $checklist_item_id }}" @if($item_review_status == 'accepted' && $transaction_document -> doc_status == 'viewed') disabled @endif>
                                                                                <i class="fal fa-times text-white"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                @endforeach

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="col-12 col-sm-6  @if(auth() -> user() -> group == 'admin') col-md-4 @endif">

                                                    <div class="checklist-item-options d-flex justify-content-betwen align-items-center p-1 pr-2 my-1 bg-light">

                                                        <div class="font-weight-bold text-primary checklist-attachment">Comments</div>

                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-primary add-notes-button" data-add-notes-div="add_notes_{{ $checklist_item_id }}" data-toggle="collapse" data-target="#notes_{{ $checklist_item_id }}" aria-expanded="false" aria-controls="notes_{{ $checklist_item_id }}"><i class="fal fa-plus"></i></button>
                                                        </div>

                                                        <div>
                                                            <button type="button" class="btn btn-sm @if($notes_count_unread > 0) btn-secondary @else btn-primary @endif view-notes-button" data-toggle="collapse" data-target="#notes_{{ $checklist_item_id }}" aria-expanded="false" aria-controls="notes_{{ $checklist_item_id }}" @if($notes_count == 0) disabled @endif>@if($notes_count_unread > 0) New! @else View @endif<span class="badge badge-pill bg-white text-danger font-weight-bold py-1 px-2 ml-2">{{ $notes_count }}</span></button>
                                                        </div>

                                                        <div class="collapse notes-collapse bg-white shadow" id="notes_{{ $checklist_item_id }}">

                                                            <div class=" px-0 px-sm-2 mb-2">

                                                                <div class="p-2">

                                                                    <div class="row">

                                                                        <div class="col-12">
                                                                            <div class="d-flex justify-content-between align-items-center">
                                                                                <div class="h5 text-primary float-left">Comments</div>
                                                                                <div>
                                                                                    <a class="text-danger" data-toggle="collapse" href="#notes_{{ $checklist_item_id }}" aria-expanded="false" aria-controls="notes_{{ $checklist_item_id }}"><i class="fad fa-times-circle fa-2x"></i></a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <hr class="mt-1">

                                                                    <div class="row">

                                                                        <div class="col-12">

                                                                            <div class="notes-div" data-checklist-item-id="{{ $checklist_item_id }}">

                                                                                @if(count($notes) > 0)

                                                                                    <div class="notes-container bg-white px-1">

                                                                                        @foreach($notes as $note)
                                                                                            @php
                                                                                            $user = $note -> user;
                                                                                            $username = $user -> name;

                                                                                            if($user -> group == 'admin') {
                                                                                                $emp_photo_location = auth() -> user() -> photo_location ?? null;
                                                                                                $avatar_bg = 'bg-orange';
                                                                                            } else if($user -> group == 'agent') {
                                                                                                $emp_photo_location = auth() -> user() -> photo_location ?? null;
                                                                                                $avatar_bg = 'bg-primary';
                                                                                            }
                                                                                            if(!$emp_photo_location) {
                                                                                                $initials = substr($user -> name, 0, 1);
                                                                                                $initials .= substr($user -> name, strpos($user -> name, ' ') + 1, 1);
                                                                                            }

                                                                                            $unread = null;
                                                                                            if($note -> note_status == 'unread' && $note -> note_user_id != auth() -> user() -> id) {
                                                                                                $unread = 'unread';
                                                                                            }

                                                                                            $created_at = $note -> created_at;
                                                                                            $date_added = $created_at -> format('n/j/Y g:iA');
                                                                                            if($created_at -> format('Y-m-d') == date('Y-m-d')) {
                                                                                                $date_added = 'Today at '.$created_at -> format('g:iA');
                                                                                            } else if($created_at -> format('Y-m-d') == date('Y-m-d', strtotime('-1 day'))) {
                                                                                                $date_added = 'Yesterday at '.$created_at -> format('g:iA');
                                                                                            }
                                                                                            @endphp

                                                                                            <div class="p-2 mb-3 note-div rounded @if($unread) bg-orange-light animate__animated animate__shakeX @else bg-blue-light @endif">

                                                                                                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                                                                                                    <div class="d-flex justify-content-start align-items-center">
                                                                                                        <div class="emp_photo mr-2">
                                                                                                            <div class="rounded-pill avatar-initials {{ $avatar_bg }}">
                                                                                                                @if($emp_photo_location)
                                                                                                                    <img src="{{ $emp_photo_location }}" class="avatar rounded-circle d-flex align-self-center mr-2 shadow">
                                                                                                                @else
                                                                                                                    <span class="text-white p-2">{{ $initials }}</span>
                                                                                                                @endif
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="text-primary font-italic">{{ $username }}</div>
                                                                                                    </div>
                                                                                                    <div>
                                                                                                        @if($note -> note_status == 'unread')
                                                                                                            @if($note -> note_user_id != auth() -> user() -> id)
                                                                                                                <button class="btn btn-success btn-sm mark-read-button mb-0" data-note-id="{{ $note -> id }}" data-notes-collapse="notes_div_{{ $checklist_item_id }}"><i class="fal fa-check mr-2"></i> Mark Read</button>
                                                                                                            @else
                                                                                                                <div class="d-flex justify-content-end align-items-center">
                                                                                                                    <span class="text-gray small">Not Read</span>
                                                                                                                    @if($note -> note_user_id == auth() -> user() -> id)
                                                                                                                        <a href="javascript: void(0)" class="delete-note-button ml-2" data-note-id={{ $note -> id }}"><i class="fad fa-times-circle text-danger"></i></a>
                                                                                                                    @endif
                                                                                                                </div>
                                                                                                            @endif
                                                                                                        @else
                                                                                                            <span class="text-success small"><i class="fal fa-check"></i> Read</span>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="text-gray bg-white p-2 rounded">
                                                                                                    {!! $note -> notes !!}
                                                                                                </div>

                                                                                                <div class="text-gray font-7 mt-1">{{ $date_added }}</div>

                                                                                            </div>



                                                                                        @endforeach

                                                                                    </div>

                                                                                @else

                                                                                    <div class="text-gray">No Comments</div>

                                                                                @endif

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                                <div class="d-flex align-items-center">
                                                                    <div class="w-90">
                                                                        <div>
                                                                            <textarea class="custom-form-element form-textarea notes-input-{{ $checklist_item_id }}" data-label="Add Comment"></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="w-10">
                                                                        <a href="javascript: void(0)" class="btn btn-primary save-notes-button ml-2" data-checklist-id="{{ $transaction_checklist_id }}" data-checklist-item-id="{{ $checklist_item_id }}"><i class="fad fa-save"></i></a>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>



                                                </div>




                                                @if(auth() -> user() -> group == 'admin')

                                                    <div class="col-12 col-sm-6 col-md-4">

                                                        @php
                                                        $bg_color = 'bg-light';
                                                        if($item_review_status == 'accepted') {
                                                            $bg_color = 'bg-green-light';
                                                        } else if($item_review_status == 'rejected') {
                                                            $bg_color = 'bg-red-light';
                                                        }
                                                        @endphp

                                                        <div class="checklist-item-options d-flex justify-content-between align-items-center my-1">

                                                            <div class="review-options p-1 {{ $bg_color }}">

                                                                <div class="@if($item_review_status == 'not_reviewed') d-flex @else d-none @endif justify-content-around align-items-center item-not-reviewed">
                                                                    <button type="button" class="btn btn-sm btn-success accept-checklist-item-button" data-checklist-item-id="{{ $checklist_item_id }}" @if($transaction_documents_count == 0) disabled @endif><i class="fal fa-check mr-2"></i> Accept</button>
                                                                    <button type="button" class="btn btn-sm btn-danger reject-checklist-item-button" data-checklist-item-id="{{ $checklist_item_id }}" @if($checklist_item -> checklist_item_required == 'yes') data-required="yes" @endif @if($transaction_documents_count == 0) disabled @endif><i class="fal fa-minus-circle mr-2"></i> Reject</button>
                                                                </div>

                                                                <div class="@if($item_review_status == 'accepted') d-flex @else d-none @endif justify-content-around align-items-center item-accepted">
                                                                    <button type="button" class="btn btn-sm btn-success" disabled><i class="fal fa-check mr-2"></i> Accepted</button>
                                                                    <div class="small mx-3">
                                                                        <a href="javascript: void(0)" class="undo-accepted" data-checklist-item-id="{{ $checklist_item_id }}" @if($checklist_item -> checklist_item_required == 'yes') data-required="yes" @endif ><i class="fal fa-undo mr-1"></i> Undo</a>
                                                                    </div>
                                                                </div>

                                                                <div class="@if($item_review_status == 'rejected') d-flex @else d-none @endif justify-content-around align-items-center  item-rejected">
                                                                    <div class="small mx-3">
                                                                        <a href="javascript: void(0)" class="undo-rejected" data-checklist-item-id="{{ $checklist_item_id }}" @if($checklist_item -> checklist_item_required == 'yes') data-required="yes" @endif ><i class="fal fa-undo mr-1"></i> Undo</a>
                                                                    </div>
                                                                    <button type="button" class="btn btn-sm btn-danger" disabled><i class="fal fa-minus-circle mr-2"></i> Rejected</button>
                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>

                                                @endif

                                            </div>

                                        </div>

                                    </div>


                                </div>

                            @endforeach

                        @else
                            <div class="text-gray">No Required Forms for this Group</div>
                        @endif

                    @endif

                @endforeach

            </div>
        </div>
    </div>
</div>

<input type="hidden" id="transaction_checklist_id" value="{{ $transaction_checklist_id }}">
