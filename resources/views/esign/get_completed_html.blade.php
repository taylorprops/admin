
<div class="d-flex justify-content-start align-items-center mt-3 mb-2">
    <div class="h4 text-orange mt-2">Completed </div>
    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Completed" data-content="These are your completed envelopes. They have been sgined by all parties."><i class="fad fa-question-circle ml-2"></i></a>
</div>

<div class="mb-5 table-responsive">

    <table id="completed_table" class="table table-hover table-bordered" width="100%">

        <thead>
            <tr>
                <th class="wpx-100"></th>
                <th>Subject</th>
                <th>Recipients</th>
                <th>Documents</th>
                <th class="wpx-100">Created</th>
                <th class="wpx-125"></th>
            </tr>
        </thead>

        <tbody>

            @foreach($envelopes as $envelope)

                @php
                $signers = $envelope -> signers;
                $recipients = [];
                foreach($signers as $signer) {
                    $recipients[] = $signer -> signer_name;
                }
                $documents = $envelope -> documents;
                @endphp
                <tr>
                    <td>Completed</td>
                    <td>{{ $envelope -> subject }}</td>
                    <td>{!! implode(', ', $recipients) !!}</td>
                    <td>
                        @foreach($documents as $document)
                            {{ shorten_text($document -> file_name, 45) }}
                            @if(!$loop -> last)<br> @endif
                        @endforeach
                    </td>
                    <td class="no-wrap small" data-sort="{{ $envelope -> created_at }}">{{ date('M jS, Y', strtotime($envelope -> created_at)) }}<br>{{ date('g:i:s A', strtotime($envelope -> created_at)) }}</td>
                    <td class="text-center"><a href="{{ $envelope -> file_location }}" class="btn btn-primary" target="_blank"><i class="fal fa-download mr-2"></i> Download</a></td>
                </tr>
            @endforeach

        </body>

    </table>

</div>
