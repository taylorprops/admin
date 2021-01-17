<div class="h4 text-orange my-3">Drafts</div>

<div class="table-responsive text-nowrap mb-5">

    <table id="drafts_table" class="table table-hover table-bordered" width="100%">

        <thead>
            <tr>
                <th class="wpx-100"></th>
                <th>Name</th>
                <th>Recipients</th>
                <th>Created</th>
                <th class="wpx-50"></th>
            </tr>
        </thead>

        <tbody>

            @foreach($drafts as $draft)

                @php
                $signers = $draft -> signers;
                $recipients = [];
                foreach($signers as $signer) {
                    $recipients[] = $signer -> signer_name;
                }
                @endphp
                <tr>
                    <td><a href="/esign/esign_add_fields/{{ $draft -> id }}" class="btn btn-primary" target="_blank">Open <i class="fal fa-arrow-right ml-2"></i></a></td>
                    <td>{{ $draft -> draft_name }}</td>
                    <td>{!! implode(', ', $recipients) !!}</td>
                    <td>{{ date('M jS, Y', strtotime($draft -> created_at)) }}</td>
                    <td class="text-center"><a href="javascript:void(0)" class="btn btn-danger delete-draft-button" data-envelope-id="{{ $draft -> id }}"><i class="fal fa-times"></i></a></td>
                </tr>
            @endforeach

        </body>

    </table>

</div>

<hr>

<button class="btn btn-primary ml-0 mb-3" type="button" data-toggle="collapse" data-target="#deleted_drafts_div" aria-expanded="false" aria-controls="deleted_drafts_div">
    View Deleted Drafts
</button>


