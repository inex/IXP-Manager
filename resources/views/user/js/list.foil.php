
<?php if( !Auth::getUser()->isSuperUser() ):?>
    <?= $t->insert( 'user/js/common' ); ?>

    <script>
        let tableList = $( '#table-list' );

        tableList.show();

        tableList.dataTable({

            responsive: true,
            responsive: true,
            ordering: false,
            searching: false,
            paging:   false,
            info:   false,
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 }
            ],


            <?php
            $count = 0;
            if( isset( $t->feParams->listOrderBy ) ) {
            foreach( $t->feParams->listColumns as $col => $cconf ) {
            if( !is_array( $cconf ) || !isset( $cconf[ 'display' ] ) || $cconf[ 'display' ] ) {
            if( isset( $t->feParams->listOrderBy ) && $t->feParams->listOrderBy == $col ) { ?>
            "aaSorting": [[ <?= $count ?>, "<?= isset( $t->feParams->listOrderByDir ) && $t->feParams->listOrderByDir == "DESC" ? 'desc' : 'asc' ?>" ]], <?php
            } // endif
            } // endif
            $count++;
            } //endforeach
            } // endif
            ?>

            "aoColumns": [
                <?php
                foreach( $t->feParams->listColumns as $col => $cconf ) {
                    if( !is_array( $cconf ) || !isset( $cconf[ 'display' ] ) || $cconf[ 'display' ] ) {
                        echo "null, ";
                    }
                }
                ?>
                <?php if( !isset( $t->feParams->hideactioncolumn ) || !$t->feParams->hideactioncolumn ): ?>
                { 'bSortable': false, "bSearchable": false, "sWidth": "150px" }
                <?php endif; ?>
            ]
        });

    </script>

<?php else: ?>
    <?= $t->insert( 'frontend/js/list' ); ?>

<?php endif; ?>


