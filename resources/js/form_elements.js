let validate_email = require('email-validator');

setInterval(function() {
    $('.custom-form-element').each(function() {
        if($(this).closest('.form-ele').length == 0) {
            form_elements();
        }
    });
}, 100);



// trigger change on any input changes
(function($){
    let originalVal = $.fn.val;
    $.fn.val = function() {
        let prev;
        if(arguments.length > 0) {
            prev = originalVal.apply(this,[]);
        }
        let result = originalVal.apply(this,arguments);
        if(arguments.length > 0 && prev != originalVal.apply(this,[])) {
            $(this).change();
        }
        return result;
    };
})(jQuery);

// FORM INPUT CHANGES
$(document).on('change', '.custom-form-element', function() {

    if(!$(this).hasClass('form-input-checkbox')) {

        let label = $(this).closest('.form-ele').find('label');
        if($(this).val() != '') {
            label.addClass('active');
        } else {
            label.removeClass('active');
        }
        if($(this).hasClass('form-select')) {
            if($(this).val() != $(this).next('.form-select-wrapper').find('.form-select-value-input').val()) {
                select_refresh($(this), null);
            }
        }

    }

});


// activate labels on focus and hide on blur if empty
$(document).on('focus', 'input.custom-form-element, textarea.custom-form-element', function(e) {
    if(!$(this).hasClass('form-input-file')) {
        e.stopImmediatePropagation();
        $(this).next('label').addClass('active');
        hide_dropdowns();
    }
});

$(document).on('blur', '.custom-form-element', function() {
    if($(this).val() == '') {
        $(this).next('label').removeClass('active');
    }
});


// show file name in input
$(document).on('change', '.custom-file-input', function() {
    let file_name = $(this).val().split('\\').pop();
    if($(this).val() != '') {
        $(this).siblings('.custom-file-label').addClass('selected').html(file_name);
        $(this).siblings('.form-input-label').addClass('active');
    } else {
        $(this).siblings('.custom-file-label').removeClass('selected').html('');
        $(this).siblings('.form-input-label').removeClass('active');
    }
});

