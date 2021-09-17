
<?= $t->insert( 'user/js/common' ); ?>

<?php if( Auth::user()->isSuperUser() ):?>

    <script>
        let tableList = $( '#table-list' );

        tableList.show();

        tableList.dataTable({
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            "aLengthMenu": [ [ 20, 50, 100, 500, -1 ], [ 20, 50, 100, 500, "All" ] ],
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 }
            ],
            "aaSorting": [1,'asc'],
        });
    </script>

<?php else: ?>

    <script>
        let tableList = $( '#table-list' );

        tableList.show();

        tableList.dataTable({
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            ordering: false,
            searching: false,
            paging:   false,
            info:   false,
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 }
            ],

            "aaSorting": [1,'asc'],
        });
    </script>

<?php endif; ?>

<?= $t->insert( 'user/js/delete-2fa' ); ?>

