<script>
    $(document).ready(function() {
        <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly || isset( $t->feParams->readonly ) || $t->feParams->allowDeleteForReadOnly ): ?>
            $( '.btn-2f-list-delete' ).click( function( event ) {
                event.preventDefault();
                let url = this.href;
                let html = `<form id="d2f-form-delete" method="POST" action="${url}">
                                    <div>Do you really want to delete this <?= $t->feParams->nameSingular ?>?</div>
                                    <?php if( isset( $t->feParams->extraDeleteMessage ) ): ?><div> <?= $t->feParams->extraDeleteMessage ?> </div><?php endif;?>
                                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="_method" value="delete" />
                                </form>`;

                bootbox.dialog({
                    title: "Delete <?= $t->feParams->titleSingular ?>",
                    message: html,
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

        <?php endif; ?>

        let tableList = $( '#table-list' );

        tableList.dataTable({
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            "aLengthMenu": [ [ 20, 50, 100, 500, -1 ], [ 20, 50, 100, 500, "All" ] ],

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
        }).show();
    });
</script>