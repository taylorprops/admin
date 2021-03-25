<div class="no-wrap">
    <table class="table table-hover table-bordered table-sm documents-table">

        <thead>
            <th>Form Name</th>
            <th width="300">Form Group</th>
            <th width="120"></th>
        </thead>

        <tbody>


            @foreach($form_groups as $form_group)

                @php $uploads = $form_group -> uploads @endphp

                @foreach ($uploads as $upload)

                    <tr>
                        <td><a href="{{ $upload -> file_location }}" target="_blank">{{ $upload -> file_name_display }}</a></td>
                        <td>{{ $form_group -> resource_name }}</td>
                        <td><a href="{{ $upload -> file_location }}" class="btn btn-sm btn-primary" target="_blank"><i class="fal fa-plus mr-2"></i> Open File</a></td>
                    </tr>

                @endforeach

            @endforeach

        </tbody>

    </table>
</div>
