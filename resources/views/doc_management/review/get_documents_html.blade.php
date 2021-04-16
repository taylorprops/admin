@if(count($checklist_item_documents) == 0)
    @php return false; @endphp
@endif
<div class="row animate__animated animate__fadeIn">

    <div class="col-12">

        <div class="review-container">

            <div class="review-image-container">

                <div class="review-image-div mx-auto">

                    @foreach($checklist_item_documents as $document)

                        @php
                        // $document_id = $document -> document_id;
                        // $document_images = $checklist_item_images_model -> where('document_id', $document_id) -> orderBy('page_number') -> get();
                        // $document_details = $transaction_documents_model -> GetDocInfo($document_id);
                        // $file_name = $document_details['file_name'];
                        // $file_location = $document_details['file_location_converted'];

                        $document_id = $document -> document_id;
                        $document_images = $document -> images;
                        $file_name = $document -> original_doc -> file_name_display;
                        $file_location = $document -> file_location_converted;
                        @endphp

                        <div class="bg-blue-light text-primary p-2 d-flex justify-content-start align-items-center" id="document_{{ $document_id }}">

                            <div class="mx-4">
                                <a href="{{ $file_location }}" target="_blank">
                                    <i class="fad fa-download fa-lg"></i>
                                </a>
                            </div>

                            <div class="h5 mt-2">
                                {{ $file_name }}
                            </div>

                        </div>

                        @foreach($document_images as $document_image)

                            <div class="p-0 mb-2 d-block border" data-document-id="{{ $document_image -> document_id }}">
                                <img src="{{ $document_image -> file_location }}" class="document-review-image">
                            </div>

                        @endforeach

                        {{-- this will be removed and appended to list-group-item.active --}}
                        <div class="checklist-item-docs-div bg-white p-1 font-8">
                            <a href="javascript:void(0)" class="document-link d-block text-gray p-1 rounded" data-toggle="tooltip" @if(strlen($file_name) > 70) title="{{ $file_name }}" @endif data-document-id="{{ $document_id }}">{{ Str::limit($file_name, 70) }}</a>
                        </div>

                    @endforeach

                </div>

            </div>

            <div class="document-options-div w-100 border-right">

                <div class="d-flex justify-content-between align-items-center">

                    @php
                    $item_review_status = $checklist_item -> checklist_item_status;

                    $bg_color = 'bg-light';
                    if($item_review_status == 'accepted') {
                        $bg_color = 'bg-green-light';
                    } else if($item_review_status == 'rejected') {
                        $bg_color = 'bg-red-light';
                    }
                    @endphp

                    <div class="review-options pl-2 pl-xl-3 pr-2 {{ $bg_color }}">

                        <div class="@if($item_review_status == 'not_reviewed') d-flex @else d-none @endif justify-content-around align-items-center mb-1 item-not-reviewed w-100 h-100 p-1">
                            <button type="button" class="btn btn-success accept-checklist-item-button mr-2 mr-xl-3"><i class="fal fa-check mr-2"></i> Accept</button>
                            <button type="button" class="btn btn-danger reject-checklist-item-button" @if($checklist_item -> checklist_item_required == 'yes') data-required="yes" @endif><i class="fal fa-minus-circle mr-2"></i> Reject</button>
                        </div>

                        <div class="@if($item_review_status == 'accepted') d-flex @else d-none @endif justify-content-around align-items-center mb-xl-1 item-accepted w-100 h-100 p-1 pl-4">
                            <div class="text-success">
                                <i class="fal fa-check-circle mr-2"></i> Accepted
                            </div>
                            <div class="ml-2 ml-xl-3">
                                <a href="javascript: void(0)" class="btn btn-primary undo-accepted" @if($checklist_item -> checklist_item_required == 'yes') data-required="yes" @endif ><i class="fal fa-undo mr-1"></i> Undo</a>
                            </div>
                        </div>

                        <div class="@if($item_review_status == 'rejected') d-flex @else d-none @endif justify-content-around align-items-center mb-xl-1 item-rejected w-100 h-100 p-1 pl-4">
                            <div class="text-danger">
                                <i class="fad fa-times-circle mr-2"></i> Rejected
                            </div>
                            <div class="ml-2 ml-xl-3">
                                <a href="javascript: void(0)" class="btn btn-primary undo-rejected" @if($checklist_item -> checklist_item_required == 'yes') data-required="yes" @endif ><i class="fal fa-undo mr-1"></i> Undo</a>
                            </div>
                        </div>

                    </div>

                    <div class="scroll-arrows">
                        <a href="javascript: void(0);" class="btn btn-primary" id="scroll_up"><i class="fal fa-chevron-double-up"></i></a>
                        <a href="javascript: void(0);" class="btn btn-primary" id="scroll_down"><i class="fal fa-chevron-double-down"></i></a>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="completed-options">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="javascript: void(0);" class="btn btn-sm btn-primary email-agent-button"><i class="fad fa-envelope mr-2"></i> Email</a>
                                </div>
                                <div>
                                    <a href="javascript: void(0);" class="btn btn-sm btn-primary next-button">Next <i class="fal fa-chevron-double-right ml-2"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mr-2">
                        <div class="d-flex justify-content-center align-items-center">

                            <input id="zoom" type="text"data-slider-id='zoomSlider' data-slider-min="50" data-slider-max="150" data-slider-step="5" data-slider-value="85" />


                            {{-- <span class="font-weight-bold text-primary mr-2 zoom-out"><i class="fal fa-minus-circle"></i></span>
                            <form class="range-field ml-0 mr-0">
                                <input class="border-0" id="zoom" type="range" min="50" max="150" value="85" />
                            </form>
                            <span class="font-weight-bold text-primary ml-2 zoom-in"><i class="fal fa-plus-circle"></i></span> --}}


                        </div>
                    </div>


                </div>

            </div>

        </div>

    </div>

</div>
