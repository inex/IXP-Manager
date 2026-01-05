<script>


    let tableList = $( '#ixpDataTable' );

    tableList.dataTable({
        stateSave: true,
        stateDuration : DATATABLE_STATE_DURATION,

        "aLengthMenu": [[20, 50, 100, 500, -1], [20, 50, 100, 500, "All"]],

        "bAutoWidth": false,

        "aaSorting": [[0, 'asc']],
        "iDisplayLength": 100,
    });

    $(document).ready(function() {
        tableList.show();
    });

</script>