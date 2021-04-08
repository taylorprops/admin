
if (document.URL.match(/calendar/)) {

    $(function () {

        let calendarEl = document.getElementById('calendar_div');

        let calendar = new FullCalendar.Calendar(calendarEl, {
            events: '/calendar_events',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay',
            },
            themeSystem: 'bootstrap',
            editable: false,
            selectable: true,
            eventClick: function(info) {

                $('.hide-multiple').show();
                show_edit_event(calendar, info);

            },
            select: function (info) {

                if(info.startStr != info.endStr) {
                    show_add_event(calendar, info, true);
                } else {
                    show_add_event(calendar, info, false);
                }

            },
            dateClick: function(info) {

                if($('.new-event').length > 0) {
                    return false;
                }
                show_add_event(calendar, info, false);
            }
        });

        calendar.render();

        $(document).on('mousedown', function(e) {
            if (!$(e.target).is('#edit_event_div *') && !$(e.target).is('.fc-daygrid-event-harness *')) {
                $('#edit_event_div').addClass('hidden');
            }
        });

        $('#all_day').on('change', show_times);

        $('#repeat_frequency').on('change', show_repeat);


        function show_add_event(calendar, info, multiple) {

            let id =  new Date().getTime();



            if(multiple == true) {

                calendar.addEvent({
                    id: id,
                    title: 'New Event',
                    start: info.startStr,
                    end: info.endStr,
                    allDay: true,
                    color: 'green',
                    classNames: ['new-event'],
                    extendedProps: ['new-event', 'multiple']
                });

            } else {

                calendar.addEvent({
                    id: id,
                    title: 'New Event',
                    start: info.dateStr,
                    allDay: true,
                    color: 'green',
                    classNames: ['new-event'],
                    extendedProps: ['new-event']
                });

            }

            let new_event = calendar.getEventById(id);

            //$('#repeat_interval').removeClass('required');
            $('#repeat_frequency').val('none');
            show_repeat();

            $('.new-event .fc-event-title-container').trigger('click');

            $(document).on('mousedown', function(e) {

                if(new_event) {
                    if($(e.target).closest('.fc-daygrid-event').hasClass('new-event')) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        return false;
                    } else {
                        if (!$(e.target).is('#edit_event_div *')) {
                            new_event.remove();
                        }
                    }
                }
            });

            $('#cancel_new_event_button').off('click').on('click', function() {
                if(new_event) {
                    new_event.remove();
                    $('#edit_event_div').addClass('hidden');
                }
            });



            $('#event_id').val('');

        }

        function show_edit_event(calendar, info) {

            let event_details = get_event_details(info);

            let multiple = false;
            let properties = Object.values(info.event.extendedProps);
            if(properties && properties.includes('multiple')) {
                multiple = true;
            }

            $('#event_id').val(event_details.event_id);
            $('#delete_event_button').data('event-id', event_details.event_id);
            $('#event_title').val(event_details.event_title);
            if($(info.el).hasClass('new-event')) {
                $('#event_title').val('');
            }

            $('#start_date').val(event_details.start_date);
            $('#start_time').val(event_details.start_time);

            // $('#end_date').val(info.event.extendedProps.end_actual);
            $('#end_date').val(event_details.end_date);

            if(multiple) {

                let end_date = new Date(event_details.end_date+' 00:00:00');
                end_date = new Date(end_date.setHours(end_date.getHours() - 1));
                let end_year = end_date.getFullYear();
                let end_month = parseInt(end_date.getMonth()) + 1;
                end_month = ('0' + end_month).slice(-2);
                let end_day = ('0' + end_date.getDate()).slice(-2);
                $('#end_date').val(end_year + '-' + end_month + '-' + end_day);

            }

            $('#end_time').val(event_details.end_time);

            let frequency = 'none';
            if(event_details.repeat_frequency) {
                frequency = event_details.repeat_frequency;
            }

            if(!multiple) {

                $('.hide-multiple').show();

                $('#repeat_frequency').val(frequency);
                $('#repeat_interval').val(event_details.repeat_interval);
                $('#repeat_until').val(event_details.repeat_until);

                show_repeat();

            } else {

                if($('#start_date').val() == $('#end_date').val()) {
                    $('.hide-multiple').show();
                } else {
                    $('.hide-multiple').hide();
                }

            }

            // hide end date and show time if not all day
            if(event_details.all_day == false) {
                $('#all_day').prop('checked', false);
                $('.end-date').hide();
                $('.times').show();
            } else  if(event_details.all_day == true) {
                $('#all_day').prop('checked', true);
                $('.end-date').show();
                $('.times').hide();
            }

            $('#end_date').prop('min', $('#start_date').val());

            $('.event-active').removeClass('event-active shadow');
            $(info.el).addClass('event-active shadow');

            let top, left;
            let x = $(info.el).offset().left;
            let y = $(info.el).offset().top;
            let el_width = $(info.el).width();
            let edit_event_div_width = $('#edit_event_div').width();
            let edit_event_div_height = $('#edit_event_div').height();
            let container_width = $(document).width();
            let container_height = $(document).height();

            if(el_width > 200) {
                el_width = 200;
            }

            // if left side
            if(x < container_width / 2) {
                left = x + el_width;
            // if right side
            } else if(x >= container_width / 2) {
                left = x - edit_event_div_width - 35;
            }

            // if top side
            if(y < container_height / 2) {
                top = y;
            // if bottom side
            } else if(y >= container_height / 2) {
                top = y - edit_event_div_height - 10;
            }

            let coords = {
                top: top+'px',
                left: left+'px',
            }

            // only remove hidden if not already opened. This way it animates to next position
            if($('#edit_event_div').hasClass('hidden')) {
                $('#edit_event_div').removeClass('hidden').css(coords);
            } else {
                $('#edit_event_div').animate(coords);
            }

            $('#cancel_new_event_button').on('click', function() {
                $('#edit_event_div').addClass('hidden');
                $('.event-active').removeClass('event-active shadow');
            });

            $('#save_event_button').off('click').on('click', function() {
                save_event(calendar, info);
            });

            $('#delete_event_button').off('click').on('click', function() {
                delete_event($(this).data('event-id'));
            });

            $('#start_time').on('change', function() {
                let start = new Date($('#start_date').val()+' '+$(this).val());
                start = new Date(start.setHours(start.getHours() + 1));

                end_hours = ('0' + start.getHours()).slice(-2);
                end_minutes = ('0' + start.getMinutes()).slice(-2);
                end_seconds = '00';
                event_end_time = end_hours+':'+end_minutes+':'+end_seconds;

                $('#end_time').val(event_end_time);
            });

            $('#start_date, #end_date').on('change', function(e) {
                if(e.target.id == 'start_date') {
                    $('#end_date').prop('min', $('#start_date').val());
                }
                if($('#start_date').val() == $('#end_date').val()) {
                    $('.hide-multiple').show();
                } else {
                    $('.hide-multiple').hide();
                }
            });

        }


        function show_repeat() {

            if($('#repeat_frequency').val() == 'none') {
                $('.repeat').hide();
                $('#repeat_interval').val('').removeClass('required');
                $('#repeat_until').val('');
            } else {
                $('.repeat').show();
                $('#frequency_text').text($('#repeat_frequency option:selected').data('text'));
                $('#repeat_interval').addClass('required');
            }
        }

        function show_times() {
            if($('#all_day').is(':checked')) {
                $('.times').hide();
                $('.end-date').show();
                $('#repeat_interval').removeClass('required');
            } else {
                $('.times').show();
                $('.end-date').hide();

            }
        }

        function get_event_details(info) {

            let event_start_date = null,
            event_start_time = null,
            event_end_date = null,
            event_end_time = null,
            event_start = null,
            event_end = null,
            start_hours = null,
            start_minutes = null,
            start_seconds = null,
            end_date = null,
            end_year = null,
            end_month = null,
            end_day = null,
            end_hours = null,
            end_minutes = null,
            end_seconds = null,
            repeat_frequency = null,
            repeat_interval = null,
            repeat_until = null;

            let event_id = info.event ? info.event.id : '';
            let event_title = info.event.title;

            let start_date = new Date(info.event.start);
            let start_year = start_date.getFullYear();
            let start_month = parseInt(start_date.getMonth()) + 1;
            start_month = ('0' + start_month).slice(-2);
            let start_day = ('0' + start_date.getDate()).slice(-2);
            let all_day = info.event.allDay;

            event_start_date = start_year+'-'+start_month+'-'+start_day;

            if(start_date.getHours() != '00') {
                start_hours = ('0' + start_date.getHours()).slice(-2);
                start_minutes = ('0' + start_date.getMinutes()).slice(-2);
                start_seconds = '00';
                event_start_time = start_hours+':'+start_minutes+':'+start_seconds;
            } else {
                event_start_time = '09:00:00';
            }

            if(info.event.end) {

                end_date = new Date(info.event.end);
                end_year = end_date.getFullYear();
                end_month = parseInt(end_date.getMonth()) + 1;
                end_month = ('0' + end_month).slice(-2);
                end_day = ('0' + end_date.getDate()).slice(-2);

                event_end_date = end_year+'-'+end_month+'-'+end_day;

                end_hours = ('0' + end_date.getHours()).slice(-2);
                end_minutes = ('0' + end_date.getMinutes()).slice(-2);
                end_seconds = '00';

                event_end_time = end_hours+':'+end_minutes+':'+end_seconds;

            } else {

                event_end_date = event_start_date;
                event_end_time = '10:00:00';

            }

            if(info.event.extendedProps.freq) {

                repeat_frequency = info.event.extendedProps.freq;
                repeat_interval = info.event.extendedProps.interval;
                repeat_until = info.event.extendedProps.until;

            }

            let event_details = {};

            event_details.event_id = event_id;
            event_details.event_title = event_title;
            event_details.all_day = all_day;
            event_details.start_date = event_start_date;
            event_details.start_time = event_start_time;
            event_details.end_date = event_end_date;
            event_details.end_time = event_end_time;
            event_details.repeat_frequency = repeat_frequency;
            event_details.repeat_interval = repeat_interval;
            event_details.repeat_until = repeat_until;


            return event_details;

        }

        function save_event(calendar, info) {

            let form = $('#edit_event_form');

            let validate = validate_form(form);

            if(validate == 'yes') {

                let formData = new FormData(form[0]);

                let all_day = $('#all_day').is(':checked') ? true : false;
                formData.append('all_day', all_day);

                axios.post('/calendar_update', formData, axios_options)
                .then(function (response) {
                    toastr['success']('Event Saved');
                    $('#edit_event_div').addClass('hidden');
                    $('.event-active').removeClass('event-active shadow');

                    calendar.getEventById(info.event.id).remove();
                    $('.new-event').closest('.fc-daygrid-event-harness').remove();
                    calendar.refetchEvents();

                })
                .catch(function (error) {

                });

            }

        }

        function delete_event(event_id) {

            let event = calendar.getEventById(event_id);
            event.remove();

            let formData = new FormData();
            formData.append('event_id', event_id);

            axios.post('/calendar_delete', formData, axios_options)
            .then(function (response) {

                $('#edit_event_div').addClass('hidden');
                $('.event-active').removeClass('event-active shadow');
                toastr['success']('Event Successfully Deleted');

            })
            .catch(function (error) {
                console.log(error);
            });

        }

    });

}
