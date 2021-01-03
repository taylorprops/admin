@foreach($earnest_account_totals as $earnest_account_total)

    @php
    $company_words = explode(' ', $earnest_account_total['company']);
    $company_initials = '';

    foreach ($company_words as $w) {
        $company_initials .= $w[0];
    }
    @endphp

    <a class="list-group-item list-group-item-action earnest-totals-tab @if($loop -> first) active @endif"
    id="account_tab_{{ $earnest_account_total['id'] }}"
    href="#account_tab_content_{{ $earnest_account_total['id'] }}"
    data-toggle="tab"
    role="tab"
    aria-controls="account_tab_content_{{ $earnest_account_total['id'] }}"
    aria-selected="true">

        <div class="d-flex justify-content-between align-items-center">
            <div class="w-50 d-flex justify-content-between">
                <div>{{ $earnest_account_total['state'] }}</div>
                <div>{{ $earnest_account_total['account_number'] }}</div>
                <div>{{ $company_initials }}</div>
            </div>
            <div>${{ number_format($earnest_account_total['total'], 2) }}</div>
        </div>

    </a>

@endforeach
