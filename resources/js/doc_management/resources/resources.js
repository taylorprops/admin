
/* TODO
    remove delete option if in use
    hide add div on blur
*/

if (document.URL.match('/resources$')) {

    $(function() {


        $('.sortable').sortable({
            placeholder: 'bg-orange-sortable',
            handle: '.list-item-handle',
            start: function () {
                reset_resources()
            },
            stop: function (event, ui) {

                let els = $(ui.item).parent('ul').children('li');
                let resources = {
                    resource: []
                }

                els.each(function () {
                    let el, resource_id, resource_index;
                    el = $(this);
                    resource_id = el.data('resource-id');
                    resource_index = el.index();
                    resources.resource.push(
                        {
                            'resource_id': resource_id,
                            'resource_index': resource_index
                        }
                    );
                });
                let formData = new FormData();
                resources = JSON.stringify(resources);
                formData.append('data', resources);
                axios.post('/doc_management/resources/reorder', formData, axios_options)
                    .then(function (response) {
                        toastr['success']('Resource Reordered');
                    })
                    .catch(function (error) {

                    });
            }
        });
        $('.sortable').disableSelection();

        options();

    });

}


function options() {
    $('.add-resource-button').off('click').on('click', function () {
        show_add_resource($(this));
    });

    $('.edit-resource-button').off('click').on('click', function () {
        show_edit_resource($(this));
    });

    $('.delete-deactivate-resource-button').off('click').on('click', function () {
        confirm_delete_deactivate_resource($(this));
    });

    //form_elements();

}

