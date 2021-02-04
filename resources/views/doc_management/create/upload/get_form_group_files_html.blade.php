@php
$category_color_ids = [];
foreach($files -> pluck('form_categories') as $categories) {
    $category_color_ids = array_unique(array_merge(explode(',', $categories), $category_color_ids));
}
$colors_array = [];
$color_names = [];
foreach($category_color_ids as $color_id) {
    $colors_array[$color_id] = $resource_items -> GetCategoryColor($color_id);
    $color_names[$color_id] = $resource_items -> getResourceName($color_id);
}

@endphp

@foreach ($files as $file)

    @php
    $checklist_count = $checklists -> countInChecklist($file -> file_id);
    $show_title = false;
    @endphp

    <div class="p-2 mb-4 uploads-list @if($file -> published == 'yes') published @else notpublished @endif @if($file -> active == 'yes') active @else notactive @endif" data-form-name="{{ $file -> file_name_display }}">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="h5 text-secondary" @if($show_title) title="{{ $file -> file_name_display }}" @endif>@if($file -> file_location != '') <i class="fad fa-file-plus mr-2 text-success"></i> @else <i class="fad fa-file-minus mr-2 text-gray"></i> @endif <a href="{{ $file -> file_location }}" target="_blank">{{ $file -> file_name_display }}</a></div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex justify-content-end">
                        @php $categories = explode(',', $file -> form_categories); @endphp
                        @foreach($categories as $category_id)
                        <span class="badge badge-pill text-white ml-1" style="background-color: {{ $colors_array[$category_id] }}">{{ $color_names[$category_id] }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 options-holder">
                    <div class="d-flex justify-content-start align-items-center flex-wrap">

                        @if($file -> published == 'yes')

                            <span class="badge @if($file -> active == 'yes') badge-success @else badge-danger @endif text-white" data-toggle="tooltip" data-html="true" title="Fields for this form can no longer be edited and the from can no longer be deleted"><i class="fal @if($file -> active == 'yes') fa-check @else fa-ban @endif mr-2"></i> Published</span>

                            <div>
                                <div class="badge @if($checklist_count > 0) badge-primary text-white @else bg-blue-light text-orange @endif" data-toggle="tooltip" data-html="true" title="Found in {{ $checklist_count }} checklists">
                                    {{ $checklist_count }}
                                </div>
                            </div>

                        @endif

                        @if($file -> file_location != '')

                            <a href="/doc_management/create/add_fields/{{ $file -> file_id }}" class="btn btn-sm btn-primary ml-0 add-edit-button" title="Add fields to the form" target="_blank"><i class="fad fa-rectangle-wide mr-2"></i> Fillable Fields</a>

                            @if($file -> published == 'no')
                                <div>
                                    <div class="badge badge-primary text-white" data-toggle="tooltip" data-html="true" title="{{ $file -> fields_count }} Fields Added">
                                        {{ $file -> fields_count }}
                                    </div>
                                </div>
                            @endif

                            <a href="/esign/esign_add_documents_from_uploads/{{ $file -> file_id }}/yes" class="btn btn-sm btn-primary ml-0 add-edit-button" title="Add signature fields to the form" target="_blank"><i class="fal fa-signature mr-2"></i> Signature Fields</a>

                        @endif

                        @if($file -> published == 'yes')

                            <div>
                                @if($file -> active == 'yes')
                                    <span data-toggle="tooltip" data-html="true" @if($checklist_count > 0) title="You can only deactivate a form that is not in any checklists. It must first be removed from all checklists" @else title="Once deactivated you can no longer add the form to checklists" @endif>
                                        <button type="button" class="activate-upload btn btn-sm btn-danger" data-id="{{ $file -> file_id }}" data-active="no" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}" @if($checklist_count > 0) disabled @endif><i class="fad fa-toggle-on mr-2"></i> Deactivate</button>
                                    </span>
                                @else
                                    <button class="activate-upload btn btn-sm btn-success" data-id="{{ $file -> file_id }}" data-active="yes" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}" {{-- data-toggle="tooltip" data-html="true" --}} title="Reactivate form"><i class="fad fa-toggle-off mr-2"></i> Activate</button>
                                @endif
                            </div>
                            @if($file -> active == 'yes')
                                <div>
                                    <span {{-- data-toggle="tooltip" data-html="true" --}} title="Manage this form and its checklist relations">
                                        <button class="manage-upload btn btn-sm btn-primary" data-id="{{ $file -> file_id }}" data-form-group-id="{{ $form_group_id }}"><i class="fal fa-bars mr-2"></i> Manage Form</button>
                                    </span>
                                </div>
                            @endif
                        @endif
                        <div>
                            <button class="edit-upload btn btn-sm btn-primary" data-id="{{ $file -> file_id }}" {{-- data-toggle="tooltip" data-html="true" --}} title="Edit form details"><i class="fad fa-edit mr-2"></i> Edit</button>
                        </div>
                        <div>
                            <button class="duplicate-upload btn btn-sm btn-primary" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}"  {{-- data-toggle="tooltip" data-html="true" --}} title="Create a duplicate of the file including all added fields"><i class="fad fa-clone mr-2"></i> Duplicate</button>
                        </div>
                        @if($file -> published == 'no')
                            <div>
                                <button class="publish-upload btn btn-sm btn-success" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}"  data-toggle="tooltip" data-html="true" title="Once published you can add the form to checklists. It will also be available for agents to access. It cannot be unpublished!"><i class="fad fa-file-export mr-2"></i> Publish</button>
                            </div>
                            <div>
                                <button class="delete-upload btn btn-sm btn-danger" data-id="{{ $file -> file_id }}" data-state="{{ $state }}" data-form-group-id="{{ $form_group_id }}"  {{-- data-toggle="tooltip" data-html="true" --}} title="Permantly delete form"><i class="fad fa-trash-alt mr-2"></i> Delete</button>
                            </div>
                        @endif

                    </div>

                    <div>
                        <div class="small text-gray mt-2">Added: {{ date('M jS, Y', strtotime($file -> created_at)) }} {{ date('g:i A', strtotime($file -> created_at)) }}</div>
                    </div>

                </div>
            </div>

        </div><!-- ./ .container -->
    </div>

@endforeach
<input type="hidden" class="files-count" value="{{ $files_count }}">
<input type="hidden" class="form-group-state" value="{{ $state }}">
<input type="hidden" class="form-group-id" value="{{ $form_group_id }}">
