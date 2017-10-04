<script>
    $(document).ready(function() {

        $( 'a[id|="list-delete"]' ).on( 'click', function( event ){

            event.preventDefault();
            url = $(this).attr("href");

            bootbox.confirm({
                message: `Do you really want to delete this object ?` ,
                buttons: {
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-primary'
                    },
                    confirm: {
                        label: 'Delete',
                        className: 'btn-danger'
                    }
                },
                callback: function ( result ) {
                    if( result) {
                        document.location.href = url;
                    }
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