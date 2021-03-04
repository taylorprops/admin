$(function() {

    get_form_group_files(0);

    $('.form-group-select').off('change').on('change', select_form_group);

    function select_form_group() {

        let form_group_id = $('.form-group-select').val();

        get_form_group_files(form_group_id);

    }

    function get_form_group_files(form_group_id) {

        $('.documents-table tbody').html('');

        axios.get('/documents/get_form_group_files', {
            params: {
                form_group_id: form_group_id
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {

            $('#forms_table_div').html(response.data);

            let length = 50;
            if(form_group_id == 0) {
                length = 10;
            }
            let dt = data_table(length, $('.documents-table'), [0, 'asc'], [0], [], false, true, true, true, true);

        })
        .catch(function (error) {
            console.log(error);
        });
    }

});