window.form_elements = function () {

    //console.log('form_elements');
    /*
    Element classes
    input | .form-input
    select | .form-select
    textarea | .form-textarea
    checkbox | .form-checkbox
    radio | .form-radio
    file input | .form-input-file
    color | form-input-color

    Element options
    all
        data-label | adds label
        disabled | functions as expected
    select
        .form-select-no-search | removes search option
        .form-select-no-cancel | removes cancel option

        multiple select uses pretty checkboxes

    */

    // remove disabled
    //$('.form-select-value-input').removeClass('disabled').prop('disabled', false);


    const form_elements = ['form-input', 'form-textarea', 'form-select', 'form-checkbox', 'form-radio', 'form-input-file', 'form-input-color'];


    form_elements.map(function (form_type, index) {

        const form_element = $('.' + form_type);

        //$('.form-select-value-input').removeClass('caret');

        //if($('.' + form_type).length > 0) {

            // add container and label
            form_element.each(function () {

                index += 1;

                const element = $(this);

                const multiple = (element.attr('multiple') == 'multiple' || element.attr('multiple') == true) ? true : false;

                // check if form-ele already applied
                if (element.closest('.form-ele').length == 0) {

                    let select_input_id = Math.floor(Math.random() * 1000000000000000) + 10000000000;
                    // avoid duplicate ids
                    if ($('#' + select_input_id).length > 0) {
                        console.log('ERROR: There are multiple elements with the same id');
                        select_input_id = select_input_id + index;
                    }

                    let id = 'input_' + select_input_id;

                    if (element.attr('id') != undefined) {
                        id = element.attr('id');
                    } else {
                        element.attr('id', id);
                    }

                    let active_label = '';
                    if (element.val() != '') {
                        active_label = 'active';
                    }

                    // wrap element with form-ele
                    // add labels on all except: select and file input
                    // select label is added in select_html
                    // file label is added in file_html
                    let label = element.data('label');
                    let label_view = '';
                    if (!label) {
                        label = '';
                        label_view = 'hidden';
                    }

                    // if html in label
                    if(label.match(/='/)) {
                        label = label.replace(/'/g, '"');
                    }

                    let small = element.hasClass('form-small') ? 'form-small' : '';
                    let large = element.hasClass('form-large') ? 'form-large' : '';

                    if($(document).width() < 576) {
                        large = '';
                        $('.form-large').removeClass('form-large');
                    }
                    let wide = element.hasClass('form-wide') ? 'form-wide' : '';

                    element.show();

                    if (form_type == 'form-input' || form_type == 'form-textarea') {

                        element.wrap('<div class="form-ele '+small+' '+large+'"></div>').parent('.form-ele').append('<label for="' + id + '" class="' + form_type + '-label ' + active_label + ' '+small+' '+large+' '+label_view+'">' + label + '</label>');

                    } else if (form_type == 'form-input-file') {

                        element.addClass('custom-file-input');

                        element.wrap('<div class="form-ele custom-file '+small+' '+large+'"></div>').parent('.form-ele').append('<label for="' + id + '" class="' + form_type + '-label ' + active_label + ' '+small+' '+large+' '+label_view+' custom-file-label"></label><label for="' + id + '" class="form-input-label ' + active_label + '">' + label + '</label>');


                    } else if (form_type == 'form-checkbox') {

                        element.wrap('<div class="form-ele"><div class="pretty p-svg p-smooth"></div></div>').parent('.pretty').append('<div class="state p-success"><svg class="svg svg-icon" viewBox="0 0 20 20"><path d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z" style="stroke: white;fill:white;"></path></svg><label for="' + id + '" class="form-check-label ' + form_type + '-label ' + active_label + '">' + label + '</label></div>');


                    } else if (form_type == 'form-radio') {

                        element.wrap('<div class="form-ele pretty p-default p-thick p-round p-smooth"></div>').parent('.form-ele').append('<div class="state p-primary-o"><label for="' + id + '" class="form-check-label ' + form_type + '-label ' + active_label + '">' + label + '</label></div>');

                    } else if (form_type == 'form-input-color') {


                        let color = $(this).val();
                        let classname = 'add-resource-color';
                        if ($(this).hasClass('edit-resource-color')) {
                            classname = 'edit-resource-color';
                        }
                        let color_html = ' \
                        <div class="form-ele"> \
                            <div class="colorpicker-div d-flex justify-content-between"> \
                                <div class="colorpicker-text">'+ label + '</div> \
                                <label class="colorpicker-label"><input type="color" class="'+ classname + ' colorpicker" value="' + color + '" data-default-value="' + color + '"></label> \
                            </div> \
                        </div> \
                        ';

                        let parent = element.parent();
                        element.remove();
                        parent.html(color_html);

                    } else if (form_type == 'form-select') {

                        element.wrap('<div class="form-ele '+small+' '+large+' select"></div>');
                        // get wrapper to append to
                        let wrapper = element.parent();

                        // hide select element
                        element.hide();

                        let disabled = '';
                        if (element.prop('disabled') == true) {
                            disabled = 'disabled';
                        }

                        // add delete if clearable
                        let clear_value = '';
                        if (disabled == '') {
                            clear_value = '<div class="form-select-value-cancel"><i class="fal fa-times"></i></div>';
                        }

                        let select_html = ' \
                        <div class="form-select-wrapper"> \
                            ' + clear_value + ' \
                            <label class="' + form_type + '-label ' + small + ' '+large+' '+label_view+'" for="select_value_' + select_input_id + '">' + label + '</label> \
                            <input type="text" class="form-select-value-input caret '+ disabled + '" id="select_value_' + select_input_id + '" readonly ' + disabled + '> \
                            <div class="form-select-dropdown shadow '+wide+'"> \
                        ';
                        //if(!element.hasClass('form-select-no-search')) {
                            select_html += ' \
                                <div class="form-select-search-div"> \
                                    <input type="text" class="form-select-search-input" placeholder="Search"> \
                                </div> \
                            ';
                        //}
                        select_html += ' \
                                <div class="form-select-options-div"> \
                                    <ul class="form-select-ul"></ul> \
                                </div> \
                            </div > \
                        </div> \
                        ';

                        wrapper.append(select_html);

                        let input = wrapper.find('.form-select-value-input');

                        // add dropdown html
                        let input_text = '';
                        element.children('option').map(function (index, option) {
                            option = $(option);
                            let value = option.prop('value');

                            if (value != '') {

                                let selected = '';
                                let text = option.text();
                                /* if(option.data('html')) {
                                    text = option.data('html');
                                } */

                                if (option.prop('selected') == true || option.prop('selected') == 'selected') {
                                    selected = 'active';
                                    input_text = text;
                                }


                                let li_html = text;
                                let multiple_li_class = '';

                                if (multiple) {
                                    let checked = (option.prop('selected') == 'checked' || option.prop('selected') == true) ? 'checked' : '';
                                    li_html = ' \
                                    <div class="mt-1 mb-1 pretty p-default p-pulse"> \
                                        <input type="checkbox" class="form-check-input select-checkbox" id="check_'+ select_input_id + '_' + index + '" data-index="' + index + '" data-value="' + value + '" data-text="' + text + '" ' + checked + '> \
                                        <div class="state p-primary-o"> \
                                            <label class="form-check-label" for="check_'+ select_input_id + '_' + index + '">' + text + '</label> \
                                        </div> \
                                    </div > \
                                    ';
                                    multiple_li_class = 'form-check-input-multiple';
                                }

                                let li = '<li class="form-select-li ' + selected + ' ' + multiple_li_class + '" data-index="' + index + '" data-value="' + value + '" data-text="' + text + '">' + li_html + '</li>';
                                wrapper.find('.form-select-ul').append(li);

                            }

                        });

                        if (element.val() != '') {
                            if (!multiple) {
                                // add value to input if selected
                                input.val(input_text).trigger('change');
                            }
                            // show cancel option
                            if (!element.hasClass('form-select-no-cancel')) {
                                if(wrapper.find('.form-select-value-input').val() == '') {
                                    wrapper.find('.form-select-value-input').addClass('caret');
                                    wrapper.find('.form-select-value-cancel').hide();
                                } else {
                                    wrapper.find('.form-select-value-input').removeClass('caret');
                                    wrapper.find('.form-select-value-cancel').show();
                                }
                            }
                            wrapper.find('label').addClass('active');
                        }

                        // add save button to exit out of multiple select
                        if (multiple) {
                            wrapper.find('.form-select-dropdown').append('<div class="w-100 form-select-save-div"><div class="d-flex d-flex justify-content-center p-0"><a href="javascript: void(0)" class="form-select-multiple-save btn btn-primary btn-sm">Close</a></div></div>');
                            $('.form-select-multiple-save').on('click', function () {
                                setTimeout(function() {
                                    hide_dropdowns();
                                }, 100);
                            });

                            set_multiple_select_value(wrapper, input);

                            /* // when a checkbox in a multiple select is changed
                            set_multiple_select(wrapper, input); */

                        }

                        // remove cancel option if class form-select-no-cancel is set
                        if (!element.hasClass('form-select-no-cancel')) {
                            wrapper.find('.form-select-value-cancel').on('click', function () {
                                element.val('').find('option').attr('selected', false);
                                element.trigger('change');
                                wrapper.find('.form-select-value-input').val('').trigger('change');
                                wrapper.find('label').removeClass('active');
                                wrapper.find('li').removeClass('active');
                                wrapper.find('.form-select-value-cancel').hide();
                                wrapper.find('.form-select-value-input').addClass('caret');
                                wrapper.find('.form-check-input').prop('checked', false);
                                hide_dropdowns();
                            });
                        }

                        // hide search option if class form-select-no-search is set
                        if (element.hasClass('form-select-no-search')) {

                            wrapper.find('.form-select-search-div, .form-select-search-input').css({ 'opacity': '0', 'height': '0px'});

                        }

                        dropdown_search(wrapper, input, element, multiple);


                        // when a single li is clicked
                        wrapper.find('.form-select-li').on('click', function () {

                            if (!$(this).hasClass('form-check-input-multiple')) {

                                hide_dropdowns();
                                let li = $(this);
                                let value = li.data('value');
                                let text = li.data('text');
                                let input = li.closest('.form-ele').find('.form-select-value-input');
                                let dropdown = li.closest('.form-ele').find('.form-select-dropdown');
                                let element = li.closest('.form-ele').find('.form-select');

                                $('.form-select-matched-option').removeClass('form-select-matched-option');
                                // set input value
                                input.val(text);
                                shorten_value(input, text, false);
                                input.trigger('change');
                                wrapper.find('label').addClass('active');

                                // remove active from all li and add to selected
                                dropdown.find('.form-select-li').removeClass('active');
                                li.addClass('active');
                                // update select element
                                element.val(value);
                                //element.trigger('change');


                            } else {
                                set_multiple_select($(this));
                            }

                            if (!element.hasClass('form-select-no-cancel')) {
                                input.siblings('.form-select-value-cancel').show();
                                wrapper.find('.form-select-value-input').removeClass('caret');
                            }

                        });



                    } // end else if (form_type == 'form-select') {

                    if (form_type != 'form-checkbox' && form_type != 'form-radio') {
                        if(element.hasClass('required')) {
                            if(form_type == 'form-select') {
                                $(this).next('div').find('.form-select-value-input').addClass('required-form-ele');
                            } else if(form_type == 'form-input-file') {
                                $(this).next('label').addClass('required-form-ele');
                            }
                        }

                        if(element.hasClass('datepicker')) {

                            element.prop('readonly', true);

                            let wrapper = element.closest('.form-ele');

                            element.closest('.form-ele').append('<div class="form-datepicker-cancel"><i class="fal fa-times fa-xs"></i></div><div class="datepicker-div"><i class="fad fa-calendar-alt fa-xs"></i></div>');

                            show_cancel_date(wrapper, element);
                            element.on('change', function() {
                                show_cancel_date(wrapper, element);
                            });

                            if(element.val() != '') {
                                wrapper.find('label').addClass('active');
                            }

                        }
                    }

                    // hide any open select dropdowns
                    /* element.closest('.form-ele *').on('focus', function () {
                        console.log('hiding dropdowns');
                        hide_dropdowns();
                    }); */


                } // end if (!element.parent().hasClass('form-ele')) {

            }); // end form_element.each(function () {

        //}

    }); // end form_elements.map(function (form_type) {


    if ($('input[type=color]').length > 0) {
        $('input[type=color]').each(function () {
            let bg_color = $(this).val();
            $(this).parent('.colorpicker-label').css({ background: bg_color }).parent('.colorpicker-div').css({ border: '1px solid ' + bg_color }).find('.colorpicker-text').css({ color: bg_color });
        });
        $('input[type=color]').on('change', function () {
            let bg_color = $(this).val();
            $(this).parent('.colorpicker-label').css({ background: bg_color }).parent('.colorpicker-div').css({ border: '1px solid ' + bg_color }).find('.colorpicker-text').css({ color: bg_color });
        });
        $('.colorpicker-text').on('click', function () {
            $(this).next('label').trigger('click');
        });
    }

    // show dropdown on focus or click
    $('.form-select-value-input').on('focus', function (e) {
        e.stopImmediatePropagation();
        $(this).addClass('form-select-value-input-focus');
        show_dropdown($(this));
    });
    $('.form-select-value-input').off('mousedown').on('mousedown', function (e) {
        e.preventDefault();
        $(this).addClass('form-select-value-input-focus');
        show_dropdown($(this));
    });

    $('.form-ele').removeClass('hidden');
    $('.custom-form-element.hidden').each(function () {
        $(this).closest('.form-ele').addClass('hidden');
    });



}

window.set_multiple_select = function(li) {

    let form_ele = li.closest('.form-ele');
    let form_select = form_ele.find('.form-select');
    let ul = form_ele.find('.form-select-ul');
    let input = form_ele.find('.form-select-value-input');
    //let selected_checks = [];

    ul.find('.form-select-li').removeClass('active');
    form_select.find('option').prop('selected', false);

    ul.find('.form-check-input:checked').each(function () {

        let checked = $(this);
        let index = checked.data('index');
        //selected_checks.push(checked.data('value'));
        checked.closest('li').addClass('active');
        // to show selected first use below
        // $(this).closest('li').addClass('active').prependTo('.form-select-ul');

        // set select element value
        form_select.find('option').eq(index).prop('selected', true);

    });

    if (form_select.val() == '') {
        form_select.closest('.form-ele').find('.form-select-value-cancel').hide();
        ul.find('.form-select-value-input').addClass('caret');
        input.val('');
    }

    form_select.on('blur', function() {
        $(this).trigger('change');
    });

    // shorten input value if too long
    set_multiple_select_value(ul, input);

}

window.show_cancel_date = function(wrapper, element) {

    if(element.val() == '') {
        wrapper.find('.form-datepicker-cancel').hide();
        if(element.closest('.field-div-container').find('div.data-div').length == 1) {
            element.closest('.field-div-container').find('div.data-div').html('');
        }
    } else {
        wrapper.find('.form-datepicker-cancel').show().on('click', function () {
            element.val('').trigger('change');
            wrapper.find('.form-datepicker-cancel').hide();
            wrapper.find('label').removeClass('active');
        });
    }
}


function show_dropdown(input) {

    let wrapper = input.closest('.form-ele');
    let select = wrapper.find('select');
    let dropdown = input.next('.form-select-dropdown');

    $('.form-select-value-input').removeClass('form-select-value-input-focus');
    wrapper.find('.form-select-li').removeClass('hidden');

    if(wrapper.find('select').prop('disabled') == false) {
        // close dropdown if already open
        if(dropdown.hasClass('active')) {
            dropdown.removeClass('active');
            if(input.val() == '') {
                input.prev('label').removeClass('active');
            }
        } else {
            hide_dropdowns();
            dropdown.addClass('active');
            input.addClass('form-select-value-input-focus');
            dropdown.find('.form-select-search-input').focus();
        }

    }


}

function hide_dropdowns() {
    if($('.form-select-dropdown.active').length > 0) {
        $('.form-select-dropdown.active').removeClass('active').find('.form-select-search-input').val('').trigger('change');
        $('.form-select-value-input-focus').removeClass('form-select-value-input-focus');

        $('.form-select-value-input').each(function() {
            if($(this).val() == '') {
                $(this).prev('label').removeClass('active');
            } else {
                $(this).prev('label').addClass('active');
            }
        });

        $('.form-select-li.matched').removeClass('matched').show();
        $('.form-select-matched-option').removeClass('form-select-matched-option');
    }
}


$(document).on('mousedown', function (e) {
    let container = $('.form-select-wrapper');
    if (!container.is(e.target) && container.has(e.target).length === 0) {
        hide_dropdowns();
    }
});


function dropdown_search(wrapper, input, element, multiple) {

    let search_input = wrapper.find('.form-select-search-input');

    search_input.on('keydown', function (e) {

        // if tab pressed
        if (e.key == 'Tab') {

            if(wrapper.find('.form-select-matched-option').length > 0) {

                e.preventDefault();

                if (!multiple) {

                    wrapper.find('.form-select-matched-option').trigger('click');
                    /* input.val(wrapper.find('.form-select-matched-option').text()).trigger('change');
                    element.find('option').attr('selected', false);
                    //element.find('option').eq($('.form-select-matched-option').data('index')).attr('selected', true);
                    element.find('option[value='+$('.form-select-matched-option').data('value')+']').attr('selected', true);
                    element.trigger('change');
                    // remove active from all li and add to selected
                    wrapper.find('.form-select-li').removeClass('active');
                    $('.form-select-matched-option').addClass('active');
                    hide_dropdowns(); */

                } else {

                    $('.form-select-matched-option input').prop('checked', true);
                    element.find('option').eq($('.form-select-matched-option').data('index')).attr('selected', true);
                    $('.form-select-matched-option').addClass('active');
                    set_multiple_select_value(wrapper, input);

                }

                if (!element.hasClass('form-select-no-cancel')) {
                    input.siblings('.form-select-value-cancel').show();
                    wrapper.find('.form-select-value-input').removeClass('caret');
                }

            }

            hide_dropdowns();

        }

    });

    search_input.on('keyup', function (e) {


        if(search_input.val().length > 0) {

            search_regex = new RegExp(search_input.val(), 'i');

            wrapper.find('.matched').removeClass('matched form-select-matched-option');
            wrapper.find('.form-select-li.active').removeClass('active');
            if(!element.hasClass('form-select-no-search')) {
                wrapper.find('.form-select-li').addClass('hidden');
            }

            wrapper.find('.form-select-li').each(function () {

                if ($(this).data('text').match(search_regex)) {
                    $(this).addClass('matched').removeClass('hidden');
                } else {
                    $(this).removeClass('matched');
                    if(!element.hasClass('form-select-no-search')) {
                        $(this).addClass('hidden');
                    }
                }

            });

            wrapper.find('.matched').first().addClass('form-select-matched-option');

        } else {

            wrapper.find('.form-select-li').show();
            wrapper.find('.form-select-matched-option').removeClass('form-select-matched-option');

        }

    });

}

function reset_labels() {
    $('.form-select-value-input').each(function() {
        if($(this).val() == '') {
            $(this).prev('label').removeClass('active');
        } else {
            $(this).prev('label').addClass('active');
        }
    });
}

/* setInterval(function () {
    $('.form-ele').removeClass('hidden');
    $('.custom-form-element.hidden').each(function () {
        $(this).closest('.form-ele').addClass('hidden');
    });
}, 500); */

window.validate_form = function (form, debug = false) {

    // TODO: add checkbox and radio validation
    let pass = 'yes';
    // remove all current invalid
    $('.invalid-label').removeClass('invalid-label');
    $('.invalid-input').removeClass('invalid-input');

    let invalid_email = 'no';

    form.find('.required').each(function () {

        let ele, classname, ele_name;
        let required = $(this);


        if (required.hasClass('form-radio')) {

            ele = required.closest('.form-ele');
            classname = 'invalid invalid-radio';
            radio_name = required.prop('name');
            if ($('[name="' + radio_name + '"]:checked').length == 0) {
                ele.addClass(classname);
                pass = 'no';
            } else {
                ele.removeClass(classname);
            }

        } else if (required.hasClass('form-input-file')) {

            ele = required;
            classname = 'invalid invalid-input';
            if (required.val() == '') {
                ele.addClass(classname);
                ele.next('label').addClass('invalid-label invalid-input');
                pass = 'no';
            } else {
                ele.removeClass(classname);
            }

        } else if (required.hasClass('form-input') || required.hasClass('form-textarea')) {

            ele = required;
            classname = 'invalid invalid-input';
            if (required.val() == '') {
                ele.addClass(classname);
                ele.next('label').addClass('invalid-label');
                pass = 'no';
            } else {
                ele.removeClass(classname);
            }

            if(required.attr('type') == 'email') {
                if(!validate_email.validate(required.val())) {
                    ele.addClass(classname);
                    ele.next('label').addClass('invalid-label');
                    pass = 'no';
                    invalid_email = 'yes';
                }
            }

        } else if (required.hasClass('form-select')) {

            ele = required.next('div').find('.form-select-value-input');
            classname = 'invalid invalid-input';
            let has_val = 'no';
            if (required.prop('multiple')) {
                if (required.find('option:selected').length > 0 && required.find('option:selected').val().length > 0) {
                    has_val = 'yes';
                }
            } else {
                if (required.val() != '' || (required.find('option:selected').length > 0 && required.val() != '')) {
                    has_val = 'yes';
                }
            }
            if (has_val == 'no') {
                ele.addClass(classname);
                ele.prev('label').addClass('invalid-label');
                pass = 'no';
            } else {
                ele.removeClass(classname);
            }

        } else {
            console.log('ERROR - form_elements.js', required.attr('id'), required.attr('class'));
        }
        // on change if ele has value remove invalid
        ele.on('change', function () {
            if (ele.val() != '') {
                ele.removeClass(classname);
                ele.prev('label').removeClass('invalid-label');
                ele.next('label').removeClass('invalid-label');
                required.prev('input').removeClass('invalid-input');
            }
        });


    });

    if(pass == 'no') {
        // focus on first invalid
        let invalid_focus = form.find('.invalid').first();

        if (invalid_focus.hasClass('file-path')) {
            invalid_focus.parent().prev('input').trigger('click');
        } else if(invalid_focus.hasClass('datepicker')) {
            setTimeout(function() {
                invalid_focus.closest('.form-ele').find('.qs-datepicker-container').removeClass('qs-hidden');
            }, 200);
        } else {
            invalid_focus.focus().trigger('click');
        }

        $('html, body').animate({
            scrollTop: invalid_focus.offset().top - 140
        }, 200);

        if(invalid_email == 'yes') {
            toastr['error']('All Required Fields Must Be Completed and Email Address is Invalid');
        } else {
            toastr['error']('All Required Fields Must Be Completed');
        }

        if(debug == true) {
            console.log('Invalid field: '+invalid_focus.prop('id'));
        }

    }

    return pass;

}

function shorten_value(input, value, multiple) {
    if (value != '') {
        // shorten value if bigger than input
        let perc = .12;
        if (multiple == false) {
            perc = .12;
        }
        let max_chars = Math.round(parseInt(input.width()) * perc);
        if (value.length > max_chars) {
            value = value.substring(0, max_chars) + '...';
        }

        input.val(value).trigger('change');
    }
}

window.select_refresh = function (ele, parent = null) {

    if(!parent) {
        if(ele.closest('.form-ele').length == 1) {
            ele.unwrap();
            ele.next('.form-select-wrapper').remove();
            ele.next('.required-div').remove();
        }
    } else {
        parent.find('.form-select').each(function() {
            if($(this).closest('.form-ele').length == 1) {
                $(this).unwrap();
                $(this).next('.form-select-wrapper').remove();
                $(this).next('.required-div').remove();
            }
        });
    }
    form_elements();

}

/* window.select_refresh = function (parent = '') {
console.log('select_refresh');
    let container = $('body');
    if(parent != '') {
        container = parent;
    }
    container.find('.form-select-value-input.caret').removeClass('caret');

    container.find('.form-ele').find('.form-select').each(function () {
        $(this).unwrap().show();
        $(this).next('.form-select-wrapper').remove();
        $(this).next('.required-div').remove();
    });

} */

function set_multiple_select_value(wrapper, input) {

    // add value to multiple select
    let selected_checks = [];
    wrapper.find('input[type="checkbox"]:checked').each(function () {
        selected_checks.push($(this).data('text'));
    });

    let value = '';
    if (selected_checks.length > 0) {
        value = selected_checks.join(', ');
    }

    if(value != '') {
        input.closest('.form-ele').find('.form-select-label').addClass('active');
    } else {
        input.closest('.form-ele').find('.form-select-label').removeClass('active');
    }

    shorten_value(input, value, true);

}

window.reset_select = function () {
    $('.form-select-li.matched').removeClass('matched').show();
    $('.form-select-matched-option').removeClass('form-select-matched-option');
}



