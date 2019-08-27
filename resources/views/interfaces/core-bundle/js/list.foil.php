<script>
    $( document ).ready( function() {
        $( "#table-cb" ).show();

        $( '#table-cb' ).DataTable( {
            responsive : true,
            "iDisplayLength": 100,
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            "columnDefs": [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 },
                { "targets": [ 2 ], "type": "string" },
                { "targets": [ 5 ], "orderData": 6 },
                { "targets": [ 6 ], "visible": false, "searchable": false }
            ],
        });

    });
</script>