

<div class="no-wrap">

    <table class="table table-hover table-bordered table-sm employees-table" width="100%">

        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Type</th>
                <th>Role</th>
                <th>Email</th>
                <th>Cell Phone</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td width="80">
                        <button class="btn btn-primary edit-employee-button"
                        data-type="{{ $emp_type }}"
                        @foreach($employee -> toArray() as $column => $value)
                            data-{{ $column }}="{{ $value }}"
                        @endforeach
                        >
                            <i class="fal fa-edit mr-2"></i> Edit
                        </button>
                    </td>
                    <td>{{ $employee -> last_name.', '.$employee -> first_name }}</td>
                    <td>{{ ucwords($employee -> emp_type) }}</td>
                    <td>{{ ucwords($employee -> emp_position) }}</td>
                    <td><a href="mailto:{{ $employee -> email }}">{{ $employee -> email }}</a></td>
                    <td>{{ $employee -> cell_phone }}</td>
                    <td><img src="{{ $employee -> photo_location }}" height="60"></td>
                </tr>
            @endforeach
        </body>

    </table>

</div>
