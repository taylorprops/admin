
<div class="d-flex justify-content-start align-items-center mt-3 mb-2">
    <div class="h4 text-orange mt-2">Cancelled </div>
    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Cancelled" data-content="These are your cancelled envelopes, cancelled by either you or the signer."><i class="fad fa-question-circle ml-2"></i></a>
</div>

<div class="mb-5">

    <table id="cancelled_table" class="table table-bordered" width="100%">

        <thead>
            <tr>
                <th class="wpx-100">Status</th>
                <th>Subject</th>
                <th>Recipients</th>
                <th class="wpx-100">Created</th>
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
                @endphp
                <tr>
                    <td>{{ $envelope -> status }}</td>
                    <td>{{ $envelope -> subject }}</td>
                    <td>{!! implode(', ', $recipients) !!}</td>
                    <td data-sort="{{ $envelope -> created_at }}">{{ date('M jS, Y', strtotime($envelope -> created_at)) }}<br>{{ date('g:i:s A', strtotime($envelope -> created_at)) }}</td>
                </tr>
            @endforeach

        </body>

    </table>

</div>
