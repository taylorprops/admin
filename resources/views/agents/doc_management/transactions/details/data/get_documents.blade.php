<div class="p-1 pl-md-4 pr-md-4 pb-md-4 documents-container">

    <div class="row mb-3 mb-sm-2 mb-md-1">
        <div class="col-12">
            <div class="add-buttons-div mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a class="btn btn-primary" data-toggle="collapse" href="#add_documents_div" aria-expanded="false" aria-controls="add_documents_div"><i class="fal fa-plus mr-2"></i> Add Documents </a>
                    </div>
                    <div class="alert alert-warning mb-0 p-1" id="in_process_div">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="mr-4">
                                <i class="fad fa-cog fa-spin fa-lg"></i>
                            </div>
                            <div>
                                You have documents still being processed. They should be available soon.
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="javascript: void(0)" class="btn btn-primary add-folder-button"><i class="fal fa-plus mr-2"></i> Add Folder</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="collapse" id="add_documents_div">

        <div class="mt-0 mb-3">

            <div class="row">
                <div class="col-12 pr-4 mb-2">
                    <div class="float-right">
                        <a data-toggle="collapse" href="#add_documents_div" aria-expanded="false" aria-controls="add_documents_div"><i class="fal fa-times text-danger fa-3x"></i></a>
                    </div>
                </div>
            </div>


            <div class="row">
                @if($transaction_type != 'referral')
                    <div class="col-12 col-md-12 col-lg-6 px-1">
                        <div class="add-docs-div bg-blue-light p-3 mb-1 mb-sm-3 border border-primary rounded-lg text-center">
                            <i class="fad fa-clone fa-3x text-primary mb-2"></i>
                            <div class="h5 text-primary mb-3">Templates <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Adding Template Documents" data-content="Add a preset template of all forms available for the checklist<br><br>or<br><br>Add a preset template of individual forms from all Associations"><i class="fad fa-question-circle ml-2"></i></a></div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    Checklist Documents<br>
                                    <a href="javascript:void(0);" class="btn btn-primary" id="add_checklist_template_button"><i class="fal fa-plus mr-2"></i> Add Template</a>
                                </div>
                                <div class="col-12 col-sm-6">
                                    Individual Documents<br>
                                    <a href="javascript:void(0);" class="btn btn-primary" id="add_individual_template_button"><i class="fal fa-plus mr-2"></i> Add Documents</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-12 col-sm-6 col-lg-3 px-1">
                    <div class="add-docs-div bg-blue-light p-3 mb-1 border border-primary rounded-lg text-center">
                        <i class="fad fa-file-upload fa-3x text-primary mb-2"></i>
                        <div class="h5 text-primary mb-3">Upload Documents <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Uploading Documents" data-content="Upload documents from your computer"><i class="fad fa-question-circle ml-2"></i></a></div>
                        <a href="javascript:void(0);" class="btn btn-primary mt-1 mt-md-4" id="upload_documents_button"><i class="fal fa-plus mr-2"></i> Upload Documents</a>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 px-1">
                    <div class="add-docs-div bg-blue-light p-3 mb-1 border border-primary rounded-lg text-center">
                        <i class="fad fa-envelope-square fa-3x text-primary mb-2"></i>
                        <div class="h5 text-primary mb-3">Email Documents <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Emailing Documents" data-content="Email documents to the address below and they will show up in a queue below. Please allow a little time for the email to be processed. You do not need to refresh the screen, the emailed documents will appear when complete."><i class="fad fa-question-circle ml-2"></i></a></div>
                        <div class="w-100 overflow-hidden">
                            <a href="mailto:{{ $property_email }}" target="_blank" class="d-block mt-1 mt-md-5">{{ $property_email }}</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="row">

        <div class="col-12 col-xl-8">

            <div id="emailed_documents_container" class="p-4 mb-3 border rounded animate__animated animate__fadeIn">

                <h5 class="text-orange"><i class="fad fa-envelope mb-3 mr-3"></i> Pending Emailed Documents</h5>

                <div class="list-group mb-3" id="emailed_documents_div"></div>

                <div class="pt-3 mt-4 d-flex justify-content-around border-top">
                    <select class="custom-form-element form-select form-select-no-search" id="emailed_documents_folder" data-label="Select Folder">
                        @foreach($folders as $folder)
                            @php
                            $folder_name = $folder -> folder_name;
                            if($for_sale == false) {
                                $folder_name = str_replace('Contract', 'Lease', $folder_name);
                            }

                            if($transaction_type == 'listing') {
                                $selected_folder = 'Listing Documents';
                            } else if($transaction_type == 'contract') {
                                $selected_folder = 'Contract Documents';
                                if($for_sale == false) {
                                    $selected_folder = 'Lease Documents';
                                }
                            } else if($transaction_type == 'referral') {
                                $selected_folder = 'Referral Documents';
                            }
                            @endphp
                            @if($folder -> folder_name != 'Trash')
                            <option value="{{ $folder -> id }}" @if($selected_folder == $folder_name) selected @endif >{{ $folder_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-around mt-3">
                    <a href="javascript: void(0)" class="btn btn-primary" id="add_emailed_documents_button"><i class="fad fa-download mr-2"></i> Import Documents</a>
                </div>

            </div>

        </div>

    </div>


    <div class="collapse" id="bulk_options_div">
        <div class="p-3">
            <div class="h5 text-orange d-none d-md-block">Bulk Options</div>
            <div class="d-flex justify-content-between justify-content-xl-start">

                <button type="button" class="btn btn-sm btn-primary add-to-checklist-button" title="Assign To Checklist" data-toggle="tooltip" data-checklist-id="{{ $checklist_id }}"><i class="fad fa-tasks mr-0 mr-md-2"></i><span class="button-text"> Assign To Checklist</span></button>

                <button type="button" class="btn btn-sm btn-primary doc-get-signed-button" title="Get Signed" data-toggle="tooltip"><i class="fad fa-signature mr-0 mr-md-2"></i><span class="button-text"> Get Signed</span></button>

                <div class="dropright" title="Print Documents">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fad fa-envelope mr-0 mr-md-2"></i><span class="button-text"> Email</span></button>
                    <div class="dropdown-menu">
                        <a class="text-primary dropdown-item docs-email-button" data-docs-type="merged" href="javascript: void(0)"><i class="fad fa-file-alt mr-2 fa-lg"></i> As One Document</a>
                        <a class="text-primary dropdown-item docs-email-button" data-docs-type="single" href="javascript: void(0)"><i class="fad fa-file mr-2 fa-lg"></i> As Individual Documents</a>
                    </div>
                </div>

                <div class="dropright" title="Print Documents">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fad fa-print mr-0 mr-md-2"></i><span class="button-text"> Print</span></button>
                    <div class="dropdown-menu">
                        <a class="text-primary dropdown-item docs-print-button" data-type="filled" href="javascript: void(0)"><i class="fad fa-file-alt mr-2 fa-lg" data-type="filled"></i> Print Filled</a>
                        <a class="text-primary dropdown-item docs-print-button" data-type="blank" href="javascript: void(0)"><i class="fad fa-file mr-2 fa-lg" data-type="blank"></i> Print Blank</a>
                    </div>
                </div>

                <div class="dropright" title="Download Documents">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fad fa-download mr-0 mr-md-2"></i><span class="button-text"> Download</span></button>
                    <div class="dropdown-menu">
                        <a class="text-primary dropdown-item docs-download-button" data-type="filled" href="javascript: void(0)"><i class="fad fa-file-alt mr-2 fa-lg" data-type="filled"></i> Download Filled</a>
                        <a class="text-primary dropdown-item docs-download-button" data-type="blank" href="javascript: void(0)"><i class="fad fa-file mr-2 fa-lg" data-type="blank"></i> Download Blank</a>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-primary move-documents-button" title="Move Documents" data-toggle="tooltip"><i class="fad fa-exchange mr-0 mr-md-2"></i><span class="button-text"> Move</span></button>

                <button type="button" class="btn btn-sm btn-danger delete-documents-button"  title="Move Documents To Trash" data-toggle="tooltip"><i class="fad fa-trash mr-0 mr-md-2"></i><span class="button-text"> Trash</span></button>
            </div>
        </div>
    </div>

    @foreach($folders as $folder)

        @php
        $docs_count = $documents -> where('folder', $folder -> id) -> count();

        $show_folder = '';
        $folder_name = $folder -> folder_name;

        if($transaction_type == 'listing') {
            if($folder_name == 'Listing Documents' && $docs_count > 0) {
                $show_folder = 'show';
            }
        } else if($transaction_type == 'contract') {
            if($folder_name == 'Contract Documents' && $docs_count > 0) {
                $show_folder = 'show';
            }
        } else if($transaction_type == 'referral') {
            if($folder_name == 'Referral Documents' && $docs_count > 0) {
                $show_folder = 'show';
            }
        }

        if($for_sale == false) {
            $folder_name = str_replace('Contract', 'Lease', $folder_name);
        }

        $deletable_folder = true;
        if(preg_match('/(Listing\sDocuments|Contract\sDocuments|Lease\sDocuments|Referral\sDocuments|Trash)/', $folder_name)) {
            $deletable_folder = false;
        }
        @endphp
        <div class="folder-div mb-4 border-top border-bottom border-primary" data-folder="{{ $folder -> id }}">

            <div class="folder-header d-flex justify-content-between">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="mt-1 mr-2 mr-sm-4">
                        <input type="checkbox" class="custom-form-element form-checkbox check-all">
                    </div>
                    <div class="h5 mt-2">
                        <a class="folder-collapse text-orange" data-toggle="collapse" href="#documents_folder_{{ $loop -> index }}" aria-expanded="false" aria-controls="documents_folder_{{ $loop -> index }}">
                            <i class="fad fa-angle-right fa-lg mr-3"></i>
                            <i class="fad fa-folder mr-1 mr-sm-3 fa-lg"></i>
                            {{ $folder_name }}
                        </a>
                        <span class="badge badge-pill badge-primary ml-1 ml-sm-3 py-1 docs-count">{{ $docs_count }}</span>
                    </div>
                </div>
                @if($deletable_folder && $documents -> where('folder', $folder -> id) -> where('assigned', 'yes') -> count() == 0)
                <div class="pt-1">
                    <a href="javascript: void(0)" class="btn btn-sm btn-danger delete-folder-button" data-folder-id="{{ $folder -> id }}"><i class="fad fa-trash"></i> <span class="d-none d-sm-inline-block ml-2 delete-folder-button" data-folder-id="{{ $folder -> id }}">Delete Folder</span></a>
                </div>
                @endif
            </div>


            <div class="collapse sortable-documents @if($folder_name != 'Trash') {{ $show_folder }} @endif" id="documents_folder_{{ $loop -> index }}" data-folder-id="{{ $folder -> id }}">

                @if(count($documents) > 0)

                    @foreach($documents as $document)

                        @if($document -> folder == $folder -> id)

                            @php
                            $assigned = $document -> assigned == 'yes' ? 'assigned' : null;
                            $disabled = $assigned == 'yes' ? 'disabled' : null;
                            @endphp

                            <div class="document-div row mx-0 py-0" data-folder-id="{{ $folder -> id }}" data-document-id="{{ $document -> id }}">

                                <div class="col-10 col-xl-5">

                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="mr-2">
                                            <a href="javascript:void(0)" class="document-handle text-blue"><i class="fad fa-bars fa-lg"></i></a>
                                        </div>
                                        <div class="mx-2 mr-md-4">
                                            <input type="checkbox" class="custom-form-element form-checkbox check-document  {{ $assigned }}" data-document-id="{{ $document -> id }}">
                                        </div>
                                        <div class="mr-2 in-process-icon">
                                            <i class="fad fa-cog fa-spin fa-lg text-orange"></i>
                                        </div>
                                        <div class="text-gray document-title py-1 py-sm-2">
                                            <a href="{{ $document -> file_location_converted }}?0293847" target="_blank">{{ $document -> file_name_display }}</a>
                                            <div class="d-flex justify-content-start flex-wrap">
                                                <div>
                                                    <span class="small">Added: {{ date('n/j/Y g:i:sA', strtotime($document -> created_at)) }} </span>
                                                </div>
                                                <div>
                                                    @if($document -> file_type == 'user')
                                                        <span class="badge badge-secondary p-1 ml-2">User File</span>
                                                    @else
                                                        <span class="badge badge-primary p-1 ml-2">System File</span>
                                                    @endif
                                                    <span class="small ml-2">{{ get_mb(filesize(Storage::disk('public') -> path(str_replace('/storage/', '', $document -> file_location_converted)))).'MB' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-2 col-xl-7">

                                    <div class="d-flex justify-content-end align-items-center h-100">

                                        @if($folder_name != 'Trash')

                                            @php
                                            $menu_options = '';

                                            if($assigned) {

                                                $menu_options .= '<div class="mr-1 mb-2 ml-2 mb-xl-0 ml-xl-0 text-success"><i class="fal fa-check mr-2"></i> <span class="d-inline-block d-xl-inline-block"> Assigned</span></div>';

                                                $menu_options .= '<button type="button" class="dropdown-item text-primary doc-rename-button" data-document-id="'.$document -> id.'" data-document-name="'.$document -> file_name_display.'" title="Rename Document"><i class="fad fa-repeat mr-1 "></i> Rename</button>';

                                            } else {

                                                $menu_options .= '<button type="button" class="dropdown-item text-primary add-to-checklist-button" data-document-id="'.$document -> id.'"  data-checklist-id="'.$checklist_id.'" title="Assign Document To Checklist Item"><i class="fad fa-tasks mr-1 "></i> Assign</button>';

                                                $menu_options .= '<button type="button" class="dropdown-item text-primary doc-rename-button" data-document-id="'.$document -> id.'" data-document-name="'.$document -> file_name_display.'" title="Rename Document"><i class="fad fa-repeat mr-1 "></i> Rename</button>';

                                                if($document -> pages_total > 1 && $document -> file_type == 'user') {
                                                    $menu_options .= '<button type="button" class="dropdown-item text-primary doc-split-button" data-document-id="'.$document -> id.'" data-checklist-id="'.$checklist_id.'" data-file-name="'.$document -> file_name_display.'" data-file-type="'.$document -> file_type.'" data-folder="'.$folder -> id.'" title="Split Document"><i class="fad fa-page-break mr-1 "></i> Split</button>';
                                                }

                                                $menu_options .= '<button type="button" class="dropdown-item text-primary doc-edit-button" onClick="window.open(\'/agents/doc_management/transactions/edit_files/'.$document -> id.'\')" data-document-id="'.$document -> id.'" title="Edit and Fill Fields"><i class="fad fa-edit mr-1 "></i> Edit/Fill</button>';

                                                $menu_options .= '<button type="button" class="dropdown-item text-primary doc-get-signed-button" data-document-id="'.$document -> id.'" title="Get Signed"><i class="fad fa-signature mr-1 "></i> Get Signed</button>';

                                            }



                                            $menu_options .= '<button type="button" class="dropdown-item text-primary doc-duplicate-button" data-document-id="'.$document -> id.'" data-file-type="'.$document -> file_type.'" title="Make Copy Of Form"><i class="fad fa-clone mr-2 mr-xl-0"></i><span class="d-inline-block d-xl-none"> Make Copy</span></button>';

                                            $menu_options .= '<button type="button" class="dropdown-item text-primary doc-email-button" data-document-id="'.$document -> id.'" title="Email Form"><i class="fad fa-envelope mr-2 mr-xl-0"></i><span class="d-inline-block d-xl-none"> Email</span></button>';

                                            $menu_options .= '
                                            <div class="dropdown-submenu">
                                                <button type="button" class="dropdown-item text-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Download Form"><i class="fad fa-download mr-2 mr-xl-0"></i><span class="d-inline-block d-xl-none"> Download</span></button>
                                                <div class="dropdown-menu">
                                                    <a class="text-primary dropdown-item" href="'.$document -> file_location_converted.'" download="'.$document -> file_name_display.'"><i class="fad fa-file-alt mr-2 fa-lg"></i> Download Filled</a>
                                                    <a class="text-primary dropdown-item" href="'.$document -> file_location.'" download="'.$document -> file_name_display.'"><i class="fad fa-file mr-2 fa-lg"></i> Download Blank</a>
                                                </div>
                                            </div>';

                                            $menu_options .= '
                                            <div class="dropdown-submenu">
                                                <button type="button" class="dropdown-item text-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Print Form"><i class="fad fa-print mr-2 mr-xl-0"></i><span class="d-inline-block d-xl-none"> Print</span></button>
                                                <div class="dropdown-menu">
                                                    <a class="text-primary dropdown-item doc-print-button" href="javascript: void(0)" data-link="'.$document -> file_location_converted.'"><i class="fad fa-file-alt mr-2 fa-lg"></i> Print Filled</a>
                                                    <a class="text-primary dropdown-item doc-print-button" href="javascript: void(0)" data-link="'.$document -> file_location.'" data-filename="'.$document -> file_name_display.'"><i class="fad fa-file mr-2 fa-lg"></i> Print Blank</a>
                                                </div>
                                            </div>';

                                            $menu_options .= '<div class="dropdown-divider d-block d-xl-none"></div>';

                                            if(!$assigned) {

                                                $menu_options .= '<button type="button" class="dropdown-item text-danger doc-delete-button" data-document-id="'.$document -> id.'" data-document-name="' . $document -> file_name_display . '" title="Delete Form"><i class="fad fa-trash mr-2 mr-xl-0"></i><span class="d-inline-block d-xl-none"> Trash</span></button>';

                                            }

                                            $menu_options_large = preg_replace('/dropdown-item\stext-primary/', 'btn btn-sm btn-primary', $menu_options);
                                            $menu_options_large = preg_replace('/dropdown-item\stext-danger/', 'btn btn-sm btn-danger', $menu_options_large);
                                            $menu_options_large = preg_replace('/dropdown-submenu/', 'dropleft', $menu_options_large);
                                            @endphp

                                            <div class="d-block d-xl-none dropleft">
                                                <button type="button" class="btn btn-primary dropdown-toggle pl-2 pr-1 py-0 pl-sm-2 pt-sm-1 pb-sm-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                                <div class="dropdown-menu">
                                                    {!! $menu_options !!}
                                                </div>
                                            </div>

                                            <div class="d-none d-xl-flex align-items-center">
                                                {!! $menu_options_large !!}
                                            </div>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        @endif

                    @endforeach

                @endif

            </div>

        </div>

    @endforeach

</div>

