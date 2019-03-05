<script>

    let pagination = true;

    <?php if($t->pp || isset( $t->data()['summary'] )): ?>
        // unless we have a single patch panel in which case we disable:
        pagination = false;
    <?php endif; ?>

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    $(document).ready(function(){

        $( '#table-ppp' ).show();

        $( '#table-ppp' ).DataTable({
            responsive : true,
            "paging":   pagination,
            columnDefs: [

                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 },
                    { responsivePriority: 3, targets: 8 },
                    { "targets": [ 0 ], "visible": false, "searchable": false,}
                ],
            "order": [[ 0, "asc" ]]
        });

    });

    $('[data-toggle="tooltip"]').tooltip();



</script>