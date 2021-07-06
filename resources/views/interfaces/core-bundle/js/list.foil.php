<script>
    $( document ).ready( function() {
        $( '#table-cb' ).dataTable( {
            responsive: true,
            iDisplayLength: 100,
            stateSave: true,
            stateDuration: DATATABLE_STATE_DURATION,
            columnDefs: [
                { targets: 0,  responsivePriority: 1 },    // visibility priority to the first column - https://datatables.net/reference/option/columns.responsivePriority
                { targets: -1, responsivePriority: 2 },    // visibility priority to the last column
                { targets: 2,  type: "string" },
            ],
        }).show();
    });
</script>