function added_item_html(resource_id, resource_type, resource_name, resource_state, resource_color, resource_association, resource_addendums, resource_form_group_type, resource_county_abbr) {

    let resource_html = ' \
    <li class="list-group-item" data-resource-id="' + resource_id + '" data-resource-type="' + resource_type + '"> \
        <div class="resource-div-details"> \
    ';
    resource_html += ' \
            <i class="fas fa-sort mr-2 mt-1 list-item-handle float-left"></i> \
            <span class="edit-resource-title list-item-handle float-left"> \
    ';
    if (resource_color != '') {
        resource_html += ' \
            <div class="resource-color-square mr-2 float-left" style="background-color: ' + resource_color + '"></div> \
        ';
    }

    let resource_name_display = resource_name;
    if (resource_state != '') {
        resource_name_display = resource_state + ' | ' + resource_name;
    }
    if (resource_county_abbr!= '') {
        resource_name_display = resource_name_display + ' | ' + resource_county_abbr;
    }
    resource_html += ' \
            ' + resource_name_display + '</span > \
            <a href="javascript: void(0)" class="delete-deactivate-resource-button text-danger float-right ml-3" data-resource-id="' + resource_id + '" data-resource-name="' + resource_name + '" data-action="delete"><i class="fal fa-ban fa-lg"></i></a> \
            <a href="javascript: void(0)" class="edit-resource-button text-primary float-right" data-resource-type="' + resource_type + '"><i class="fad fa-edit fa-lg"></i></a> \
        </div> \
        <div class="resource-div-edit container-fluid"> \
            <form> \
                <div class="row py-3"> \
                    <div class="col-4 px-1"> \
                        <input type="text" class="custom-form-element form-input edit-resource-input" value="' + resource_name + '" data-default-value="' + resource_name + '" data-label="Resource Name"> \
                    </div> \
    ';
    if (resource_state != '') {
        resource_html += ' \
                    <div class="col px-1"> \
                        <select class="custom-form-element form-select edit-resource-state form-select-no-cancel form-select-no-search required" data-label="State" data-default-value="' + resource_state + '"> \
                            <option value=""></option> \
        ';
        let states = active_states.split(',');
        $.each(states, function (index, state) {
            let selected = '';
            if (resource_state == state) {
                selected = 'selected';
            }
            resource_html += '<option value="' + state + '" ' +selected + '>' + state + '</option>';
        });
        resource_html += ' \
                        </select> \
                    </div> \
        ';
    }
    if (resource_association != '') {
        resource_html += ' \
                    <div class="col px-1"> \
                        <select class="custom-form-element form-select edit-resource-association form-select-no-cancel form-select-no-search required" data-label="Association" data-default-value="' + resource_association + '"> \
                            <option value=""></option> \
        ';
        let selected = '';
        if (resource_association == 'yes') {
            selected = 'selected';
        }
        resource_html += '<option value="yes" ' + selected + '>Yes</option>';
        selected = '';
        if (resource_association == 'no') {
            selected = 'selected';
        }
        resource_html += '<option value="no" ' + selected + '>No</option>';
        resource_html += ' \
                        </select> \
                    </div> \
        ';
    }
    if (resource_addendums != '') {
        resource_html += ' \
                    <div class="col px-1"> \
                        <select class="custom-form-element form-select edit-resource-addendums form-select-no-cancel form-select-no-search required" data-label="Addenda" data-default-value="' + resource_addendums + '"> \
                            <option value=""></option> \
        ';
        let selected = '';
        if (resource_addendums == 'yes') {
            selected = 'selected';
        }
        resource_html += '<option value="yes" ' + selected + '>Yes</option>';
        selected = '';
        if (resource_addendums == 'no') {
            selected = 'selected';
        }
        resource_html += '<option value="no" ' + selected + '>No</option>';
        resource_html += ' \
                        </select> \
                    </div> \
        ';
    }
    if (resource_form_group_type != '') {
        resource_html += ' \
                    <div class="col px-1"> \
                        <select class="custom-form-element form-select edit-resource-form-group-type form-select-no-cancel form-select-no-search required" data-label="Form Type" data-default-value="' + resource_form_group_type + '"> \
                            <option value=""></option> \
        ';
        let selected = '';
        if (resource_form_group_type == 'listing') {
            selected = 'selected';
        }
        resource_html += '<option value="listing" ' + selected + '>Listing</option>';
        selected = '';
        if (resource_form_group_type == 'contract') {
            selected = 'selected';
        }
        resource_html += '<option value="contract" ' + selected + '>Listing</option>';
        selected = '';
        if (resource_form_group_type == 'both') {
            selected = 'selected';
        }
        resource_html += '<option value="both" ' + selected + '>Both</option>';
        resource_html += ' \
                        </select> \
                    </div> \
        ';
    }
    if (resource_color != '') {
        resource_html += ' \
                    <div class="col px-1"> \
                        <input type="color" class="custom-form-element form-input-color   edit-resource-color colorpicker" value="' + resource_color + '" data-default-value="' + resource_color + '" data-label="Tag Color"> \
                    </div> \
        ';
    }
    if (resource_county_abbr != '') {
        resource_html += ' \
                    <div class="col px-1"> \
                        <input type="text" class="form-input" value="' + resource_county_abbr + '" data-default-value="' + resource_county_abbr + '" data-label="County Abbr"> \
                    </div> \
        ';
    }
    if (resource_account_number != '') {
        resource_html += ' \
                    <div class="col px-1"> \
                        <input type="text" class="form-input" value="' + resource_account_number + '" data-default-value="' + resource_account_number + '" data-label="Account #"> \
                    </div> \
        ';
    }
    resource_html += ' \
                    <div class="col-1 pl-2"> \
                        <a href="javascript: void(0)" class="save-edit-resource-button" data-resource-id="' + resource_id + '" data-resource-type="' + resource_type + '"><i class="fad fa-save text-primary fa-2x mt-3"></i></a> \
                    </div> \
                    <div class="col-1 px-1"> \
                        <a href="javascript: void(0)" class="close-edit-resource-button"><i class="fal fa-times text-danger fa-2x mt-3"></i></a> \
                    </div> \
                </div> \
            </form> \
        </div> \
    </li> \
    ';

    return resource_html;
}

