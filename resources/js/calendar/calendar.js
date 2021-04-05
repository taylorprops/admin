
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
            editable: true,
            eventClick: function(info) {

                let event_details = get_event_details(info);
                let x = $(info.el).offset().left;
                let y = $(info.el).offset().top;
                let el_width = $(info.el).width();
                let edit_event_div_width = $('#edit_event_div').width();
                let edit_event_div_height = $('#edit_event_div').height();
                let container_width = $(document).width();
                let container_height = $(document).height();

                $('.event-active').removeClass('event-active shadow');
                $(info.el).addClass('event-active shadow');

                let top, left;

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

            }
        });

        calendar.render();

        $(document).on('mousedown', function(e) {
            if (!$(e.target).is('#edit_event_div *') && !$(e.target).is('.fc-daygrid-event-harness *')) {
                $('#edit_event_div').addClass('hidden');
            }
        });

    });

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
        repeat_until = null,
        rrule = null;

        let event_id = info.event.id;
        let event_title = info.event.title;

        if(info.event._def.recurringDef) {
            rrule = info.event._def.recurringDef.typeData.rruleSet._rrule[0].options;
        }

        let start_date = new Date(info.event.start);
        let start_year = start_date.getFullYear();
        let start_month = parseInt(start_date.getMonth()) + 1;
        start_month = ('0' + start_month).slice(-2);
        let start_day = ('0' + start_date.getDate()).slice(-2);

        event_start_date = start_year+'-'+start_month+'-'+start_day;

        if(info.event.allDay == false) {

            start_hours = ('0' + start_date.getHours()).slice(-2);
            start_minutes = ('0' + start_date.getMinutes()).slice(-2);
            start_seconds = '00';

            event_start_time = start_hours+':'+start_minutes+':'+start_seconds;

            if(info.event.end) {

                end_date = new Date(info.event.end);
                end_year = end_date.getFullYear();
                end_month = parseInt(end_date.getMonth()) + 1;
                end_month = ('0' + end_month).slice(-2);
                end_day = ('0' + end_date.getDate()).slice(-2);

                event_end_date = end_year+'-'+end_month+'-'+end_day;

                if(end_date.getHours() != '00' && end_date.getMinutes() != '00') {
                    end_hours = ('0' + end_date.getHours()).slice(-2);
                    end_minutes = ('0' + end_date.getMinutes()).slice(-2);
                    end_seconds = '00';

                    event_end_time = end_hours+':'+end_minutes+':'+end_seconds;

                }

            }

        }

        if(rrule) {

            event_end_date = '';
            event_end_time = '';
            repeat_frequency = rrule.freq;
            repeat_interval = rrule.interval;
            repeat_until = rrule.until;

        }

        event_details = {};

        event_details.event_id = event_id;
        event_details.event_title = event_title;
        event_details.start_date = event_start_date;
        event_details.start_time = event_start_time;
        event_details.end_date = event_end_date;
        event_details.end_time = event_end_time;
        event_details.repeat_frequency = repeat_frequency;
        event_details.repeat_interval = repeat_interval
        event_details.repeat_until = repeat_until;


        return event_details;

    }

    function save_event(form) {

        let formData = new FormData(form[0]);

        axios.post('/calendar_update', formData, axios_options)
        .then(function (response) {
            console.log(response);
        })
        .catch(function (error) {
            console.log(error);
        });

    }

}
