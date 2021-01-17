<div class="h4 text-orange my-3">Deleted Drafts</div>

<div class="table-responsive text-nowrap">

    <table id="deleted_drafts_table" class="table table-hover table-bordered" width="100%">

        <thead>
            <tr>
                <th class="wpx-100"></th>
                <th>Name</th>
                <th>Recipients</th>
                <th>Created</th>
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
                @endphp
                <tr>
                    <td><a href="javascript:void(0)" class="btn btn-primary restore-draft-button" data-envelope-id="{{ $draft -> id }}">Restore Draft <i class="fal fa-undo ml-2"></i></a></td>
                    <td>{{ $draft -> draft_name }}</td>
                    <td>{!! implode(', ', $recipients) !!}</td>
                    <td>{{ date('M jS, Y', strtotime($draft -> created_at)) }}</td>
                </tr>
            @endforeach

        </body>

    </table>

</div>
