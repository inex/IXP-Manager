<?= $t->insert( 'interfaces/virtual/js/interface' ); ?>
<script>
    $(document).ready( function() {
        $( '#table' ).dataTable( {
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive : true,
            "iDisplayLength": 100,
            "columnDefs": [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 },
                { "targets": [ 5 ], "orderData": 6 },
                { "targets": [ 6 ], "visible": false, "searchable": false },
                { "targets": [ 7 ], "orderData": 8 },
                { "targets": [ 8 ], "visible": false, "searchable": false }
            ],
        }).show();
    });

    /**
     * on click even allow to delete a Virtual Interface
     */
    $( '.btn-delete' ).click( function(e){
        e.preventDefault();
        deletePopup( $( this ), 'pi');
    });
</script>