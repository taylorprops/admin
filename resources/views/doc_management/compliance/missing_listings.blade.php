<div class="no-wrap">

    <table id="listings_table" class="table table-hover table-sm" width="100%">

        <thead>
            <tr>
                <th>Agent</th>
                <th>Address</th>
                <th>List Date</th>
                <th></th>
            </tr>
        </thead>

        <tbody>

            @foreach($listings as $listing)

                <tr>
                    <td>{{ $listing -> agent -> full_name }}</td>
                    <td>{{ $listing -> FullStreetAddress.' '.$listing -> City.', '.$listing -> StateOrProvince.' '.$listing -> PostalCode }}</td>
                    <td>{{ $listing -> MlsListDate }}</td>
                    <td></td>
                </tr>

            @endforeach

        </body>

    </table>

</div>
