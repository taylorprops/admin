<div class="h4 text-orange my-3">Sent Envelopes</div>

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
