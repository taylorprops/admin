import datepicker from 'js-datepicker';
import html2canvas from 'html2canvas';
import { Notifier } from '@airbrake/browser';

if(!document.URL.match(/admin\/$/) && !document.URL.match(/agentdocuments.com\/$/)) {
    const airbrake = new Notifier({
        projectId: 332797,
        projectKey: '15f129cfa6bcf9d6f60251e4f547a607',
        environment: app_env
    });
}



// check for duplicate ids
/* setTimeout(function() {
    $('[id]').each(function(){
        var ids = $('[id="'+this.id+'"]');
        if(ids.length > 1 && ids[0] == this) {
            console.warn('Multiple IDs #'+this.id);
        }
    });
}, 3000); */

$(function() {

    global_loading_off();

    /* let input = $('#refresh_page');
    input.val() == 'yes' ? location.href = location.href : input.val('yes');
    console.log(input.val()); */


    /* if(!document.URL.match(/admin\/$/)) {
        inactivityTime();
    } */


    window.toastr.options = {
        "timeOut": 3000,
        "preventDuplicates": false,
    }

    window.text_editor = function(options) {

        if(options.selector == '') {
            options.selector = '.text-editor';
        }
        options.content_style = 'body { font-size: .9rem; }',
        //options.content_css = '/css/tinymce.css';
        options.force_p_newlines = false;
        options.forced_root_block = '';
        options.branding = false;


        tinymce.remove(options.selector);
        tinymce.init(options);

    }



    // send csrf with every ajax request
    window._token = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': _token
        }
    });
    // axios headersObj
    window.axios_options = {
        headers: { 'X-CSRF-TOKEN': _token }
    };

    window.axios_headers_html = {
        'Accept-Version': 1,
        'Accept': 'text/html',
        'Content-Type': 'text/html'
    }

    // Add a response interceptor
    /* axios.interceptors.response.use(function (response) {
        //console.log(response);
        // if(response.status != 200) {
        //     // if ajax returns a redirect to login page this will force the parent page to redirect to login
        //     if(response.data.match(/doctype/)) {
        //         window.location = '/';
        //     }
        // }
        // return response;

    }, function (error) {
        // alert(error);
        // if(error.response.status == 404) {
        //     window.location = '/';
        // }
    }); */


    $(document).on('focus', '.numbers-only', function (e) {
        e.target.focus().select();
    });

    $('.draggable').draggable({
        handle: '.draggable-handle'
    });

    $(document).on('change', 'input[type="file"]', function() {
        let file_name = Array.from(this.files).map(x => x.name).join(', ')
        $(this).siblings('.custom-file-label').addClass("selected").html(file_name);
        /* let file_name = $(this).val();
        $(this).siblings('.custom-file-label').html(file_name); */
    });


    let format_phone = setInterval(function() {
        $('.phone').not('.formatted').each(function() {
            if($(this).val() != '') {
                global_format_phone(this);
                $(this).attr('maxlength', 14).addClass('formatted');
            }
        });
    }, 1000);


    window.ucwords = function(str) {
        return str
            //.toLowerCase()
            .split(' ')
            .map(function(word) {
                if(word) {
                    return word[0].toUpperCase() + word.substr(1);
                }
            })
            .join(' ');
    }

    window.shorten_text = function(text, max) {
        if(text.length > max) {
            return text.substring(0, max)+'...';
        }
        return text;
    }

    $(document).on('keyup change', '.phone', function () {
        global_format_phone(this);
        $(this).attr('maxlength', 14);
    });

    setInterval(function() {
        datepicker_custom();
        global_tooltip();
    }, 1000);




    window.datatable_settings = {
        "autoWidth": false,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "responsive": true,
        "destroy": true,
        fixedHeader: true,
        "language": {
            search: '',
            searchPlaceholder: 'Search'
        },
        "language": {
            "info": "_START_ to _END_ of _TOTAL_",
            "lengthMenu": "Show _MENU_",
            "search": ""
        },

    }

    window.data_table = function(page_length, table, sort_by, no_sort_cols, hidden_cols, show_buttons, show_search, show_info, show_paging, show_hide_cols = true, hide_header_and_footer = false) {

        /*
        table = $('#table_id')
        sort_by = [1, 'desc'] - col #, dir
        no_sort_cols = [0, 8] - array of cols
        hidden_cols = [0, 8] - array of cols
        show_buttons = true/false
        show_search = true/false
        show_info = true/false
        show_paging = true/false
        show_hide_cols = true/false
        hide_header_and_footer = true/false
        */

        datatable_settings.pageLength = parseInt(10);
        if(page_length != '') {
            datatable_settings.pageLength = parseInt(page_length);
        }

        if(sort_by.length > 0) {
            datatable_settings.order = [[sort_by[0], sort_by[1]]];
        }

        if(no_sort_cols.length > 0) {
            datatable_settings.columnDefs = [{
                orderable: false,
                targets: no_sort_cols
            }];
        }

        if(hidden_cols.length > 0) {
            hidden_cols.forEach(function(col) {
                datatable_settings.columnDefs.push({
                    targets: [col],
                    visible: false
                });
            });
        }

        let buttons = '';

        if(show_buttons == true) {
            datatable_settings.buttons = [
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ];
            buttons = '<B>';

            if(show_hide_cols == true) {
                datatable_settings.buttons.push({
                    extend: 'colvis',
                    text: 'Hide Columns'
                });
            }
        }



        let search = '';
        if(show_search == true) {
            search = '<f>';
        }

        let info = '';
        if(show_info == true) {
            info = '<i>';
        }

        let paging = '';
        let length = '';
        datatable_settings.paging = false;
        if(show_paging == true) {
            paging = '<p>';
            datatable_settings.paging = true;
            length = '<l>';
        }

        if(hide_header_and_footer == true) {
            datatable_settings.drawCallback = function() {
                $(this.api().table().header()).hide();
                $(this.api().table().footer()).hide();
            }
            info = '';
            paging = '';
            datatable_settings.paging = false;
            length = '';
            search = '';
            buttons = '';
            datatable_settings.buttons = [];
        }


        datatable_settings.dom = '<"d-flex justify-content-between flex-wrap align-items-center text-gray"'+search+info+length+buttons+'>rt<"d-flex justify-content-between align-items-center text-gray"'+info + paging+'>'

        let dt = table.DataTable(datatable_settings);

        $('.dataTables_filter [type="search"]').attr('placeholder', 'Search');

        return dt;

    }

    window.format_date = function(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, month, day].join('-');
    }

    // confirm modals on enter | requires .modal-confirm and .modal-confirm-button
    /* $('.modal-confirm').on('show.bs.modal', function () {
        $('body').off('keyup').on('keyup', function(event) {
            if (event.keyCode === 13) {
                $(this).find('.modal-confirm-button').trigger('click');
            }
        });
    }); */

    // multiple modal stacking
    $(document).on('show.bs.modal', '.modal', function () {
        // increase modal and backdrop z-index accordingly
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
        // make all but modal-xl draggable
        if(!$(this).find('modal-dialog').hasClass('modal-xl')) {
            $(this).addClass('draggable').find('.modal-header').addClass('draggable-handle');
        }

        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust()
            .responsive.recalc();

    });

    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust()
            .responsive.recalc();
    });

    if(!document.URL.match('/(password|login)/') && $('#login_page').length == 0) {
        get_global_notifications();
        setInterval(function() {
            get_global_notifications();
        }, 10000);
    }

    $(document).on('click', '#notifications_control', function() {
        $('#notifications_collapse').scrollTop(0);
    });

    $(document).on('click', '.bug-report-button', function() {

        $('.bug-report-button').html('Please Wait... <span class="spinner-border spinner-border-sm mr-2"></span>');

        setTimeout(function() {
            const screenshotTarget = document.body;
            html2canvas(screenshotTarget, {
                onclone: function() {
                    $('#bug_report_modal').modal('show');
                    $('.bug-report-button').html('Report Bug <i class="fal fa-bug"></i>');
                }
            }).then(canvas => {

                /* $('#send_bug_report').off('click').on('click', function() {

                    let validate = validate_form($('#bug_report_form'));

                    if(validate == 'yes') {

                        $('#send_bug_report').html('Sending Report... <span class="spinner-border spinner-border-sm mr-2"></span>');

                        let formData = new FormData();
                        let url = document.URL;
                        let image = canvas.toDataURL('image/png', 1);
                        window.open(image);
                        let message = $('#bug_report_message').val();

                        formData.append('message', message);
                        formData.append('url', url);
                        formData.append('image', image);

                        axios.post('/bug_reports/submit_bug_report', formData, axios_options)
                        .then(function (response) {
                            $('#bug_report_modal').modal('hide');
                            $('.modal-backdrop').removeClass('hidden');
                            $('#modal_success').modal().find('.modal-body').html('Your bug report was successfully sent. You will be notified once the issue has been resolved.');
                            $('#send_bug_report').html('Send Report <i class="fad fa-share ml-2"></i>');
                        })
                        .catch(function (error) {
                            console.log(error);
                        });

                    }

                }); */

                canvas.toBlob(function(blob) {

                    let url = document.URL;

                    $('#send_bug_report').off('click').on('click', function() {

                        let validate = validate_form($('#bug_report_form'));

                        if(validate == 'yes') {

                            $('#send_bug_report').html('Sending Report... <span class="spinner-border spinner-border-sm mr-2"></span>');

                            let formData = new FormData();
                            let message = $('#bug_report_message').val();

                            formData.append('message', message);
                            formData.append('url', url);
                            formData.append('image', blob);

                            axios.post('/bug_reports/submit_bug_report', formData, axios_options)
                            .then(function (response) {
                                $('#bug_report_modal').modal('hide');
                                $('.modal-backdrop').removeClass('hidden');
                                $('#modal_success').modal().find('.modal-body').html('Your bug report was successfully sent. You will be notified once the issue has been resolved.');
                                $('#send_bug_report').html('Send Report <i class="fad fa-share ml-2"></i>');
                            })
                            .catch(function (error) {
                                console.log(error);
                            });

                        }

                    });

                }, 'image/png');

            });
        }, 10);
    });

});

