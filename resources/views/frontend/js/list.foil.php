<script>
    $(document).ready(function() {

        $( 'a[id|="list-delete"]' ).on( 'click', function( event ){

            event.preventDefault();

            let objectId = $( "#" + this.id).attr( "data-related" );

            let html = `<form id='form-delete' method='POST' action='<?= action($t->controller.'@delete' ) ?>' >
                            <div>Do you really want to delete this <?= $t->data[ 'feParams' ]->titleSingular ?></div>
                            <input type='hidden' name="_token" value="<?= csrf_token() ?>">
                            <input type='hidden' name="id" value="${objectId}">
                        </form>`;

            let dialog = bootbox.dialog({
                message: html,
                title: "Delete action",
                onEscape: function() {
                    location.reload();
                },
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> Close',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');

                            return false;
                        }
                    },
                    submit: {
                        label: '<i class="fa fa-times"></i> Submit',
                        callback: function () {
                            $('#form-delete').submit();
                        }
                    },
                }
            });


        });

        $( '#table-list' ).dataTable({
            "aLengthMenu": [ [ 10, 25, 50, 100, 500, -1 ], [ 10, 25, 50, 100, 500, "All" ] ],
            "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
            "bAutoWidth": false,
            <?php $count=0 ?>
            <?php if( isset( $t->data[ 'feParams']->listOrderBy ) ): ?>
            <?php foreach( $t->data[ 'feParams']->listColumns as $col => $cconf ): ?>
            <?php if( !is_array( $cconf ) || !isset( $cconf[ 'display' ] ) || $cconf[ 'display' ] ): ?>
            <?php if( isset( $t->data[ 'feParams']->listOrderBy ) && $t->data[ 'feParams']->listOrderBy == $col ): ?>
            'aaSorting': [[ <?= $count ?> , <?php if( isset( $t->data[ 'feParams']->listOrderByDir ) && $t->data[ 'feParams']->listOrderByDir =="DESC" ): ?> 'desc'<?php else: ?> 'asc' <?php endif;?> ]],
            <?php endif; ?>
            <?php $count = $count + 1 ?>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php endif; ?>
            'aoColumns': [
                <?php foreach( $t->data[ 'feParams']->listColumns as $col => $cconf ): ?>
                <?php if( !is_array( $cconf ) || !isset( $cconf[ 'display' ] ) || $cconf[ 'display' ] ): ?>
                null ,
                <?php endif; ?>
                <?php endforeach; ?>
                { 'bSortable': false, "bSearchable": false, "sWidth": "150px" }
            ]
        });

        $( '#table-list' ).show();

    });
</script>