function show_add_resource(ele) {
    let resource_type = ele.data('resource-type');
    let resource_div = ele.closest('.resource-div');
    let add_resource_div = resource_div.find('.add-resource-div');

    let add_button = resource_div.find('.add-resource-button');
    let cancel_button = resource_div.find('.cancel-add-resource-button');
    let save_button = resource_div.find('.add-resource-save-button');

    let resource_input = add_resource_div.find('.add-resource-input');
    let resource_state_select = '';
    let resource_state = '';
    let resource_input_color = '';
    let resource_color = '';
    let resource_association_select = '';
    let resource_association = '';
    let resource_addendums_select = '';
    let resource_addendums = '';
    let resource_form_group_type_select = '';
    let resource_form_group_type = '';
    let resource_county_abbr_input = '';
    let resource_county_abbr = '';
    let resource_account_number_input = '';
    let resource_account_number = '';

    if (add_resource_div.find('.add-resource-state').length > 0) {
        resource_state_select = add_resource_div.find('.add-resource-state');
    }
    if (add_resource_div.find('.add-resource-color').length > 0) {
        resource_input_color = add_resource_div.find('.add-resource-color');
    }
    if (add_resource_div.find('.add-resource-association').length > 0) {
        resource_association_select = add_resource_div.find('.add-resource-association');
    }
    if (add_resource_div.find('.add-resource-addendums').length > 0) {
        resource_addendums_select = add_resource_div.find('.add-resource-addendums');
    }
    if (add_resource_div.find('.add-resource-form-group-type').length > 0) {
        resource_form_group_type_select = add_resource_div.find('.add-resource-form-group-type');
    }
    if (add_resource_div.find('.add-resource-county-abbr').length > 0) {
        resource_county_abbr_input = add_resource_div.find('.add-resource-county-abbr');
    }
    if (add_resource_div.find('.add-resource-account-number').length > 0) {
        resource_account_number_input = add_resource_div.find('.add-resource-account-number');
    }

    reset_add_resource_div(add_resource_div, add_button, resource_input, resource_state_select, cancel_button);

    let appendTo = resource_div.find('.list-group');

    add_button.hide();
    cancel_button.show().on('click', function () {
        add_resource_div.slideUp('fast');
        add_button.show();
        resource_input.val('');
        if (resource_state_select) {
            resource_state_select.val('');
        }
        if (resource_input_color) {
            resource_input_color.val(resource_input_color.data('default-value'));
        }
        if (resource_association_select) {
            resource_association_select.val('');
        }
        if (resource_addendums_select) {
            resource_addendums_select.val('');
        }
        if (resource_form_group_type_select) {
            resource_form_group_type_select.val('');
        }
        if (resource_county_abbr_input) {
            resource_county_abbr_input.val('');
        }
        if (resource_account_number_input) {
            resource_account_number_input.val('');
        }
        cancel_button.hide();
        reset_add_resource_div(add_resource_div, add_button, resource_input, resource_state_select, cancel_button);
    });

    $(document).on('mousedown', function (e) {
        if (!$(e.target).is('.add-resource-div *')) {
            reset_add_resource_div(add_resource_div, add_button, resource_input, resource_state_select, cancel_button);
        }
    });

    add_resource_div.slideDown('fast');
    resource_input.focus();

    save_button.off('click').on('click', function () {

        let validate = validate_form(add_resource_div.find('form'));

        if (validate == 'yes') {

            let formData = new FormData();
            let resource_name = resource_input.val();
            if (resource_state_select) {
                resource_state = resource_state_select.val();
            }
            if (resource_input_color) {
                resource_color = resource_input_color.val();
            }
            if (resource_association_select) {
                resource_association = resource_association_select.val();
            }
            if (resource_addendums_select) {
                resource_addendums = resource_addendums_select.val();
            }
            if (resource_form_group_type_select) {
                resource_form_group_type = resource_form_group_type_select.val();
            }
            if (resource_county_abbr_input) {
                resource_county_abbr = resource_county_abbr_input.val();
            }
            if (resource_account_number_input) {
                resource_account_number = resource_account_number_input.val();
            }

            formData.append('resource_type', resource_type);
            formData.append('resource_name', resource_name);
            formData.append('resource_state', resource_state);
            formData.append('resource_color', resource_color);
            formData.append('resource_association', resource_association);
            formData.append('resource_addendums', resource_addendums);
            formData.append('resource_form_group_type', resource_form_group_type);
            formData.append('resource_county_abbr', resource_county_abbr);
            formData.append('resource_account_number', resource_account_number);

            axios.post('/doc_management/resources/add', formData, axios_options)
                .then(function (response) {
                    cancel_button.trigger('click');
                    toastr['success']('Resource Added Successfully');
                    let resource_id = response.data;
                    let new_resource_html = added_item_html(resource_id, resource_type, resource_name, resource_state, resource_color, resource_association, resource_addendums, resource_form_group_type, resource_county_abbr, resource_account_number);
                    $(new_resource_html).appendTo(appendTo);
                    //options();

                })
                .catch(function (error) {

                });
        }
    });


}

