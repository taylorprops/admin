if (document.URL.match(/transaction_details/)) {

    window.contracts_init = function() {

        $('.contract-div').on('mouseenter', function () {
            $(this).addClass('z-depth-3').removeClass('shadow');
        });
        $('.contract-div').on('mouseleave', function () {
            $(this).removeClass('z-depth-3').addClass('shadow');
        });

    }
}
