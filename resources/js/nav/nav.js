$(function () {

    show_sidebar();
    $(window).on('resize', function() {
        let window_width = $(window).width();
        if(window_width > 1500) {
            show_sidebar();
        }
    });

    $('.sidebar-dropdown > a').on('click', function () {
        $('.sidebar-submenu').slideUp(200);
        if (
            $(this)
                .parent()
                .hasClass('active')
        ) {
            $('.sidebar-dropdown').removeClass('active');
            $(this)
                .parent()
                .removeClass('active');
        } else {
            $('.sidebar-dropdown').removeClass('active');
            $(this)
                .next('.sidebar-submenu')
                .slideDown(200);
            $(this)
                .parent()
                .addClass('active');
        }
    });

    $('#close-sidebar').on('click', function () {
        $('.page-wrapper').removeClass('toggled');
        //$('.top-search').show();
    });
    $('#show_sidebar').on('click', function () {
        $('.page-wrapper').addClass('toggled');
        //$('.top-search').hide();
    });


});

function show_sidebar() {

    if($(document).width() > 1600) {
        $('.page-wrapper').addClass('toggled');
        // $('.top-search').hide();
        // $('.show-sidebar').css({ 'z-index': '0' });
    } else {
        $('.page-wrapper').removeClass('toggled');
        // $('.top-search').show();
        // $('.show-sidebar').css({ 'z-index': '3' });
    }
}
