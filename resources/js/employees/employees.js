
if (document.URL.match(/employees/)) {

    $(function () {

        get_employees('in_house', 'yes');
        get_employees('transaction_coordinator', 'yes');

        $(document).on('click', '#add_employee_button', function () {
            edit_employee(null);
        });
        $(document).on('click', '.edit-employee-button', function () {
            edit_employee($(this));
        });

        $(document).on('click', '.delete-image-button', delete_photo);

        $('#show_active').on('change', function () {
            get_employees($('.employee-nav-link.active').data('type'), $(this).val());
        });


        let agent_photo_file = document.getElementById('agent_photo_file');
        let agent_photo_file_pond = FilePond.create(agent_photo_file);

        agent_photo_file_pond.setOptions({
            allowImagePreview: false,
            server: {
                process: {
                    url: '/filepond_upload'
                }
            },
            labelIdle: 'Drag & Drop here or<br><span class="filepond--label-action"> Browse </span>',
            onpreparefile: (file, output) => {
                let img = new Image();
                img.src = URL.createObjectURL(output);
                img.id = 'crop_image';
                let width = img.naturalWidth;
                let height = img.naturalHeight;
                $('#crop_modal').find('.crop-container').html(img);
                show_cropper(width, height, agent_photo_file_pond);
            },
            onprocessfile: (file, output) => {
                //console.log(file, output);
            }
        });


        let agent_docs_file = document.getElementById('agent_docs_file');
        let agent_docs_file_pond = FilePond.create(agent_docs_file);

        agent_docs_file_pond.setOptions({
            allowImagePreview: false,
            multiple: true,
            server: {
                process: {
                    url: '/employees/docs_upload',
                    onerror: (response) => response.data,
                    ondata: (formData) => {
                        formData.append('emp_id', $('#id').val());
                        formData.append('emp_type', $('#emp_type option:selected').data('type'));
                        return formData;
                    }
                }
            },
            labelIdle: 'Drag & Drop here or <span class="filepond--label-action"> Browse </span>',
            onprocessfiles: () => {
                agent_docs_file_pond.removeFiles();
            }
        });

        $('.filepond--credits').hide();


    });


    function show_cropper(width, height, agent_photo_file_pond) {

        $('#crop_modal').modal('show');

        let image = document.querySelector('#crop_image');
        let cropper = new Cropper(image, {
            aspectRatio: 3 / 4,
            minContainerHeight: height,
            minContainerWidth: width,
            minCanvasHeight: height,
            minCanvasWidth: width,

        });

        $('#save_crop_button').off('click').on('click', function() {
            $(this).html('<span class="spinner-border spinner-border-sm mr-2"></span> Saving Image...');
            save_cropped_image(cropper, agent_photo_file_pond);
        });

        $('#crop_modal').on('hidden.bs.modal', function() {
            agent_photo_file_pond.removeFiles();
        });

    }

    function save_cropped_image(cropper, agent_photo_file_pond) {

        cropper.getCroppedCanvas({
            width: 300,
            height: 400,
            fillColor: '#fff',
            imageSmoothingEnabled: false,
            imageSmoothingQuality: 'high',
        });

        // Upload cropped image to server if the browser supports `HTMLCanvasElement.toBlob`.
        // The default value for the second parameter of `toBlob` is 'image/png', change it if necessary.
        cropper.getCroppedCanvas().toBlob((blob) => {

            let formData = new FormData();
            let emp_id = $('#id').val();
            let emp_type = $('#emp_type').find('option:selected').data('type');

            // Pass the image file name as the third parameter if necessary.
            formData.append('cropped_image', blob/*, 'example.png' */);
            formData.append('emp_id', emp_id);
            formData.append('emp_type', emp_type);

            axios.post('/employees/save_cropped_upload', formData, axios_options)
            .then(function (response) {
                cropper.destroy();
                $('#crop_modal').modal('hide');
                $('#photo_location').attr('src', response.data.path);
                $('.has-photo').removeClass('hidden');
                $('.no-photo').addClass('hidden');
                agent_photo_file_pond.removeFiles();
                $('#save_crop_button').html('<i class="fad fa-save mr-2"></i> Save');
                get_employees(emp_type, 'yes');
            })
            .catch(function (error) {
                console.log(error);
            });

        }, 'image/png');

    }

    function delete_photo() {

        let emp_id = $('#id').val();
        let emp_type = $('#emp_type').find('option:selected').data('type');

        let formData = new FormData();
        formData.append('emp_id', emp_id);
        formData.append('emp_type', emp_type);

        axios.post('/employees/delete_photo', formData, axios_options)
        .then(function (response) {
            $('#photo_location').attr('src', '');
            $('.has-photo').addClass('hidden');
            $('.no-photo').removeClass('hidden');
            get_employees(emp_type, 'yes');
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function get_employees(emp_type, active) {

        axios.get('/employees/get_employees', {
            params: {
                emp_type: emp_type,
                active: active
            }
        })
            .then(function (response) {

                $('#' + emp_type + '_div').html(response.data);
                data_table(25, $('.employees-table'), [1, 'asc'], [0, 6], [], true, true, true, true, true);

            })
            .catch(function (error) {

            });

    }

    function edit_employee(ele) {

        $('#edit_employee_modal').find('input, select').val('');
        $('#edit_employee_modal').modal('show');
        $('.agent-docs-div').html('');
        $('.edit-div').addClass('hidden');
        $('.add-div').addClass('hidden');


        if (ele) {

            $('.edit-div').removeClass('hidden');

            $('#edit_employee_modal_title').html('Edit Employee - <span class="text-gray">' + ele.data('first_name') + ' ' + ele.data('last_name') + '</span>');
            $.each(ele.data(), function (index, value) {
                if(index != 'photo_location') {
                    $('#' + index).val(value);
                }
            });
            if(ele.data('photo_location') != '') {
                $('#photo_location').attr('src', ele.data('photo_location'));
                $('.has-photo').removeClass('hidden');
                $('.no-photo').addClass('hidden');
            } else {
                $('.has-photo').addClass('hidden');
                $('.no-photo').removeClass('hidden');
            }
            $('#email_orig').val(ele.data('email'));
            $('.edit-col').show();

            get_docs();

        } else {

            $('.add-div').removeClass('hidden');

            $('#edit_employee_modal_title').text('Add Employee');
            $('.edit-col').hide();

        }

        $('#save_edit_employee_button').off('click').on('click', save_employee);

    }

    function save_employee() {

        let form = $('#edit_employee_form');

        let validate = validate_form(form);

        if (validate == 'yes') {

            let emp_type = $('#emp_type').find('option:selected').data('type');

            let formData = new FormData(form[0]);
            axios.post('/employees/save_employee', formData, axios_options)
                .then(function (response) {
                    $('#edit_employee_modal').modal('hide');
                    toastr['success']('Employee Successfully Saved');
                    get_employees(emp_type, 'yes');

                    $('#employee_saved_modal').modal('show');
                    $('#employee_saved_button').on('click', function () {

                        let emp_id = response.data.emp_id;
                        $('button[data-id="'+emp_id+'"]').trigger('click');
                        $('#employee_saved_modal').modal('hide');

                    });
                })
                .catch(function (error) {
                });

        }

    }

    function get_docs() {

        let emp_id = $('#id').val();
        let emp_type = $('#emp_type').find('option:selected').data('type');

        axios.get('/employees/get_docs', {
            params: {
                emp_id: emp_id,
                emp_type: emp_type
            }
        })
        .then(function (response) {
            $('.agent-docs-div').html(response.data);
            $('.delete-doc-button').off('click').on('click', function() {
                delete_doc($(this).data('doc-id'), 'no');
            });
            $('.restore-doc-button').off('click').on('click', function() {
                delete_doc($(this).data('doc-id'), 'yes');
            });
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function delete_doc(doc_id, active) {

        let formData = new FormData();
        let emp_type = $('#emp_type').find('option:selected').data('type');
        formData.append('doc_id', doc_id);
        formData.append('emp_type', emp_type);
        formData.append('active', active);
        axios.post('/employees/delete_doc', formData, axios_options)
        .then(function (response) {
            get_docs();
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function restore_doc(doc_id) {

    }

}
