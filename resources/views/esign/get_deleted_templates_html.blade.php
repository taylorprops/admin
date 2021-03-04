<div class="h4 text-orange my-3">Deleted Templates</div>

<div class="table-responsive text-nowrap">

    <table id="deleted_templates_table" class="table table-bordered" width="100%">

        <thead>
            <tr>
                <th class="wpx-100"></th>
                <th>Subject</th>
                <th>Recipients</th>
                <th>Documents</th>
                <th class="wpx-100">Created</th>
            </tr>
        </thead>

        <tbody>

            @foreach($deleted_templates as $template)

                @php
                $signers = $template -> signers;
                $recipients = [];
                foreach($signers as $signer) {
                    $recipients[] = $signer -> template_role;
                }
                $envelopes = $template -> envelopes;
                @endphp
                <tr>
                    <td><a href="javascript:void(0)" class="btn btn-primary restore-template-button" data-template-id="{{ $template -> id }}">Restore template <i class="fal fa-undo ml-2"></i></a></td>
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
                    <td class="no-wrap small" data-sort="{{ $template -> created_at }}">{{ date('M jS, Y', strtotime($template -> created_at)) }}<br>{{ date('g:i:s A', strtotime($template -> created_at)) }}</td>
                </tr>
            @endforeach

        </body>

    </table>

</div>
<input type="hidden" id="deleted_templates_count" value="{{ count($deleted_templates) }}">
