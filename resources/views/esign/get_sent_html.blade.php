
<div class="d-flex justify-content-start align-items-center mt-3 mb-5">
    <div class="h4 text-orange mt-2">In Process </div>
    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="In Process" data-content="These are your active envelopes. They have been sent for signatures but have not been signed by all parties yet."><i class="fad fa-question-circle ml-2"></i></a>
</div>

<div class="table-responsive text-nowrap mb-5">

    <table id="sent_table" class="table table-hover table-bordered" width="100%">

        <thead>
            <tr>
                <th class="wpx-100"></th>
                <th>Name</th>
                <th>Recipients</th>
                <th>Created</th>
                <th></th>
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
                    <td></td>
                    <td>{{ $envelope -> subject }}</td>
                    <td>{!! implode(', ', $recipients) !!}</td>
                    <td>{{ date('M jS, Y', strtotime($envelope -> created_at)) }}</td>
                    <td class="text-center"></td>
                </tr>
            @endforeach

        </body>

    </table>

</div>
