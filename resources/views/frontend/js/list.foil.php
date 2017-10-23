<script>

    let d2f_read_only = <?= ( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ) ? 'false' : 'true' ?>;

    $(document).ready(function() {

        <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>

            $( 'a[id|="d2f-list-delete"]' ).on( 'click', function( event ) {

                event.preventDefault();

                let objectId = $( "#" + this.id ).attr( "data-object-id" );

                let html = `<form id="d2f-form-delete" method="POST" action="<?= action($t->controller.'@delete' ) ?>">
                                <div>Do you really want to delete this <?= $t->feParams->nameSingular ?>?</div>
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="${objectId}">
                            </form>`;

                bootbox.dialog({
                    message: html,
                    title: "Delete <?= $t->feParams->titleSingular ?>",
                    buttons: {
                        cancel: {
                            label: 'Close',
                            callback: function () {
                                $('.bootbox.modal').modal('hide');
                                return false;
                            }
                        },
                        submit: {
                            label: 'Submit',
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

            "aLengthMenu": [ [ 20, 50, 100, 500, -1 ], [ 20, 50, 100, 500, "All" ] ],

            "bAutoWidth": false,

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
                { 'bSortable': false, "bSearchable": false, "sWidth": "150px" }
            ]
        });

        tableList.show();
    });
</script>
