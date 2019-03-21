<script>

    $( document ).on( 'click', '.d2f-list-delete', function( event ) {

        event.preventDefault();

        let objectId    = $(this).attr( "data-object-id" );
        let custId      = $(this).attr( "data-cust-id" );

        let html = `<form id="d2f-form-delete" method="POST" action="<?= route($t->feParams->route_prefix.'@delete' ) ?>">
                                <div>Do you really want to delete this <?= $t->feParams->nameSingular ?>?</div>
                                <?php if( isset( $t->feParams->extraDeleteMessage ) ): ?><div> <?= $t->feParams->extraDeleteMessage ?> </div><?php endif;?>
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="${objectId}">
                                <input type="hidden" name="custid" value="${custId}">
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