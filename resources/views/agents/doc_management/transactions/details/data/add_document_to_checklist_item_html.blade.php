<div class="container p-0 p-sm-1 p-md-2">
    <div class="row">
        <div class="col-12">
            <div class="h5 text-primary mb-3">Select the Document from the left and click "Assign" on the corresponding Checklist Item</div>
        </div>
    </div>

    <div class="row">

        <div class="col-12 col-md-5">

            <div class="h5 text-orange">Documents</div>

            <div class="border p-2 rounded">

                <div class="row">

                    <div class="d-none {{-- d-md-block col-md-2 pr-0 --}}">

                        <div class="doc-list-arrows-div ml-2">
                            <button type="button" class="btn btn-primary doc-list-arrow mb-2" data-dir="up"><i class="fal fa-arrow-up fa-2x"></i></button>
                            <br>
                            <button type="button" class="btn btn-primary doc-list-arrow mt-2" data-dir="down"><i class="fal fa-arrow-down fa-2x"></i></button>
                        </div>

                    </div>

                    <div class="col-12 {{-- col-md-10 pl-0 --}}">

                        <div id="add_to_checklist_documents_wrapper" class="list-group">

                            @foreach($documents as $document)

                                <div class="add-to-checklist-document-div list-group-item list-group-item-action border border-primary rounded my-2 @if($loop -> first) active shadow p-4 @else p-2 @endif" data-document-id="{{ $document -> id}}" data-file-name="{{ $document -> file_name_display }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="doc-name">
                                            {{ $document -> file_name_display }}
                                        </div>
                                        <div class="helper @if(!$loop -> first) hide @endif">
                                            <i class="fal fa-arrow-right fa-2x text-white ml-3"></i>
                                        </div>
                                    </div>
                                    <div class="assigned-div text-success">Assigned <i class="fad fa-check-circle"></i></div>
                                </div>

                            @endforeach


                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-12 col-md-7">

            <div class="h5 text-orange mt-3 mt-sm-0">Checklist Items</div>



            <div id="add_to_checklist_items_wrapper">

                <div id="add_to_checklist_items_div" class="list-group">

                    @foreach($checklist_groups as $checklist_group)

                        {{-- <div class="h5 text-primary mt-1 mb-0">~~~~~~~~~ {{ $checklist_group -> resource_name }} ~~~~~~~~~</div> --}}

                        @if(count($checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id)) > 0)

                            @foreach($checklist_items -> where('checklist_item_group_id', $checklist_group -> resource_id) as $checklist_item)

                                @php
                                $contract = '';
                                $release = '';
                                $closing_doc = '';
                                if($checklist_item -> checklist_form_id > 0) {
                                    $checklist_item_name = $checklist_items_model -> GetFormName($checklist_item -> checklist_form_id);
                                    if($upload -> IsRelease($checklist_item -> checklist_form_id)) {
                                        $release = 'release';
                                    }
                                    if($upload -> IsContract($checklist_item -> checklist_form_id)) {
                                        $contract = 'contract';
                                    }
                                    if($upload -> IsClosingDoc($checklist_item -> checklist_form_id)) {
                                        $closing_doc = 'closing_doc';
                                    }
                                } else {
                                    $checklist_item_name = $checklist_item -> checklist_item_added_name;
                                }
                                $status_details = $transaction_checklist_items_modal -> GetStatus($checklist_item ->  id);
                                $docs = $transaction_checklist_item_documents -> where('checklist_item_id', $checklist_item ->  id);
                                $docs_count = $docs -> count();
                                $status = $status_details -> status;
                                $agent_classes = $status_details -> agent_classes;



                                @endphp

                                <div class="list-group-item border rounded mb-3 p-1">

                                    <div class="text-gray d-flex justify-content-between">

                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="mr-2">
                                                <button type="button" class="btn btn-primary btn-sm assign-button {{ $release.' '.$contract.' '.$closing_doc.' '.strtolower($status) }}" data-checklist-id="{{ $checklist_item -> checklist_id }}" data-checklist-item-id="{{ $checklist_item ->  id }}" data-file-name="{{ $checklist_item_name }}"><i class="fal fa-plus mr-2"></i> Assign</button>
                                            </div>

                                            <div class="text-primary">
                                                {{ $checklist_item_name }}
                                            </div>
                                        </div>

                                        <div>
                                            <span class="badge checklist-item-badge p-1 {{ $agent_classes }} mr-2">{{ $status }}</span>
                                        </div>

                                    </div>


                                    <div class="submitted-docs-div border-top p-2 @if(count($docs) == 0) hide @endif">
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="font-weight-bold text-gray mr-3 no-wrap">Docs Added</div>
                                            <div class="submitted-docs w-100">
                                                @foreach ($docs as $doc)
                                                    @php
                                                    $document_details = $transaction_documents_model -> GetDocInfo($doc -> document_id);
                                                    $file_name = $document_details['file_name'];
                                                    @endphp
                                                    <div class="d-flex justify-content-start align-items-center docs small"><div><i class="fad fa-check-circle text-success mr-2"></i></div><div>{{ Str::limit($file_name, 85) }}</div></div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            @endforeach

                        @endif

                    @endforeach

                </div>

            </div>

        </div>
    </div>
</div>
