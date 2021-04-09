
<div class="d-flex justify-content-start align-items-center mt-3 mb-2">
    <div class="h4 text-orange mt-2">Canceled </div>
    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Canceled" data-content="These are your canceled envelopes, canceled by either you or the signer."><i class="fad fa-question-circle ml-2"></i></a>
</div>

<div class="mb-5 no-wrap">

    <table id="canceled_table" class="table table-hover table-bordered" width="100%">

        <thead>
            <tr>
                <th class="wpx-100">Status</th>
                <th>Subject</th>
                <th>Recipients</th>
                <th>Documents</th>
                <th class="wpx-100">Created</th>
            </tr>
        </thead>

        <tbody>

            @foreach($envelopes as $envelope)

                @php
                $signers = $envelope -> signers;
                $recipients = [];
                foreach($signers as $signer) {
                    if($signer -> signer_status == 'document_declined') {
                        $recipients[] = '<span class="text-danger">'.$signer -> signer_name.' - <span class="small">Declined</span></span>';
                    } else if($signer -> signer_status == 'document_bounced') {
                        $recipients[] = '<span class="text-danger">'.$signer -> signer_name.' - <span class="small">Bounced</span></span>';
                    } else if($signer -> signer_status == 'document_expired') {
                        $recipients[] = '<span class="text-danger">'.$signer -> signer_name.' - <span class="small">Expired</span></span>';
                    } else {
                        $recipients[] = $signer -> signer_name;
                    }
                }
                $documents = $envelope -> documents;
                @endphp
                <tr>
                    <td>{{ $envelope -> status }}</td>
                    <td>{{ $envelope -> subject }}</td>
                    <td>{!! implode(', ', $recipients) !!}</td>
                    <td>
                        @foreach($documents as $document)
                            <a href="{{ $document -> file_location }}" target="_blank">{{ shorten_text($document -> file_name, 45) }}</a>
                            @if(!$loop -> last)<br> @endif
                        @endforeach
                    </td>
                    <td class="no-wrap small" data-sort="{{ $envelope -> created_at }}">{{ date('M jS, Y', strtotime($envelope -> created_at)) }}<br>{{ date('g:i:s A', strtotime($envelope -> created_at)) }}</td>
                </tr>
            @endforeach

        </body>

    </table>

</div>
