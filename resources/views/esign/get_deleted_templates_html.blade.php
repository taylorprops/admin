<div class="h4 text-orange my-3">Deleted Templates</div>

<div class="table-responsive text-nowrap">

    <table id="deleted_templates_table" class="table table-hover table-bordered" width="100%">

        <thead>
            <tr>
                <th class="wpx-100"></th>
                <th>Name</th>
                <th>Recipients</th>
                <th>Created</th>
            </tr>
        </thead>

        <tbody>

            @foreach($deleted_templates as $template)

                @php
                $signers = $template -> signers;
                $recipients = [];
                foreach($signers as $signer) {
                    $recipients[] = $signer -> signer_role;
                }
                @endphp
                <tr>
                    <td><a href="javascript:void(0)" class="btn btn-primary restore-template-button" data-envelope-id="{{ $template -> id }}">Restore template <i class="fal fa-undo ml-2"></i></a></td>
                    <td>{{ $template -> template_name }}</td>
                    <td>{!! implode(', ', $recipients) !!}</td>
                    <td>{{ date('M jS, Y', strtotime($template -> created_at)) }}</td>
                </tr>
            @endforeach

        </body>

    </table>

</div>
<input type="hidden" id="deleted_templates_count" value="{{ count($deleted_templates) }}">
