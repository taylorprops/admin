<table id="contacts_table" class="table table-hover table-bordered table-sm" width="100%">

    <thead>
        <tr>
            <th class="text-center pl-3"><input type="checkbox" class="custom-form-element form-checkbox check-all" data-label=""></th>
            <th>
                <div class="bulk-options text-left hidden">
                    <button class="btn btn-danger delete-button">Delete Checked</button>
                </div>
            </th>
            <th>Contact</th>
            <th>Company</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Address</th>
        </tr>
    </thead>

    <tbody>
        @foreach($contacts as $contact)

            @php
            // $members = $contact -> members;
            // $listings = [];
            // $contracts = [];
            // foreach($members as $member) {
            //     if($member -> Listing_ID > 0) {
            //         $listings[] = $member -> Listing_ID;
            //     }
            //     if($member -> Contract_ID > 0) {
            //         $contracts[] = $member -> Contract_ID;
            //     }
            // }
            // $listings = implode(',', $listings);
            // $contracts = implode(',', $contracts);
            @endphp

            <tr>
                <td class="wpx-30 text-center pl-3"><input type="checkbox" class="custom-form-element form-checkbox contact-checkbox" data-label="" data-contact-id="{{ $contact -> id }}"></td>
                <td class="wpx-80">
                    <button class="btn btn-primary btn-sm edit-contact-button"
                    data-contact-id="{{ $contact -> id }}"
                    data-contact-first="{{ $contact -> contact_first }}"
                    data-contact-last="{{ $contact -> contact_last }}"
                    data-contact-company="{{ $contact -> contact_company }}"
                    data-contact-street="{{ $contact -> contact_street }}"
                    data-contact-city="{{ $contact -> contact_city }}"
                    data-contact-state="{{ $contact -> contact_state }}"
                    data-contact-zip="{{ $contact -> contact_zip }}"
                    data-contact-phone-home="{{ $contact -> contact_phone_cell }}"
                    data-contact-phone-cell="{{ $contact -> contact_phone_home }}"
                    data-contact-email="{{ $contact -> contact_email }}">
                    <i class="fa fa-eye mr-2"></i> View
                </button>
                </td>
                <td>{{ $contact -> contact_last.', '.$contact -> contact_first }}</td>
                <td>{{ $contact -> contact_company }}</td>
                <td>
                    <div>C - <a href="tel:{{ $contact -> contact_phone_cell }}">{{ format_phone($contact -> contact_phone_cell) }}</a></div>
                    @if($contact -> contact_phone_home != '')
                        <div>H - <a href="tel:{{ $contact -> contact_phone_home }}">{{ format_phone($contact -> contact_phone_home) }}</a></div>
                    @endif
                </td>
                <td><a href="mailto:{{ $contact -> contact_email}}" target="_blank">{{ $contact -> contact_email }}</a></td>
                <td>{{ $contact -> contact_street.' '.$contact -> contact_cit.', '.$contact -> contact_state.' '.$contact -> contact_zip }}</td>
            </tr>

        @endforeach

    </body>

</table>
