
<div class="d-flex justify-content-start align-items-center mt-3 mb-2">
    <div class="h4 text-orange mt-2">In Process </div>
    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="In Process" data-content="These are your active envelopes. They have been sent for signatures but have not been signed by all parties yet."><i class="fad fa-question-circle ml-2"></i></a>
</div>

<div class="mb-5 table-responsive">

    <table id="in_process_table" class="table table-bordered" width="100%">

        <thead>
            <tr>
                <th class="wpx-100">Status</th>
                <th>Subject</th>
                <th>Recipients</th>
                <th>Documents</th>
                <th class="wpx-100">Sent</th>
                <th class="wpx-125"></th>
            </tr>
        </thead>

        <tbody>

            @foreach($envelopes as $envelope)

                @php
                $signers = $envelope -> signers;
                $callbacks = $envelope -> callbacks;
                $documents = $envelope -> documents;
                @endphp

                <tr>
                    <td>{{ $envelope -> status }}</td>
                    <td>{{ $envelope -> subject }}</td>
                    <td class="pr-4">
                        @php
                        foreach($signers as $signer) {

                            $callback_signer = $callbacks -> where('signer_id', $signer -> id) -> last();
                            $event_type = $callback_signer ? $callback_signer -> event_type : null;

                            $signer_status = [
                                'document_sent' => '
                                <div class="text-primary no-wrap">'.$signer -> signer_name.'</div>
                                <div class="no-wrap">
                                    <div class="d-flex justify-content-end align-items-center text-primary">
                                        Sent <i class="fal fa-arrow-right ml-3"></i><br>
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center text-primary">
                                        <a href="javascript:void(0)" class="text-orange resend-envelope-button" data-envelope-id="'.$envelope -> id.'" data-signer-id="'.$signer -> id.'">Resend <i class="fal fa-redo ml-2"></i></a>
                                    </div>
                                </div>',
                                'document_viewed' => '
                                <div class="text-primary no-wrap">'.$signer -> signer_name.'</div>
                                <div class="no-wrap">
                                    <div class="d-flex justify-content-end align-items-center text-primary">
                                        Viewed <i class="fad fa-hourglass-half ml-2"></i><br>
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center text-primary">
                                        <a href="javascript:void(0)" class="text-orange resend-envelope-button" data-envelope-id="'.$envelope -> id.'" data-signer-id="'.$signer -> id.'">Resend <i class="fal fa-redo ml-2"></i></a>
                                    </div>
                                </div>',
                                'document_signed' => '<div class="text-success no-wrap">'.$signer -> signer_name.'</div><div class="text-success"> Signed <i class="fal fa-check ml-2"></i></div>',
                                'document_declined' => '<div class="text-danger no-wrap">'.$signer -> signer_name.'</div><div class="text-danger"> Declined <i class="fal fa-ban ml-2"></i></div>',
                                'signer_bounced' => '<div class="text-danger no-wrap">'.$signer -> signer_name.'</div><div class="text-danger"> Bounced <i class="fal fa-ban ml-2"></i></div>',
                                'document_expired' => '<div class="text-danger no-wrap">'.$signer -> signer_name.'</div><div class="text-danger"> Expired <i class="fad fa-ban ml-2"></i></div>',
                                'document_cancelled' => '<div class="text-danger no-wrap">'.$signer -> signer_name.'</div><div class="text-danger"> Cancelled <i class="fal fa-ban ml-2"></i></div>'
                            ][$event_type] ?? '<div>'.$signer -> signer_name.'</div>';

                            echo '<div class="d-flex justify-content-between">'.$signer_status.'</div>';

                        }
                        @endphp
                    </td>
                    <td>
                        @foreach($documents as $document)
                            <a href="{{ $document -> file_location }}" target="_blank">{{ shorten_text($document -> file_name, 45) }}</a>
                            @if(!$loop -> last)<br> @endif
                        @endforeach
                    </td>
                    <td class="no-wrap small" data-sort="{{ $envelope -> created_at }}">
                        {{ date('M jS, Y', strtotime($envelope -> created_at)) }}<br>{{ date('g:i:s A', strtotime($envelope -> created_at)) }}
                    </td>
                    <td class="text-center">
                        <button class="btn btn-danger cancel-envelope-button" data-envelope-id="{{ $envelope -> id }}"><i class="fal fa-times-circle mr-2"></i> Cancel</button>
                    </td>
                </tr>

            @endforeach

        </body>

    </table>

</div>
