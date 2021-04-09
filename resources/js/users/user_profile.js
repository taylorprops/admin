
if (document.URL.match(/user_profile/)) {

    $(function () {


        $('#save_profile_button').on('click', save_profile);

        $(document).on('click', '.delete-image-button', delete_photo);

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


        $('.filepond--credits').hide();


    });


    function show_cropper(width, height, agent_photo_file_pond) {

        $('#crop_modal').modal('show');
        $('#crop_modal').on('hidden.bs.modal', function(){
            agent_photo_file_pond.removeFiles();
        });

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

            // Pass the image file name as the third parameter if necessary.
            formData.append('cropped_image', blob/*, 'example.png' */);

            axios.post('/users/save_cropped_upload', formData, axios_options)
            .then(function (response) {
                cropper.destroy();
                $('#crop_modal').modal('hide');
                $('#photo_location').attr('src', response.data.path);
                $('.has-photo').removeClass('hidden');
                $('.no-photo').addClass('hidden');
                agent_photo_file_pond.removeFiles();
                $('#save_crop_button').html('<i class="fad fa-save mr-2"></i> Save');

            })
            .catch(function (error) {
                console.log(error);
            });

        }, 'image/png');

    }

    function delete_photo() {


        let formData = new FormData();

        axios.post('/users/delete_photo', formData, axios_options)
        .then(function (response) {
            $('#photo_location').attr('src', '');
            $('.has-photo').addClass('hidden');
            $('.no-photo').removeClass('hidden');
        })
        .catch(function (error) {
            console.log(error);
        });
    }


    function save_profile() {

        let form = $('#edit_user_form');

        let validate = validate_form(form);

        if (validate == 'yes') {

            let formData = new FormData(form[0]);

            axios.post('/users/save_profile', formData, axios_options)
                .then(function (response) {

                    toastr['success']('Profile Details Successfully Saved');

                })
                .catch(function (error) {
                });

        }

    }


}
