<div class="row">

    <div class="col-12 col-sm-6 col-lg-4">

        <div class="d-flex justify-content-between align-items-center bg-blue-light p-2 rounded">

            <div class="text-primary font-10">
                <a href="/doc_management/document_review">Documents to Review</a>
            </div>
            <div class="bg-orange font-11 text-white rounded w-15 text-center">
                <a href="/doc_management/document_review" class="text-white p-2 d-block w-100 h-100">{{ $docs_to_review_count }}</a>
            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-lg-4">

        <div class="d-flex justify-content-between align-items-center bg-blue-light p-2 rounded">

            <div class="text-primary font-10">
                <a href="/doc_management/document_review">Releases to Review</a>
            </div>
            <div class="bg-orange font-11 text-white rounded w-15 text-center">
                <a href="/doc_management/document_review" class="text-white p-2 d-block w-100 h-100">{{ $releases_to_review_count }}</a>
            </div>

        </div>

    </div>

</div>


<div class="row mt-2">

    <div class="col-12 col-sm-6 col-lg-4">

        <div class="d-flex justify-content-between align-items-center bg-blue-light p-2 rounded">

            <div class="text-primary font-10">
                <a href="/doc_management/balance_earnest">Pending Earnest Checks</a>
            </div>
            <div class="bg-orange font-11 text-white rounded w-15 text-center">
                <a href="/doc_management/balance_earnest" class="text-white p-2 d-block w-100 h-100">{{ $pending_earnest_count }}</a>
            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-lg-4">

        <div class="d-flex justify-content-between align-items-center bg-blue-light p-2 rounded">

            <div class="text-primary font-10">
                <a href="/doc_management/active_earnest">Deposits To Release</a>
            </div>
            <div class="bg-orange font-11 text-white rounded w-15 text-center">
                <a href="/doc_management/active_earnest" class="text-white p-2 d-block w-100 h-100">{{ $deposits_to_release_count }}</a>
            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-lg-4">

        <div class="d-flex justify-content-between align-items-center bg-blue-light p-2 rounded">

            <div class="text-primary font-10">
                <a href="/doc_management/commission">Pending Commission Breakdowns</a>
            </div>
            <div class="bg-orange font-11 text-white rounded w-15 text-center">
                <a href="/doc_management/commission" class="text-white p-2 d-block w-100 h-100">{{ $pending_commissions_count }}</a>
            </div>

        </div>

    </div>

</div>
