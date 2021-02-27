<div class="h4 text-orange my-3">Deleted Drafts</div>

<div class="">

    <table id="deleted_drafts_table" class="table table-bordered" width="100%">

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

            @foreach($deleted_drafts as $draft)

                @php
                $signers = $draft -> signers;
                $recipients = [];
                foreach($signers as $signer) {
                    $recipients[] = $signer -> signer_name;
                }
                $documents = $envelope -> documents;
                @endphp
                <tr>
                    <td><a href="javascript:void(0)" class="btn btn-primary restore-draft-button" data-envelope-id="{{ $draft -> id }}">Restore Draft <i class="fal fa-undo ml-2"></i></a></td>
                    <td>{{ $draft -> draft_name }}</td>
                    <td>{!! implode(', ', $recipients) !!}</td>
                    <td>
                        @foreach($documents as $document)
                            <a href="{{ $document -> file_location }}" target="_blank">{{ shorten_text($document -> file_name, 45) }}</a>
                            @if(!$loop -> last)<br> @endif
                        @endforeach
                    </td>
                    <td class="no-wrap small" data-sort="{{ $draft -> created_at }}">{{ date('M jS, Y', strtotime($draft -> created_at)) }}<br>{{ date('g:i:s A', strtotime($draft -> created_at)) }}</td>
                </tr>
            @endforeach

        </body>

    </table>

</div>
<input type="hidden" id="deleted_drafts_count" value="{{ count($deleted_drafts) }}">
