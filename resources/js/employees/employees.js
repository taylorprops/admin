if(document.URL.match(/employees/)) {

    $(function() {

        get_employees('in_house', 'yes');
        get_employees('transaction_coordinators', 'yes');

        $(document).on('click', '#add_employee_button', function() {
            edit_employee(null);
        });
        $(document).on('click', '.edit-employee-button', function() {
            edit_employee($(this));
        });

        $('#show_active').on('change', function() {
            get_employees($('#employee_tabs .nav-link.active').data('type'), $(this).val());
        });

    });

    function get_employees(type, active) {

        axios.get('/employees/get_employees', {
            params: {
                type: type,
                active: active
            }
        })
        .then(function (response) {

            $('#'+type+'_div').html(response.data);
            data_table(25, $('.employees-table'), [1, 'asc'], [0,6], [], true, true, true, true, true);



        })
        .catch(function (error) {

        });

    }

    function edit_employee(ele) {

        $('#edit_employee_modal').find('input, select').val('');
        $('#edit_employee_modal').modal('show');

        if(ele) {

            $('#edit_employee_modal_title').text('Edit Employee');
            $.each(ele.data(), function(index, value) {
                $('#'+index).val(value);
            });
            $('.edit-col').show();

        } else {

            $('#edit_employee_modal_title').text('Add Employee');
            $('.edit-col').hide();

        }

        $('#save_edit_employee_button').off('click').on('click', save_employee);

    }

    function save_employee() {

        let form = $('#edit_employee_form');

        let validate = validate_form(form);

        if(validate == 'yes') {

            let formData = new FormData(form[0]);
            axios.post('/employees/save_employee', formData, axios_options)
            .then(function (response) {
                $('#edit_employee_modal').modal('hide');
                toastr['success']('Employee Successfully Saved')
            })
            .catch(function (error) {
                console.log(error);
            });

        }

    }

}
