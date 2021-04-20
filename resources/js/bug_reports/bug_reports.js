if(document.URL.match(/bug_reports/)) {

    $(function() {

        data_table(10, $('#bug_report_table'), [3, 'desc'], [0], [], true, true, true, true, true);

    });

}
