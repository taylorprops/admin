<div class="d-flex justify-content-between align-items-center mt-3 mb-2">
    <div class="d-flex justify-content-start align-items-center">
        <div class="h4 text-orange mt-2">Templates </div>
        <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Templates" data-content="Templates are used to automatically add fields and signers to any documents. <br><br>You can create a template by clicking the Add Template button and you will also be given the option prior to sending documents for signatures."><i class="fad fa-question-circle ml-2"></i></a>
    </div>
    <div>
        <a href="/esign/esign_template_add_documents_and_signers/user" class="btn btn-success add-template-button"><i class="fa fa-plus mr-2"></i> Create Template</a>
    </div>
</div>

<div class="mb-5 no-wrap">

    <table id="templates_table" class="table table-hover table-bordered" width="100%">

        <thead>
            <tr>
                <th class="wpx-100"></th>
                <th>Subject</th>
                <th>Recipients</th>
                <th>Documents</th>
                <th class="wpx-100">Created</th>
                <th class="wpx-50"></th>
            </tr>
        </thead>

        <tbody>

            @foreach($templates as $template)

                @php
                $signers = $template -> signers;
                $recipients = [];
                foreach($signers as $signer) {
                    $recipients[] = $signer -> signer_role;
                }

                @endphp
                <tr>
                    <td><a href="/esign/esign_template_add_fields/{{ $template -> template_type }}/{{ $template -> id }}" class="btn btn-primary" target="_blank">View/Edit <i class="fal fa-arrow-right ml-2"></i></a></td>
                    <td>{{ $template -> template_name }}</td>
                    <td>{!! implode(', ', $recipients) !!}</td>
                    <td>
                        <a href="{{ $template -> file_location }}" target="_blank">{{ shorten_text($template -> file_name, 45) }}</a>
                    </td>
                    <td class="no-wrap small" data-sort="{{ $template -> created_at }}">{{ date('M jS, Y', strtotime($template -> created_at)) }}<br>{{ date('g:i:s A', strtotime($template -> created_at)) }}</td>
                    <td class="text-center"><a href="javascript:void(0)" class="btn btn-danger delete-template-button" data-template-id="{{ $template -> id }}"><i class="fal fa-times"></i></a></td>
                </tr>
            @endforeach

        </body>

    </table>

</div>

<hr class="show-deleted-templates hidden">

<button class="btn btn-primary ml-0 mb-3 show-deleted-templates hidden" type="button" data-toggle="collapse" data-target="#deleted_templates_div" aria-expanded="false" aria-controls="deleted_templates_div">
    View Deleted Templates
</button>


