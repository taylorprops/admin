$(function () {

    show_sidebar();
    $(window).on('resize', show_sidebar);

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
    });
    $('#show-sidebar').on('click', function () {
        $('.page-wrapper').addClass('toggled');
    });


});

function show_sidebar() {

    if($(document).width() > 1600) {
        $('.page-wrapper').addClass('toggled');
    } else {
        $('.page-wrapper').removeClass('toggled');
    }
}