function reset_add_resource_div(add_resource_div, add_button, resource_input, resource_state_select, cancel_button) {
    add_resource_div.slideUp('fast');
    add_button.show();
    resource_input.val('');
    if (resource_state_select) {
        resource_state_select.val('');
    }
    cancel_button.hide();
    add_resource_div.find('input, select').each(function () {
        $(this).val($(this).data('default-value'));
    });
    //select_refresh();
}

function show_edit_resource(ele) {

    reset_resources();
    let resource_type = ele.data('resource-type');
    let list_group_item = ele.closest('.resource-div-details');
    let resource_div = list_group_item.next('.resource-div-edit');
    let resource_input = resource_div.find('.edit-resource-input');
    let resource_state_select = '';
    if (resource_div.find('.edit-resource-state').length > 0) {
        resource_state_select = resource_div.find('.edit-resource-state');
    }
    let resource_input_color = '';
    if (resource_div.find('.edit-resource-color').length > 0) {
        resource_input_color = resource_div.find('.edit-resource-color');
    }
    let resource_association_select = '';
    if (resource_div.find('.edit-resource-association').length > 0) {
        resource_association_select = resource_div.find('.edit-resource-association');
    }
    let resource_addendums_select = '';
    if (resource_div.find('.edit-resource-addendums').length > 0) {
        resource_addendums_select = resource_div.find('.edit-resource-addendums');
    }
    let resource_form_group_type_select = '';
    if (resource_div.find('.edit-resource-form-group-type').length > 0) {
        resource_form_group_type_select = resource_div.find('.edit-resource-form-group-type');
    }
    let resource_county_abbr_input = '';
    if (resource_div.find('.edit-resource-county-abbr').length > 0) {
        resource_county_abbr_input = resource_div.find('.edit-resource-county-abbr');
    }
    let resource_account_number_input = '';
    if (resource_div.find('.edit-resource-account-number').length > 0) {
        resource_account_number_input = resource_div.find('.edit-resource-account-number');
    }


    let save = resource_div.find('.save-edit-resource-button');
    let close = resource_div.find('.close-edit-resource-button');

    list_group_item.hide();
    resource_div.show();

    save.off('click').on('click', function () {

        save_edit_resource($(this), resource_input, resource_state_select, resource_input_color, resource_association_select, resource_addendums_select, resource_form_group_type_select, resource_county_abbr_input, resource_account_number_input, list_group_item, resource_div, resource_type);

    });

    close.on('click', function () {

        reset_edit_resource_div(list_group_item, resource_div, resource_input, resource_state_select, resource_association_select, resource_addendums_select, resource_form_group_type_select, resource_input_color, resource_county_abbr_input, resource_account_number_input);

    });

}

function reset_edit_resource_div(list_group_item, resource_div, resource_input, resource_state_select, resource_association_select, resource_addendums_select, resource_form_group_type_select, resource_input_color, resource_county_abbr_input) {
    list_group_item.show();
    resource_div.hide();
    resource_input.val(resource_input.data('default-value'));
    if (resource_state_select) {
        resource_state_select.val(resource_state_select.data('default-value'));
    }
    if (resource_input_color) {
        resource_input_color.val(resource_input_color.data('default-value'));
    }
    if (resource_association_select) {
        resource_association_select.val(resource_association_select.data('default-value'));
    }
    if (resource_addendums_select) {
        resource_addendums_select.val(resource_addendums_select.data('default-value'));
    }
    //select_refresh();
}