function get_global_notifications() {

    // notifications
    axios.get('/notifications/get_notifications')
    .then(function (response) {

        if(response.data.status == 'error') {
            window.location = '/login';
        }

        $('.global-notifications-div').html(response.data);

        $('.notifications-unread-count').text($('.global-notifications-count').first().text());

        $('.notifications-mark-as-read').on('click', function () {

            let id = $(this).data('id');
            let request = notifications_mark_read(id, 'read');
            request.then(function (response) {
                $('div.alert[data-id="'+id+'"]').fadeOut();
                let counter = $('.notifications-unread-count');
                let count = parseInt(counter.first().text()) - 1;
                counter.text(count);
            });
        });

        $('.notifications-mark-all').on('click', function () {
            $('#confirm_modal').modal().find('.modal-body').html('Mark All As Read?');
            $('#confirm_modal').modal().find('.modal-title').html('Please Confirm');
            $('#confirm_button').on('click', function() {
                let request = notifications_mark_read('0', 'read');
                request.then(function (response) {
                    get_global_notifications();
                });
            });
        });

        $('.notifications-mark-unread').on('click', function () {

            let id = $(this).data('id');
            let request = notifications_mark_read(id, 'unread');
            request.then(function (response) {
                get_global_notifications();
            });
        });

    })
    .catch(function (error) {
        console.log(error);
    });

    $('#notifications_collapse').on('shown.bs.collapse', function () {
        $(document).on('click', function (e) {
            let notification_div = $('#notifications_collapse');
            if (!notification_div.is(e.target) && notification_div.has(e.target).length === 0) {
                $('#notifications_collapse').collapse('hide');
            }
        });
    });

}

