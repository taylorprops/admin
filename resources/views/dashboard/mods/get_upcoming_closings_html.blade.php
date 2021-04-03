<div class="no-wrap">

    <table id="upcoming_closings_table" class="table table-hover table-bordered table-sm" width="100%">

        <thead>
            <tr>
                <th>Address</th>
                <th>Settle Date</th>
                <th>Checklist Status</th>
                @if(auth() -> user() -> group == 'admin')
                <th>Agent</th>
                @endif
                <th></th>
            </tr>
        </thead>

        <tbody>

            @foreach($contracts as $contract)

                @php
                if($contract -> DocsMissingCount > 0) {
                    $checklist_status = '<span class="text-danger font-8"><i class="fal fa-exclamation-circle mr-2"></i> Missing Items</span>';
                } else {
                    $checklist_status = '<span class="text-success font-8"><i class="fal fa-check mr-2"></i> Complete</span>';
                }
                @endphp
                <tr>
                    <td>
                        <a href="/agents/doc_management/transactions/transaction_details/{{ $contract -> id }}/contract" class="d-block h-100 line-height-px-40">{{ $contract -> FullStreetAddress.' '.$contract -> City.', '.$contract -> StateOrProvince.' '.$contract -> PostalCode }}</a>
                    </td>
                    <td>
                        <div title="Settle Date" data-toggle="tooltip">
                            SD - {{ date_mdy($contract -> CloseDate) }}
                        </div>
                    </td>
                    <td>
                        {!! $checklist_status !!}
                    </td>
                    @if(auth() -> user() -> group == 'admin')
                    <td>
                        {{ $contract -> agent -> full_name }}
                    </td>
                    @endif
                    <td>
                        <div class="w-100 d-flex justify-content-around">
                            @if($contract -> ListPictureURL)
                                <img src="{{ $contract -> ListPictureURL }}" class="img-responsive upcoming-closing-image">
                            @else
                                <i class="fad fa-home fa-3x text-primary"></i>
                            @endif
                        </div>
                    </td>
                </tr>

            @endforeach

        </body>

    </table>

</div>
