if (document.URL.match(/transaction_details/)) {


    window.tasks_init = function() {

        $('.add-task-button, .edit-task-button').off('click').on('click', function() {
            add_edit_task($(this));
        });

        $('#delete_task_button').on('click', delete_task);

        $(document).on('mouseup', function (e) {
            let task_div = $('.task-container, .add-task-button');
            if (!task_div.is(e.target) && task_div.has(e.target).length === 0) {
                $('.task-container').collapse('hide');
            }
        });

        adjust_date();
        $('.date-change-trigger').on('keyup input change', adjust_date);

        filter_tasks('active');

        $('.filter-tasks').on('click', function() {
            $('.filter-tasks').removeClass('active');
            filter_tasks($(this).data('show'));
        }).addClass('active');

        setTimeout(function() {
            $('.filter-tasks[data-show="active"]').focus().trigger('click');
        }, 500);

        $('.task-container').on('hidden.bs.collapse', function () {
            clear_form();
        });

        $('.mark-completed-button').on('click', function() {
            mark_task_completed($(this).data('task-id'), $(this).data('status'));
        });

        $('.task.pending').appendTo('.tasks-list-group');


    }

    function mark_task_completed(task_id, status) {

        let formData = new FormData();
        formData.append('task_id', task_id);
        formData.append('status', status);

        axios.post('/agents/doc_management/transactions/mark_task_completed', formData, axios_options)
        .then(function (response) {
            toastr['success']('Status Successfully Changed');
            load_tabs('tasks');
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function add_edit_task(ele) {

        $('html, body').scrollTop(300);

        $('.task-container').collapse('show');

        $('.close-collapse').on('click', function() {
            $('.collapse').collapse('hide');
        });

        $('.delete-div').removeClass('hidden');
        if(ele.data('action') == 'add') {
            $('#task_members').closest('.form-ele').find('.form-select-label').trigger('click');
            $('.delete-div').addClass('hidden');
        }


        $('.task-ele, .reminder-ele').hide();

        let type = ele.data('type');

        $('#reminder').val(type == 'task' ? 0 : 1);

        $('#task_action_task').closest('.form-ele').find('.form-select-li').show();

        let action = 'Add';

        if(ele.data('action') == 'edit') {

            action = 'Edit';
            $('#task_id').val(ele.data('task-id'));
            $('#reminder').val(ele.data('reminder'));
            $('#task_title').val(ele.data('task-title'));
            $('#task_option_days').val(ele.data('task-option-days'));
            $('#task_option_position').val(ele.data('task-option-position'));
            $('#task_action').val(ele.data('task-action'));
            $('#task_action_task').val(ele.data('task-action-task'));
            $('#task_date').val(ele.data('task-date'));
            $('#task_time').val(ele.data('task-time'));

            $('#task_action_task').closest('.form-ele').find('.form-select-li[data-value="'+ele.data('task-id')+'"]').hide();

            let task_members = '';
            if(ele.data('task-members').toString().match(/,/)) {
                task_members = ele.data('task-members').split(',');
            } else {
                task_members = [ele.data('task-members').toString()];
            }

            $('#task_members').val(task_members);

            adjust_date();

        }

        if(type == 'task') {

            $('.task-ele').show();

            $('.task-header').html('<i class="fal fa-tasks mr-2"></i> '+action+' Task');
            $('.task-type').text('Task');
            $('.task-date').next('.form-input-label').text('Task Date');

        } else if(type == 'reminder') {

            $('.reminder-ele').show();

            $('.task-header').html('<i class="fal fa-clock mr-2"></i>'+action+' Reminder');
            $('.task-type').text('Reminder');
            $('.task-date').next('.form-input-label').text('Reminder Date');

        }

        $('#task_option_days').on('blur', function() {
            if($(this).val() == '') {
                $(this).val('0');
            }
        });

        $('#task_time').val('09:00:00');

        $('#save_task_button').off('click').on('click', save_task);

    }

    function save_task() {

        $('#task_members, #task_title').addClass('required');

        if($('#task_action').val() == '') {

            // no trigger event, only date required
            $('#task_date').addClass('required');

        } else {

            // has trigger event
            let task_action = $('#task_action option:selected');
            let task_action_task = $('#task_action_task option:selected');

            // see if date required. not required when trigger event has no date yet
            let task_date_required = null;
            // if has db col get date
            if(task_action.data('has-db-column') == 'yes') {

                if(task_action.data('date') != '') {
                    task_date_required = 'yes';
                }

            } else {
                // get date from task action task
                if(task_action_task.data('date') != '' && task_action.prop('value') != '223') {
                    task_date_required = 'yes';
                }

            }

            if(task_date_required) {
                $('#task_date').addClass('required');
            }

        }

        let form = $('#task_form');
        let validate = validate_form(form);

        if(validate == 'yes') {

            $('#task_date').prop('disabled', false);

            let formData = new FormData(form[0]);
            formData.append('task_id', $('#task_id').val());
            formData.append('reminder', $('#reminder').val());
            formData.append('Listing_ID', $('#Listing_ID').val());
            formData.append('Contract_ID', $('#Contract_ID').val());
            formData.append('transaction_type', $('#transaction_type').val());

            axios.post('/agents/doc_management/transactions/save_task', formData, axios_options)
            .then(function (response) {
                let text = $('#reminder').val() == '0' ? 'Task' : 'Reminder';
                toastr['success'](text+' Successfully Added');
                load_tabs('tasks');

            })
            .catch(function (error) {
                console.log(error);
            });

        }

    }

    function delete_task() {

        let task_id = $('#task_id').val();
        let formData = new FormData();
        formData.append('task_id', task_id);
        axios.post('/agents/doc_management/transactions/delete_task', formData, axios_options)
        .then(function (response) {
            $('.edit-task-button[data-task-id="'+task_id+'"]').closest('.list-group-item').fadeOut('slow');
            $('.task-container').collapse('hide');
            toastr['success']('Successfully Deleted');
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function filter_tasks(show) {

        $('.list-group-item-top').removeClass('list-group-item-top');
        $('.task').hide();
        $('.task[data-status="'+show+'"]').show();
        $('.task[data-status="'+show+'"]').eq(0).addClass('list-group-item-top');
    }

    function clear_form() {

        $('#task_id').val('');
        $('#reminder').val('');
        $('#task_title').val('');
        $('#task_option_days').val('0');
        $('#task_option_position').val('after');
        $('#task_action').val('');
        //$('#task_action_task option').first().prop('selected', true);
        $('#task_date').val('');
        $('#task_time').val('09:00');
        $('#task_members').val('').trigger('change');

        $('.invalid, .invalid-input, .invalid-label').removeClass('invalid invalid-input invalid-label');

    }

    function adjust_date() {

        let days = parseInt($('#task_option_days').val());
        let position = $('#task_option_position').val();

        let event_date = null;
        $('#task_date').val('');

        $('.no-date-info-div').addClass('hidden');

        if($('#task_action option:selected').val() != '') {

            $('#task_date').prop('disabled', true);

            if($('#task_action option:selected').data('has-db-column') == 'yes') {

                event_date = $('#task_action option:selected').data('date');
                $('.task-action-task').addClass('hidden');

            } else {

                if($('#task_action option:selected').prop('value') == '222') {

                    event_date = $('#task_action_task option:selected').data('date');

                } else {

                    if($('#task_action_task option:selected').data('task-completed') == 'yes') {
                        $('#task_date').val($('#task_action_task option:selected').data('date-completed'));
                    } else {
                        $('#task_date').val('');
                        $('.no-date-info-div').removeClass('hidden');
                    }
                    $('.task-action-task').removeClass('hidden');
                    return true;

                }

                $('.task-action-task').removeClass('hidden');
            }

        } else {

            $('#task_date').prop('disabled', false);

            //$('#task_date').val('');
            $('.task-action-task').addClass('hidden');

        }

        if(event_date) {

            event_date += ' 00:00:00';

            $('.no-date-info-div').addClass('hidden');

            let date_input = $('#task_date');

            let date = new Date(event_date);
            let new_date = '';
            if(position == 'before') {
                new_date = new Date(date.setDate(date.getDate()-days));
            } else {
                new_date = new Date(date.setDate(date.getDate()+days));
            }

            let year = new_date.getFullYear();
            let month = parseInt(new_date.getMonth()) + 1;
            month = ('0' + month).slice(-2);
            let day = ('0' + new_date.getDate()).slice(-2);

            event_date = year+'-'+month+'-'+day;

            date_input.val(event_date);

        } else {

            if($('#task_action option:selected').val() != '') {
                $('#task_date').val('');
                $('.no-date-info-div').removeClass('hidden');
                $('.no-date-event').text($('#task_action option:selected').text());
            }

        }

    }


}
