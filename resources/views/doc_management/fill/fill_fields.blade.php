@extends('layouts.main')
@section('title', 'Fill Fields')
@section('content')

<div class="container-fluid page-container page-fill-fields file-view-container p-0">

        <div class="container-fluid">
            <div class="row">
                <div class="col-12 field-toolbar">
                    <a href="javascript: void(0)" id="save_fill_fields" class="btn btn-primary">Save Fields</a>
                    <a href="javascript: void(0)" id="to_pdf" class="btn btn-primary ml-5">To Pdf</a>
                </div>
            </div>
        </div>

    <div class="container-fluid">
        <div class="row">

            <?php $total_pages = count($images); ?>

            <div class="col-2 px-0">
                <div class="field-list-container file-view"></div>
            </div>

            <div class="col-8 p-0">

                <div class="container-fluid p-0">
                    <div class="file-viewer-container mx-auto">
                        <div class="file-view" id="file_viewer">


                            @foreach($images as $image)

                            <?php $c = $image['page_number']; ?>

                                <div class="file-view-page-info bg-primary text-white p-2" id="page_{{ $c }}">
                                    Page <?php echo $c.' of '.$total_pages; ?>
                                </div>
                                <div class="file-view-page-container border border-primary w-100" data-id="{{ $c }}">
                                    <div class="field-container w-100 h-100">
                                        <img class="file-image-bg w-100 h-100" src="{{ $image['file_location'] }}">

                                        @foreach($fields as $field)
                                            @if($field['page'] == $c)
                                                @include('doc_management.fill.field', [$field, $c, $field_inputs, $field_values])
                                            @endif
                                        @endforeach

                                    </div> <!-- end field-container -->
                                </div> <!-- end file-view-page-container -->
                                <div class="file-view-page-info bg-primary text-white p-2 mb-4">
                                    Page {{ $c.' of '.$total_pages }}
                                </div>

                            @endforeach

                        </div> <!-- ende file_viewer -->
                    </div> <!-- end file-viewer-container -->

                </div> <!-- end container-fluid p-0 -->
            </div> <!-- col-8 -->
            <div class="col-2 pl-0">
                <div class="file-view" id="thumb_viewer">
                    <div class="h3 text-white bg-primary-dark p-2"><i class="fad fa-send-backward mr-3"></i> Pages</div>
                    @foreach($images as $image)
                        <?php $c = $image['page_number']; ?>
                        <div class="file-view-thumb-container  w-50 mx-auto <?php echo($c == 1) ? 'active' : ''; ?>" id="thumb_{{ $c }}" data-id="{{ $c }}">
                            <div class="file-view-thumb">
                                <a href="javascript: void(0)"><img class="file-thumb w-100 h-100" src="{{ $image['file_location'] }}"></a>
                            </div>
                            <div class="file-view-thumb-footer text-center mb-4">
                                Page {{ $c }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div><!-- ./ .container -->
<input type="hidden" id="file_id" value="{{ $file[0]['file_id'] }}">
<input type="hidden" id="file_name" value="{{ $file[0]['file_name'] }}">
@endsection


