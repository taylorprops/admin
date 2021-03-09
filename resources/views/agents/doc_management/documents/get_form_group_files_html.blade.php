<table class="table table-bordered table-sm documents-table">

    <thead>
        <th width="120"></th>
        <th width="300">Form Group</th>
        <th>Form Name</th>
    </thead>

    <tbody>


        @foreach($form_groups as $form_group)

            @php $uploads = $form_group -> uploads @endphp

            @foreach ($uploads as $upload)

                <tr>
                    <td><a href="{{ $upload -> file_location }}" class="btn btn-sm btn-primary" target="_blank"><i class="fal fa-plus mr-2"></i> Open File</a></td>
                    <td>{{ $form_group -> resource_name }}</td>
                    <td>{{ $upload -> file_name_display }}</td>
                </tr>

            @endforeach

        @endforeach

    </tbody>

</table>