window.upload_documents = function() {

    $('#file_upload').dmUploader({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        auto: false,
        url: '/agents/doc_management/transactions/upload_documents',
        extFilter: ["jpg", "jpeg", "png", "gif", "pdf"],
        maxFileSize: 100000000, // 100 Megs
        extraData: function() {
            return {
                'Listing_ID': $('#Listing_ID').val(),
                'Contract_ID': $('#Contract_ID').val(),
                'Referral_ID': $('#Referral_ID').val(),
                'transaction_type': $('#transaction_type').val(),
                'Agent_ID': $('#Agent_ID').val(),
                'folder': $('#documents_folder').val()
            };
        },
        onDragEnter: function () {
            // Happens when dragging something over the DnD area
            this.addClass('active');
        },
        onDragLeave: function () {
            // Happens when dragging something OUT of the DnD area
            this.removeClass('active');
        },
        onInit: function () {
            // Plugin is ready to use
            //console.log('upload initialized :)', 'info');
        },
        onComplete: function () {
            // All files in the queue are processed (success or error)
            $('#upload_documents_modal').modal('hide');
            $('.modal-stack').remove();
            load_tabs('documents');
            //load_tabs('checklist');
            $('#save_upload_documents_button').html('<i class="fad fa-check mr-2"></i> Upload Documents');
        },
        onNewFile: function (id, file) {
            // When a new file is added using the file selector or the DnD area
            //console.log('New file added #' + id);
            ui_multi_add_file(id, file);
        },
        onBeforeUpload: function (id) {
            // about tho start uploading a file
            //console.log('Starting the upload of #' + id);
            ui_multi_update_file_status(id, 'uploading', 'Uploading...');
            ui_multi_update_file_progress(id, 0, '', true);
        },
        onUploadCanceled: function (id) {
            // Happens when a file is directly canceled by the user.
            ui_multi_update_file_status(id, 'warning', 'Canceled by User');
            ui_multi_update_file_progress(id, 0, 'warning', false);
        },
        onUploadProgress: function (id, percent) {
            // Updating file progress
            ui_multi_update_file_progress(id, percent);
        },
        onUploadSuccess: function (id, data) {
            // A file was successfully uploaded
            /* console.log('Server Response for file #' + id + ': ' + JSON.stringify(data));
            console.log('Upload of file #' + id + ' COMPLETED', 'success'); */
            ui_multi_update_file_status(id, 'success', 'Upload Complete');
            ui_multi_update_file_progress(id, 100, 'success', false);
        },
        onUploadError: function (id, xhr, status, message) {
            ui_multi_update_file_status(id, 'danger', message);
            ui_multi_update_file_progress(id, 0, 'danger', false);
        },
        onFallbackMode: function () {
            // When the browser doesn't support this plugin :(
            console.log('Plugin cant be used here, running Fallback callback', 'danger');
        },
        onFileSizeError: function (file) {
            $('#modal_danger').modal().find('.modal-body').html('File \'' + file.name + '\' cannot be added: There is a 100MB limit');
        },
        onFileExtError: function (file) {
            $('#modal_danger').modal().find('.modal-body').html('Wrong File Type - Only PDFs and Images can be uploaded');
        }
    });
}

function ui_multi_add_file(id, file) {

    var template = ' \
    <li class="media"> \
        <div class="media-body mb-1"> \
            <p class="mb-2 small"> \
                '+file.name+' - Status: <span class="text-muted">Waiting</span> <a href="javascript: void(0)" data-id="'+id+'" class="cancel-upload float-right text-danger mr-2"><i class="fa fa-times"></i></a> \
            </p> \
            <div class="progress mb-1 small"> \
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> \
                </div> \
            </div> \
            <hr class="my-1" /> \
        </div> \
    </li> \
    ';

    template = $(template);
    template.prop('id', 'uploaderFile' + id);
    template.data('file-id', id);

    $('#files_queue').find('li.empty').fadeOut(); // remove the 'no files yet'
    $('#files_queue').prepend(template);
}

// Changes the status messages on our list
function ui_multi_update_file_status(id, status, message) {
    $('#uploaderFile' + id).find('span').html(message).prop('class', 'status text-' + status);
}

// Updates a file progress, depending on the parameters it may animate it or change the color.
function ui_multi_update_file_progress(id, percent, color, active) {
    color = (typeof color === 'undefined' ? false : color);
    active = (typeof active === 'undefined' ? true : active);

    var bar = $('#uploaderFile' + id).find('div.progress-bar');

    bar.width(percent + '%').attr('aria-valuenow', percent);
    bar.toggleClass('progress-bar-striped progress-bar-animated', active);

    if (percent === 0) {
        bar.html('');
    } else {
        // bar.html(percent + '%');
        bar.html('Uploading...');
    }

    if (color !== false) {
        bar.removeClass('bg-success bg-info bg-warning bg-danger');
        bar.addClass('bg-' + color);
    }
}
