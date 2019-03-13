
<?php if( !Auth::getUser()->isSuperUser() ):?>
    <?= $t->insert( 'frontend/js/common' ); ?>
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


        $( '#table-list' ).on( 'click', '.d2f-list-delete', function( event ) {

            event.preventDefault();

            let objectId = $( "#" + this.id ).attr( "data-object-id" );

            let html = `<form id="d2f-form-delete" method="POST" action="<?= route($t->feParams->route_prefix.'@delete' ) ?>">
                                <div>Do you really want to delete this <?= $t->feParams->nameSingular ?>?</div>
                                <?php if( isset( $t->feParams->extraDeleteMessage ) ): ?><div> <?= $t->feParams->extraDeleteMessage ?> </div><?php endif;?>
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="${objectId}">
                            </form>`;

            bootbox.dialog({
                message: html,
                title: "Delete <?= $t->feParams->titleSingular ?>",
                buttons: {
                    cancel: {
                        label: 'Close',
                        className: 'btn-secondary',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');
                            return false;
                        }
                    },
                    submit: {
                        label: 'Delete',
                        className: 'btn-danger',
                        callback: function () {
                            $('#d2f-form-delete').submit();
                        }
                    },
                }
            });
        });
    </script>

<?php else: ?>
    <?= $t->insert( 'frontend/js/list' ); ?>

<?php endif; ?>