window.notifications_mark_read = function(id, mark) {

    let formData = new FormData();
    formData.append('id', id);
    formData.append('mark', mark);

    return axios.post('/notifications/mark_as_read', formData, axios_options);

}

window.send_email_general = function() {

    $('#send_email_general_button').html('<i class="fas fa-spinner fa-pulse mr-2"></i> Sending Email');

    let from = $('#email_general_from').val();
    let to = $('#email_general_to').val();
    let cc = $('#email_general_cc').val();

    let to_addresses = [];
    to_addresses.push({
        type: 'to',
        address: to
    });
    if(cc != '') {
        to_addresses.push({
            type: 'cc',
            address: cc
        });
    }
    let subject = $('#email_general_subject').val();
    let message = tinymce.activeEditor.getContent();

    let formData = new FormData();
    formData.append('from', from);
    formData.append('to_addresses', JSON.stringify(to_addresses));
    formData.append('subject', subject);
    formData.append('message', message);

    axios.post('/send_email', formData, axios_options)
    .then(function (response) {

        $('#send_email_general_button').html('<i class="fad fa-share mr-2"></i> Send Email');
        $('#email_general_modal').modal('hide');

        toastr['success']('Email Successfully Sent');

        reset_email_general();

    })
    .catch(function (error) {

    });

}

