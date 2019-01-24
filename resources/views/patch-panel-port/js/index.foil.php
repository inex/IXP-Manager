<script>

    let pagination = true;

    <?php if($t->pp || isset( $t->data()['summary'] )): ?>
        // unless we have a single patch panel in which case we disable:
        pagination = false;
    <?php endif; ?>

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    $(document).ready(function(){
        loadDataTable( 'ppp' );
        $( '#area-ppp' ).show();

    });

    $('[data-toggle="tooltip"]').tooltip();

    //////////////////////////////////////////////////////////////////////////////////////
    // functions:

    /**
     * initialise the datatable table
     */
    function loadDataTable( tableId ){
        table = $( '#table-' + tableId ).DataTable({
            "paging":   pagination,
            "autoWidth": false,
            "columnDefs": [{
                "targets": [ 0 ],
                "visible": false,
                "searchable": false,
            }],
            "order": [[ 0, "asc" ]]
        });
    }

</script>