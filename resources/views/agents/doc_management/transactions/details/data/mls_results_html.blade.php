<div class="row">
    <div class="col-12">
        <div class="h4 text-success"><i class="fal fa-check mr-2"></i> Property Located</div>
        <div class="h5 text-gray">
            The following data can be imported. Deselect any fields you do not want to replace.
            <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Import Data" data-content="Any selected fields will be automatically imported unless your deselect them.<br><br>Some required fields such as address fields will be overwritten.">
                <i class="fad fa-question-circle ml-2"></i>
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <div class="h5">Existing Data</div>
    </div>
    <div class="col-6">
        <div class="h5 w-100 text-right">New Data</div>
    </div>
</div>
@foreach($mls_search as $col => $val)
    @php

    @endphp
    <div class="row">
        <div class="col-2">
            {{ $col }}
        </div>
        <div class="col-4">
            {{ $listing_details -> $col }}
        </div>
        <div class="col-5">
            {{ $val }}
        </div>
        <div class="col-1">
            <input type="checkbox" class="custom-form-element form-checkbox" data-key="">
        </div>
    </div>
@endforeach


