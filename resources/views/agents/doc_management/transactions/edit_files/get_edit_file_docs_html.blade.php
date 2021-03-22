<div class="container-1200 mx-auto animate__animated animate__fadeIn">

    <div class="row">

        @php $total_pages = count($images); @endphp

        <div class="col-12 col-lg-10 p-0 mx-auto">

            <div class="container-fluid p-0">
                <div class="file-viewer-container">

                    <div class="file-view animate__animated animate__fadeIn" id="file_viewer">

                        @foreach($images as $image)

                        <?php $c = $image -> page_number; ?>

                            <div class="h5 bg-primary p-2 text-center mb-0" id="page_{{ $c }}">
                                <span class="badge text-white font-10">Page <?php echo $c.' of '.$total_pages; ?></span>
                            </div>
                            <div class="file-view-page-container border border-primary w-100 @if($loop -> first) active @endif" data-id="{{ $c }}">
                                <link rel="preconnect" href="https://fonts.gstatic.com">
                                <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">
                                <div class="fields-container w-100 h-100">
                                    <img class="file-image-bg w-100 h-100" src="{{ $image -> file_location }}?r={{ date('YmdHis') }}">
                                    @foreach($user_fields as $user_field)
                                        @if($user_field -> page == $c)
                                            @include('/agents/doc_management/transactions/edit_files/field_html', [$user_field, $c, $Listing_ID, $Contract_ID, $Referral_ID, $transaction_type, $Agent_ID])
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="h5 text-white bg-primary p-2 text-center page-foot">
                                <span class="badge">End Page {{ $c }}</span>
                            </div>

                        @endforeach

                    </div> <!-- ende file_viewer -->
                </div> <!-- end file-viewer-container -->

            </div> <!-- end container-fluid p-0 -->

        </div>

        <div class="col-2 d-none d-lg-block p-0 edit-file-sidebar border-right">
            <div class="file-view animate__animated animate__fadeIn" id="thumb_viewer">
                <div class="h5 text-white bg-primary p-2"><i class="fad fa-send-backward mr-3"></i> Pages</div>
                @foreach($images as $image)
                    @php $c = $image -> page_number; @endphp
                    <div class="file-view-thumb-container mb-2 mx-auto @if($c == 1) active @endif" id="thumb_{{ $c }}" data-id="{{ $c }}">
                        <div class="file-view-thumb">
                            <a href="javascript: void(0)"><img class="file-thumb w-100 h-100" src="{{ $image -> file_location }}?r={{ date('YmdHis') }}"></a>
                        </div>
                        <div class="file-view-thumb-footer text-center mb-1">
                            Page {{ $c }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
