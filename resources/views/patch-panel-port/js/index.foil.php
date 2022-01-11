<script>

    let pagination = true;

    <?php if( $t->pp || isset( $t->data()['summary'] )): ?>
        // unless we have a single patch panel in which case we disable:
        pagination = false;
    <?php endif; ?>

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    $(document).ready(function(){
        $( '#table-ppp' ).dataTable({
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            // skip ordering per https://github.com/inex/IXP-Manager/issues/639
            "stateSaveParams": function (settings, data) {
                data.order = undefined;
            },
            responsive : true,
            "paging":   pagination,
            columnDefs: [

                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 },
                    { responsivePriority: 3, targets: 8 },
                    { "targets": [ 0 ], "visible": false, "searchable": false,}
                ],
            "order": [[ 0, "asc" ]]
        }).show();

    });

    $( '[data-toggle="tooltip"]' ).tooltip();
</script>