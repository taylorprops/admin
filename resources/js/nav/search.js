$(function() {

    $(document).on('keyup', '.main-search-input', function() {
        //$('#main_nav_collapse').collapse('hide');
        main_search($(this));
    });

    $(document).on('click', '.hide-search', hide_search);

    $(document).on('click', '.search-item-link', function(e) {
        e.stopImmediatePropagation();
    });

    $(document).on('click', '.search-item', function(){
        window.location = $(this).data('href');
    });

    /* $(document).on('click', '.search-results-row .list-group-item', function() {
        window.open($(this).data('href'));
    }) */

    // let top = $('#main_nav_bar').css('height');
    // $('.main-search-results-div').css('top', top);


    let search_request = null;

    function main_search(input) {

        // cancel  previous ajax if exists
        if (search_request) {
            search_request.cancel();
        }

        // creates a new token for upcoming ajax (overwrite the previous one)
        search_request = axios.CancelToken.source();

        let value = input.val().trim();
        let container = $('.main-search-results-div');

        if(value.length > 0) {

            $('.hide-search').removeClass('hidden');

            axios.get('/search', {
                cancelToken: search_request.token,
                params: {
                    value: value
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {

                $('.main-search-results').html(response.data);
                container.show();


                $(document).on('mouseup', function (e) {
                    let search_divs = $('.search-ele');
                    if (!search_divs.is(e.target) && search_divs.has(e.target).length === 0) {
                        hide_search();
                    }
                });

                // $('#main_nav_collapse .nav-link').on('click', function(){
                //     hide_search();
                // });

            })
            .catch(function (error) {

            });

        } else {

            $('.main-search-results').html('');
            container.hide();
            $('.hide-search').addClass('hidden');

        }

    }

    function hide_search() {
        $('.main-search-results-div').hide();
        $('.main-search-input').val('');
        $('.hide-search').addClass('hidden');
    }


});