window.reset_email_general = function () {
    $('#email_general_to').val('');
    $('#email_general_cc').val('');
    $('#email_general_subject').val('');
    $('#email_general_message').val('');
}

window.datepicker_custom = function() {

    $('.datepicker').not('.datepicker-added').not('.field-datepicker').each(function() {

        $(this).addClass('datepicker-added');
        let id = $(this).prop('id');
        if(!id) {
            id = new Date().getTime();
        }
        const picker = datepicker('#'+id, {
            onSelect: (instance, date) => {
                let element = $('#' + instance.el.id);
                let wrapper = element.closest('.form-ele');
                show_cancel_date(wrapper, element);
            },
            onHide: instance => {

            },
            formatter: (input, date, instance) => {
                console.log(date);
                const value = date.toJSON().slice(0, 10);
                input.value = value;
                $('#'+id).focus().trigger('click');
            },
            showAllDates: true,
        });

        // update picker when changed dynamically
        /* $(this).on('change', function() {
            if(!$(this).val().match(/[0-9]{4}-[0-9]{2}-[0-9]{2}/)) {
                let date = $(this).val().split('-');
                console.log(date);
                picker.setDate(new Date(date[0], parseInt(date[1]) - 1, date[2]), true);
                setTimeout(function() {
                    const isHidden = picker.calendarContainer.classList.contains('qs-hidden');
                    if(!isHidden) {
                        picker.hide();
                    }
                }, 100);
            }
        }); */

    });

}

// session timeout
/* window.inactivityTime = function () {
    var time;
    window.onload = resetTimer;
    // DOM Events
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;

    function logout() {

        $('#confirm_modal').modal('show').find('.modal-title').html('Session Expired!');
        $('#confirm_modal').find('.modal-body').html('<div class="d-flex justify-content-start align-items-center"><div><i class="fad fa-exclamation-circle fa-2x text-danger mr-3"></i></div><div>Your session has expired due to inactivity.</div></div>');
        $('#confirm_modal').find('.modal-sm').removeClass('modal-sm').find('.modal-header').addClass('bg-danger');

        let logout = $('#confirm_modal').find('.btn-danger');
        logout.text('Log Off');
        let stay = $('#confirm_modal').find('.btn-primary');
        stay.text('Continue Session');

        let force_logout = setTimeout(function() {
            location.href = '/logout';
        }, 1000 * 60 * 5);

        logout.on('click', function() {
            location.href = '/';
        });
        stay.on('click', function() {
            clearTimeout(force_logout);
            resetTimer();
            $('#confirm_modal').modal('hide');
        });


    }

    function resetTimer() {
        clearTimeout(time);
        let timeout = 1000 * 60 * 60;
        //let timeout = 1000 * 5;
        time = setTimeout(logout, timeout);
    }
}; */


