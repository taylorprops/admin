$(function ($) {

    show_sidebar();
    $(window).on('resize', show_sidebar);

    $(".sidebar-dropdown > a").on('click', function () {
        $(".sidebar-submenu").slideUp(200);
        if (
            $(this)
                .parent()
                .hasClass("active")
        ) {
            $(".sidebar-dropdown").removeClass("active");
            $(this)
                .parent()
                .removeClass("active");
        } else {
            $(".sidebar-dropdown").removeClass("active");
            $(this)
                .next(".sidebar-submenu")
                .slideDown(200);
            $(this)
                .parent()
                .addClass("active");
        }
    });

    $("#close-sidebar").on('click', function () {
        $(".page-wrapper").removeClass("toggled");
    });
    $("#show-sidebar").on('click', function () {
        $(".page-wrapper").addClass("toggled");
    });

    function show_sidebar() {

        if($(document).width() > 1200) {
            $(".page-wrapper").addClass("toggled");
        } else {
            $(".page-wrapper").removeClass("toggled");
        }
    }


});











/* (function ($) {
    var defaults = {
        sm: 540,
        md: 720,
        lg: 960,
        xl: 1140,
        navbar_expand: 'lg',
        animation: true,
        animateIn: 'show',
    };
    $.fn.bootnavbar = function (options) {

        var screen_width = $(document).width();
        settings = $.extend(defaults, options);

        if (screen_width >= settings.lg) {

            $(this).find('.dropdown').each(function () {
                $(this).on('mouseenter', function() {
                    $(this).addClass('show');
                    $(this).find('.dropdown-menu').first().addClass('show');
                    if (settings.animation) {
                        $(this).find('.dropdown-menu').first().addClass('animate__animated animate__' + settings.animateIn);
                    }
                });
                $(this).on('mouseleave', function() {
                    $(this).removeClass('show');
                    $(this).find('.dropdown-menu').first().removeClass('show');
                });
            });

        }

        // $('.dropdown-input').on('click', function() {
        //     console.log('clicked');
        //     $(this).addClass('show');
        //     $(this).find('.dropdown-menu').first().addClass('show');
        //     if (settings.animation) {
        //         $(this).find('.dropdown-menu').first().addClass('animate__animated animate__' + settings.animateIn);
        //     }
        // });

        $('.dropdown-menu a.dropdown-toggle').on('click', function (e) {
            // if (!$(this).next().hasClass('show')) {
            //     $(this).parents('.dropdown-menu').first().find('.show').removeClass('show');
            // }
            // var $subMenu = $(this).next('.dropdown-menu');
            // $subMenu.toggleClass('show');

            // $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
            //     $('.dropdown-submenu .show').removeClass('show');
            // });

            return false;
        });
    };
})(jQuery); */
