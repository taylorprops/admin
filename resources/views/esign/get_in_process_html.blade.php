
<div class="d-flex justify-content-start align-items-center mt-3 mb-5">
    <div class="h4 text-orange mt-2">In Process </div>
    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="In Process" data-content="These are your active envelopes. They have been sent for signatures but have not been signed by all parties yet."><i class="fad fa-question-circle ml-2"></i></a>
</div>

<div class="mb-5">

    <table id="in_process_table" class="table table-hover table-bordered" width="100%">

        <thead>
            <tr>
                <th class="wpx-100">Status</th>
                <th>Subject</th>
                <th>Recipients</th>
                <th class="wpx-100">Sent</th>
                <th></th>
            </tr>
        </thead>

        <tbody>

            @foreach($envelopes as $envelope)

                @php
                $signers = $envelope -> signers;
                $callbacks = $envelope -> callbacks;
                @endphp

                <tr>
                    <td>{{ $envelope -> status }}</td>
                    <td>{{ $envelope -> subject }}</td>
                    <td>
                        @php
                        foreach($signers as $signer) {

                            $callback_signer = $callbacks -> where('signer_id', $signer -> id) -> last();
                            $event_type = $callback_signer ? $callback_signer -> event_type : null;
                            /* echo '<pre>';
                            dump($callback_signer);
                            echo '</pre>'; */
                            $signer_status = [
                                'document_sent' => $signer -> signer_name.' - Sent',
                                'document_signed' => $signer -> signer_name.' - Signed',
                                'document_declined' => $signer -> signer_name.' - Declined',
                                'signer_bounced' => $signer -> signer_name.' - Bounced',
                                'document_expired' => $signer -> signer_name.' - Expired',
                                'document_cancelled' => $signer -> signer_name.' - Cancelled'
                            ][$event_type] ?? $signer -> signer_name;

                            echo '<div class="w-100">'.$signer_status.'</div>';

                        }
                        @endphp
                    </td>
                    <td data-sort="{{ $envelope -> created_at }}">{{ date('M jS, Y', strtotime($envelope -> created_at)) }}<br>{{ date('g:i:s A', strtotime($envelope -> created_at)) }}</td>
                    <td class="text-center"></td>
                </tr>

            @endforeach

        </body>

    </table>

</div>
