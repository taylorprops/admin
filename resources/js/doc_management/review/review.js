if(document.URL.match(/document_review/)) {

    $(function() {

        $('.page-wrapper').removeClass('toggled').css({ overflow: 'hidden' });
        $('.show-sidebar').css({ 'z-index': '3' });

        $('.property-item').off('click').on('click', function() {
            $('.documents-div').children().addClass('animate__animated animate__bounceOutDown');
            $('.details-div').children().addClass('animate__animated animate__fadeOut');
            global_loading_on('', '<div class="h4 text-white">Loading Checklist Documents...</div>');
            let id = $(this).data('id');
            let type = $(this).data('type');
            get_checklist(id, type);
            get_details(id, type);
            set_property_item_active($(this));
            show_hide_next();
        });

        $('#close_checklist_button').off('click').on('click', close_checklist);

        $('.next-button').off('click').on('click', function() {
            next_property();
        });

        $('#search_properties').on('keyup', search_properties);
        $('#cancel_search_properties').on('click', cancel_search_properties);

        if($('#review_contract_id').val() > 0) {
            $('.cancellation[data-id="' + $('#review_contract_id').val() +'"]').trigger('click');
        }

        //form_elements();

        /* text-editor */

    });


    function search_properties() {
        if($(this).val() != '') {
            let v = new RegExp($(this).val(), 'i');
            $('.property-list-header, .property-item').hide();
            $('.address-div').each(function() {
                if($(this).text().match(v)) {
                    $(this).closest('.property-item').show();
                }
            });
            $('.property-list-header').each(function() {

                let header = $(this);
                let cat = header.data('cat');
                let items = $('[data-cat="' + cat + '"]');
                let show = false;

                items.each(function() {
                    if($(this).css('display') == 'block') {
                        show = true;
                    }
                });
                if(show) {
                    header.show();
                }

            });
        } else {
            $('.property-list-header, .property-item').show();
        }

    }

    function cancel_search_properties() {
        $('.property-list-header, .property-item').show();
        $('#search_properties').val('');
    }

    function set_property_item_active(ele) {
        $('.property-item').removeClass('active').addClass('list-group-item-action');
        ele.addClass('active').removeClass('list-group-item-action');
    }

    window.next_property = function() {
        let ele = $('.property-item.active');
        let index = ele.index();
        cancel = false;
        let last_index = null;
        $('.property-item').each(function() {
            if(cancel == false) {
                if($(this).index() > index) {
                    $(this).trigger('click');
                    cancel = true;
                    last_index = $(this).index();
                }
            }
        });

        show_hide_next();
    }

    function show_hide_next() {
        if($('.list-group-item.property-item.active').index() == $('.list-group-item.property-item').last().index()) {
            $('.next-button').hide();
        } else {
            $('.next-button').show();
        }
    }

    window.get_checklist = function(id, type) {

        $('#add_checklist_item_modal').modal('hide');

        axios.get('/doc_management/get_checklist', {
            params: {
                id: id,
                type: type
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {

            $('.checklist-items-div').html(response.data);
            $('.checklist-items-container').removeClass('animate__fadeOut').show();

            $('.checklist-item-name').off('click').on('click', function(e) {

                if($(this).hasClass('active')) {
                    return false;
                }

                let checklist_item_div = $(this).closest('.checklist-item-div');
                $('.checklist-item-div').removeClass('active').find('.checklist-item-name').removeClass('active text-white');
                checklist_item_div.addClass('active').find('.checklist-item-name').addClass('active text-white');
                get_documents($(this).data('checklist-item-id'), $(this).data('checklist-item-name'));

            });

            $('.notes-div').each(function() {
                get_notes($(this).data('checklist-item-id'));
            });

            let item_div = '';
            let show_notes = false;
            if($('.checklist-item-div.pending').length > 0 || $('.checklist-item-div.notes-unread').length > 0) {
                let pending_index = '1000';
                let notes_index = '1000';
                if($('.checklist-item-div.pending').length > 0) {
                    pending_index = $('.checklist-item-div.pending').eq(0).index();
                }
                if($('.checklist-item-div.notes-unread').length > 0) {
                    notes_index = $('.checklist-item-div.notes-unread').eq(0).index();
                }

                if(pending_index < notes_index) {
                    item_div = $('.checklist-item-div.pending');
                } else {
                    item_div = $('.checklist-item-div.notes-unread');
                    show_notes = true;
                }
            } else {
                item_div = $('.checklist-item-div');
            }
            item_div.eq(0).find('.checklist-item-name').trigger('click');
            setTimeout(function() {
                if(show_notes) {
                    item_div.eq(0).find('.checklist-item-notes-div').collapse('show');
                }
            }, 1000);

            scroll_checklist_item(item_div);

            $('.notes-toggle').off('click').on('click', function() {
                $(this).closest('.checklist-item-div').find('.checklist-item-name').trigger('click');
            });

            $('.modal').each(function() {
                $(this).appendTo('body');
            });

            $('.mark-required').off('click').on('click', function() {
                mark_required($(this), $(this).data('checklist-item-id'), $(this).data('required'));
            });

            $('.remove-checklist-item').off('click').on('click', function() {
                show_remove_checklist_item($(this), $(this).data('checklist-item-id'));
            });

            $('.add-checklist-item-button').off('click').on('click', show_add_checklist_item);

            $('.save-notes-button').off().on('click', save_add_notes);


            $('#property_id').val(id);
            $('#property_type').val(type);

            //form_elements();

            setTimeout(function() {
                global_loading_off();
            }, 1500);

        })
        .catch(function (error) {

        });

    }

    function scroll_checklist_item(item_div) {
        let item_id = item_div.prop('id');
        $('.checklist-items-container').scrollTop(0);
        $('.checklist-items-container').animate({
            scrollTop: $('#'+item_id).offset().top - 210
        },'fast');
    }

    function get_documents(checklist_item_id, checklist_item_name) {

        axios.get('/doc_management/get_documents', {
            params: {
                checklist_item_id: checklist_item_id,
                checklist_item_name: checklist_item_name
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {

            $('.checklist-item-docs-div').remove();
            $('.documents-div').html(response.data).show();

            // hide all notes before opening a new item
            $('.checklist-item-notes-div').collapse('hide');
            // clear docs sections
            $('.documents-list').hide();
            $('.list-group-item.checklist-item-div').find('.documents-list').html('');
            // add documents to checklist item and open it
            if($('.checklist-item-docs-div').length > 0) {
                $('.list-group-item.checklist-item-div.active').find('.documents-list').show()
                    .append('<div class="font-weight-bold text-primary border-bottom mb-2 pb-3">Documents</div>')
                    .append($('.checklist-item-docs-div'))
                    .find('.document-link').on('click', function() {
                        let id = $(this).data('document-id');
                        $('.review-image-container').scrollTop(0);
                        $('.review-image-container').animate({
                            scrollTop: $('#document_' + id).offset().top - 70
                        },'fast');
                    });
            }

            $('.accept-checklist-item-button, .reject-checklist-item-button, .undo-accepted, .undo-rejected').data('checklist-item-id', checklist_item_id);

            $('.accept-checklist-item-button').off('click').on('click', function() {
                checklist_item_review_status($(this), 'accepted', null);
            });
            $('.reject-checklist-item-button').off('click').on('click', function() {
                show_checklist_item_review_status($(this), 'rejected');
            });

            $('.undo-accepted, .undo-rejected').off('click').on('click', function() {
                checklist_item_review_status($(this), 'not_reviewed', null);
            });

            $('.next-button').off('click').on('click', function() {
                next_property();
            });

            $('.email-agent-button').off('click').on('click', function() {
                reset_email();
                show_email_agent();
            });

            let options = {
                menubar: false,
                statusbar: false,
                toolbar: false
            }
            text_editor(options);

            // new slider
            let zoom_input = $('#zoom').slider({
                formatter: function(value) {
                    return value+'%';
                }
            });

            $('#zoom').on('input change', zoom);


            $('#scroll_up').off('click').on('click', function () {
                document.querySelector('.review-image-container').scrollBy({
                    top: -500, // could be negative value
                    left: 0,
                    behavior: 'smooth'
                });
            });
            $('#scroll_down').off('click').on('click', function (evt) {
                document.querySelector('.review-image-container').scrollBy({
                    top: 500, // could be negative value
                    left: 0,
                    behavior: 'smooth'
                });
            });

            show_hide_next();


        })
        .catch(function (error) {

        });

    }

    function get_details(id, type) {

        axios.get('/doc_management/get_details', {
            params: {
                id: id,
                type: type
            }
        })
        .then(function (response) {

            $('.details-div').html(response.data);

            $('#EarnestHeldBy, #UsingHeritage').on('change', function () {

                let Contract_ID = $('#Contract_ID').val();
                let EarnestHeldBy = $('#EarnestHeldBy').val();
                let UsingHeritage = $('#UsingHeritage').val();

                let formData = new FormData();
                formData.append('Contract_ID', Contract_ID);
                formData.append('EarnestHeldBy', EarnestHeldBy);
                formData.append('UsingHeritage', UsingHeritage);
                axios.post('/doc_management/save_earnest_and_title_details', formData, axios_options)
                .then(function (response) {
                    toastr['success']('Changes Successfully Saved');
                })
                .catch(function (error) {
                    console.log(error);
                });
            });

        })
        .catch(function (error) {

        });

    }



    function close_checklist() {
        $('.checklist-items-container').addClass('animate__animated animate__fadeOut').hide();
        $('.documents-div').children().addClass('animate__animated animate__bounceOutDown');
        $('.documents-div').html('<div class="h1 text-primary w-100 text-center mt-5 pt-5"><i class="fal fa-arrow-left mr-2"></i> To Begin Select A Property</div>');
        $('.details-div').children().addClass('animate__animated animate__fadeOut');
        cancel_search_properties();
    }

    function zoom() {
        let z = $(this).val();
        $('.review-image-div').css({ width: z+'%' });
    }

}