/**************************  STANDARD USE FUNCTIONS ***********************************/


window.getCookie = function(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

window.scrollToAnchor = function(id) {
    var anchor_position = $('#'+id).offset().top;
    $('html,body').animate({
        scrollTop: anchor_position
    }, 1500);
}

// Numbers Only
$(document).on('keydown', '.numbers-only', function (event) {
    // set attr  max with input type = text

    let allowed_keys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', ',', 'Backspace', 'ArrowLeft', 'ArrowRight', 'Delete', 'Tab', 'Control', 'v'];

    if(!$(this).hasClass('no-decimals')) {
        allowed_keys.push('.');
    }
    if(!allowed_keys.includes(event.key)) {
        event.preventDefault();
    } else {
        //let min = $(this).attr('min') ?? null;
        let max = $(this).attr('max') ?? null;
        /* if(min) {
            if(parseInt($(this).val()+event.key) < min) {
                event.preventDefault();
                $(this).val(event.key);
            }
        } */
        if(max) {
            if(parseInt($(this).val()+event.key) > max) {
                event.preventDefault();
                $(this).val(event.key);
            }
        }
    }


    // Allow special chars + arrows
    /* if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 190 || event.keyCode == 110
        || event.keyCode == 27 || event.keyCode == 13
        || ((event.keyCode == 65 || event.keyCode == 86 || event.keyCode == 90) && event.ctrlKey == true)
        || (event.keyCode >= 35 && event.keyCode <= 39)) {
        return;
    } else {

        // If it's not a number stop the keypress
        if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
            event.preventDefault();
        }
    } */
});

window.global_loading_on = function(ele, html) {
    // ele used if containing loading frame in an element, otherwise leave blank
    let spinner_html = ' \
    <div class="loading-spinner"> \
        <div class="spinner-grow text-success" role="status"> \
            <span class="sr-only">Loading...</span> \
        </div> \
        <div class="spinner-grow text-danger" role="status"> \
            <span class="sr-only">Loading...</span> \
        </div> \
        <div class="spinner-grow text-warning" role="status"> \
            <span class="sr-only">Loading...</span> \
        </div> \
        <div class="spinner-grow text-info" role="status"> \
            <span class="sr-only">Loading...</span> \
        </div> \
    </div> \
    <div class="loading-spinner-html mt-0 mx-2 mx-sm-auto">'+html+'</div> \
    ';

    if(ele != '') {
        $(ele).html(spinner_html);
    } else {
        $('body').append('<div class="loading-bg">'+spinner_html+'</div>');
    }
}
window.global_loading_off = function() {
    $('.loading-spinner, .loading-spinner-html, .loading-bg').remove();
}

window.global_tooltip = function() {
    $('[data-toggle="tooltip"]').tooltip({ html: true, trigger : 'hover' });
    $('[data-toggle="popover"]').popover({ html: true });
}



window.global_get_url_parameters = function(key) {
    // usage
    // let tab = global_get_url_parameters('tab');
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    if (urlParams.has(key)) {
        return urlParams.get(key);
    }
    return false;
}

// Format Phone
window.global_format_phone = function (obj) {
    if(obj) {
        let numbers = obj.value.replace(/\D/g, ''),
            char = { 0: '(', 3: ') ', 6: '-' };
        obj.value = '';
        for (let i = 0; i < numbers.length; i++) {
            if (i > 13) {
                return false;
            }
            obj.value += (char[i] || '') + numbers[i];
        }
    }
}

// FORMAT SOCIAL SECURITY
window.global_fmtssn = function (socInput) {
    re = /\D/g; // remove any characters that are not numbers
    socnum = socInput.value.replace(re, "");
    sslen = socnum.length;
    if (sslen > 3 && sslen < 6) {
        ssa = socnum.slice(0, 3);
        ssb = socnum.slice(3, 5);
        socInput.value = ssa + "-" + ssb;
    } else {
        if (sslen > 5) {
            ssa = socnum.slice(0, 3);
            ssb = socnum.slice(3, 5);
            ssc = socnum.slice(5, 9);
            socInput.value = ssa + "-" + ssb + "-" + ssc;
        } else {
            socInput.value = socnum;
        }
    }
}

/*
PURPOSE: remove duplicates from array
USAGE:
group_ids = ['a', 'b', 'c', 'c'];
group_ids = group_ids.filter(global_array_unique);
*/

window.global_array_unique = function (value, index, self) {
    return self.indexOf(value) === index;
}

// Format Money
window.global_format_number = function (num) {
    const formatter = new Intl.NumberFormat('en-US', {
        style: 'decimal',
        minimumFractionDigits: 0
    });

    num = num.toString().replace(/[,\$]/g, '');
    return formatter.format(num);
}

window.global_format_number_with_decimals = function (num) {
    const formatter = new Intl.NumberFormat('en-US', {
        style: 'currency', currency: 'USD'
    });

    num = num.replace(/[,\$]/g, '').toString();
    return formatter.format(num);
}

window.format_money = function(ele) {
    ele.val('$'+global_format_number(ele.val()));
}

window.format_money_with_decimals = function(ele) {
    ele.val(global_format_number_with_decimals(ele.val()));
}

window.global_format_money = function() {
    $('.money, .money-decimal').each(function() {
        let val = $(this).val();
        if(val.match(/[a-zA-Z]+/)) {
            $(this).val(val.replace(/[a-zA-Z]+/,''));
        }
    });
    if($('.money').length > 0) {
        format_money($('.money'));
        $('.money').on('keyup', function () {
            let val = $(this).val();
            if(val.match(/[a-zA-Z]+/)) {
                $(this).val(val.replace(/[a-zA-Z]+/,''));
            }

            format_money($(this));
        });
    }
    if($('.money-decimal').length > 0) {
        $('.money-decimal').each(function() {
            if($(this).val() != '') {
                format_money_with_decimals($(this));
            }
        });
        $('.money-decimal').on('change', function () {
            if($(this).val() != '') {
                format_money_with_decimals($(this));
            }
        })
        .on('keyup', function() {
            let val = $(this).val();
            if(val.match(/[a-zA-Z]+/)) {
                $(this).val(val.replace(/[a-zA-Z]+/,''));
            }
        });
    }
}

// Date Difference JS
window.global_date_diff = function (s, e) {
    let start = new Date(s);
    let end = new Date(e);
    let diff = new Date(end - start);
    let days = Math.ceil(diff / (1000 * 60 * 60 * 24));

    return days;
}

window.nl2br = function(str, replaceMode, isXhtml) {

    var breakTag = (isXhtml) ? '<br />' : '<br>';
    var replaceStr = (replaceMode) ? '$1'+ breakTag : '$1'+ breakTag +'$2';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, replaceStr);
}

$(document).on('keydown', function(event) {
    if (event.ctrlKey && event.key === 's') {
        $('.main-search-input').focus().select();
        return false;
    }
});

// get location details from zip code
/* window.get_location_details = function(zip) {
    if(zip.length == 5) {
        let location_details = [];
        let city = state = county = '';
        axios.get('/agents/doc_management/global_functions/get_location_details, {
            params: {
                zip: zip
            },
        })
        .then(function (response) {
            let data = response.data;
            city = data.city;
            state = data.state;
            county = data.county;
            location_details.push(city);
            location_details.push(state);
            location_details.push(county);
        })
        .catch(function (error) {

        });

        return location_details;
    }
} */

/* Multiple key strokes */
/* let keysPressed = {};
        document.addEventListener('keydown', (event) => {
            keysPressed[event.key] = true;

            if (keysPressed['Shift'] && event.key == 'Tab') {
                console.log(keysPressed['Shift']+' + '+event.key);
            }
        });

        document.addEventListener('keyup', (event) => {
            delete keysPressed[event.key];
        }); */
