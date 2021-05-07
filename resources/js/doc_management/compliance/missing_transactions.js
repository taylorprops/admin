alert('test');

if(document.URL.match(/missing_listings/) || document.URL.match(/missing_contracts/)) {

    $(function() {


    data_table(25, $('#listings_table'), [2, 'desc'], [3], [], true, true, true, true, true, false);

    });

}
