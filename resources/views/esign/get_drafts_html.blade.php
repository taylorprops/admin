
<div class="d-flex justify-content-start align-items-center mt-3 mb-5">
    <div class="h4 text-orange mt-2">Drafts </div>
    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Drafts" data-content="Envelope Drafts are like email drafts. You can completely prepare a document for signing and save it for a later date. Templates can be used multiple times as well.<br><br>You will be given the option to save your evelope as a draft when creating an envelope."><i class="fad fa-question-circle ml-2"></i></a>
</div>


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

<hr class="show-deleted-drafts hidden">

<button class="btn btn-primary ml-0 mb-3 show-deleted-drafts hidden" type="button" data-toggle="collapse" data-target="#deleted_drafts_div" aria-expanded="false" aria-controls="deleted_drafts_div">
    View Deleted Drafts
</button>