function save_edit_resource(ele, resource_input, resource_state_select, resource_input_color, resource_association_select, resource_addendums_select, resource_form_group_type_select, resource_county_abbr_input, resource_account_number_input, list_group_item, resource_div, resource_type) {

    let resource_id = ele.data('resource-id');
    let resource_title = list_group_item.find('.edit-resource-title');
    let resource_name = resource_input.val();
    let resource_state = '';
    if (resource_state_select) {
        resource_state = resource_state_select.val();
    }
    let resource_color = '';
    let resource_color_square;
    if (resource_input_color) {
        resource_color = resource_input_color.val();
        resource_color_square = ele.closest('.list-group-item').find('.resource-color-square');
    }
    let resource_association = '';
    if (resource_association_select) {
        resource_association = resource_association_select.val();
    }
    let resource_addendums = '';
    if (resource_addendums_select) {
        resource_addendums = resource_addendums_select.val();
    }
    let resource_form_group_type = '';
    if (resource_form_group_type_select) {
        resource_form_group_type = resource_form_group_type_select.val();
    }
    let resource_county_abbr = '';
    if (resource_county_abbr_input) {
        resource_county_abbr = resource_county_abbr_input.val();
    }
    let resource_account_number = '';
    if (resource_account_number_input) {
        resource_account_number = resource_account_number_input.val();
    }

    let resource_title_html = '';

    let validate = validate_form(resource_div.find('form'));

    if (validate == 'yes') {

        let formData = new FormData();
        formData.append('resource_id', resource_id);
        formData.append('resource_name', resource_name);
        formData.append('resource_state', resource_state);
        formData.append('resource_color', resource_color);
        formData.append('resource_association', resource_association);
        formData.append('resource_addendums', resource_addendums);
        formData.append('resource_form_group_type', resource_form_group_type);
        formData.append('resource_county_abbr', resource_county_abbr);
        formData.append('resource_account_number', resource_account_number);

        axios.post('/doc_management/resources/edit', formData, axios_options)
            .then(function (response) {

                list_group_item.show();
                resource_div.hide();

                resource_input.data('default-value', resource_name);
                if (resource_state_select) {
                    resource_state_select.data('default-value', resource_state);
                    resource_title_html = resource_state + ' | ';
                }
                resource_title_html += resource_name;
                if (resource_county_abbr_input) {
                    resource_county_abbr_input.data('default-value', resource_county_abbr);
                    resource_title_html += ' | ' + resource_county_abbr;
                }
                resource_title.html(resource_title_html);

                if (resource_color) {
                    resource_color_square.css('background-color', resource_color);
                }
                if (resource_association_select) {
                    resource_association_select.data('default-value', resource_association);
                }
                if (resource_addendums_select) {
                    resource_addendums_select.data('default-value', resource_addendums);
                }

                toastr['success']('Resource Edited Successfully');
            })
            .catch(function (error) {

            });

    }

}

function reset_resources() {
    $('.resource-div-details').show();
    $('.resource-div-edit').hide();
    $('.resource-input').each(function () {
        $(this).val($(this).data('default-value'));
    });
}

function confirm_delete_deactivate_resource(ele) {
    let action = $(ele).data('action');
    $('#confirm_delete_deactivate_resource_modal').modal();

    let resource_name = ele.data('resource-name');
    $('.delete-deactivate-resource-file-name').text(resource_name);

    let confirm_text, title_text;
    if(action == 'delete') {
        confirm_text = 'Are you sure you want to permanently delete this resource?';
        title_text = 'Delete Resource';
    }
    $('.confirm-delete-deactivate-resource-text').text(confirm_text);
    $('#confirm_delete_deactivate_resource_modal_title').text(title_text);

    $('#confirm_delete_deactivate_resource').on('click', function () {
        delete_deactivate_resource_resource(ele, action)
    });
}

function delete_deactivate_resource_resource(ele, action) {

    let resource_id = ele.data('resource-id');
    let formData = new FormData();
    formData.append('resource_id', resource_id);
    formData.append('action', action);

    axios.post('/doc_management/resources/delete_deactivate', formData, axios_options)
        .then(function (response) {
            $(ele).closest('.list-group-item').remove();
            toastr['success']('Resource ' + action + 'd successfully');
            $('#confirm_delete_deactivate_resource_modal').modal('hide');
        })
        .catch(function (error) {

        });
}


