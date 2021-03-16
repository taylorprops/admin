<div class="d-flex justify-content-between align-items-center mt-3 mb-2">
    <div class="d-flex justify-content-start align-items-center">
        <div class="h4 text-orange mt-2">System Templates </div>
        <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Templates" data-content="System Templates are in-house templates used on forms we provide."><i class="fad fa-question-circle ml-2"></i></a>
    </div>
</div>

<div class="mb-5 table-responsive">

    <table id="system_templates_table" class="table table-hover table-bordered" width="100%">

        <thead>
            <tr>
                <th class="wpx-100"></th>
                <th>Name</th>
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
                    $recipients[] = $signer -> template_role;
                }
                $envelopes = $template -> envelopes;
                @endphp
                <tr>
                    <td><a href="/esign/esign_add_fields/0/yes/{{ $template -> id }}" class="btn btn-primary" target="_blank">View/Edit <i class="fal fa-arrow-right ml-2"></i></a></td>
                    <td>{{ $template -> template_name }}</td>
                    <td>{!! implode(', ', $recipients) !!}</td>
                    <td>
                        @foreach($envelopes as $envelope)
                            @php
                            $documents = $envelope -> documents;
                            @endphp
                            @foreach($documents as $document)
                                <a href="{{ $document -> file_location }}" target="_blank">{{ shorten_text($document -> file_name, 45) }}</a>
                                @if(!$loop -> last)<br> @endif
                            @endforeach
                        @endforeach
                    </td>
                    <td class="no-warp small" data-sort="{{ $template -> created_at }}">{{ date('M jS, Y', strtotime($template -> created_at)) }}<br>{{ date('g:i:s A', strtotime($template -> created_at)) }}</td>
                    <td class="text-center"><a href="javascript:void(0)" class="btn btn-danger delete-system-template-button" data-template-id="{{ $template -> id }}"><i class="fal fa-times"></i></a></td>
                </tr>
            @endforeach

        </body>

    </table>

</div>

<hr class="show-deleted-system-templates hidden">

<button class="btn btn-primary ml-0 mb-3 show-deleted-system-templates hidden" type="button" data-toggle="collapse" data-target="#deleted_system_templates_div" aria-expanded="false" aria-controls="deleted_system_templates_div">
    View Deleted Templates
</button>


