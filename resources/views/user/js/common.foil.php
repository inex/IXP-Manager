<script>

    $( document ).on( 'click', '.d2f-list-delete', function( event ) {

        event.preventDefault();

        let objectId    = ( this.id ).substring( 16 );
        let urlDelete   =   this.href;
        let nbC2U       = $(this).attr( "data-nb-c2u" );
        let superUser   = <?= Auth::getUser()->isSuperUser() ? 'true' : 'false' ?> ;
        let message = 'Do you really want to delete this <?= $t->feParams->nameSingular ?>?';

        if( superUser && !$(this).hasClass( "btn-delete-c2u"  )  ) {
            message = `Are you sure you want to delete this user and its ${nbC2U} customer links?`;
        } else {
            message = 'Do you really want to delete this Customer from this User ?';
        }

        let html = `<form id="d2f-form-delete" method="POST" action="${urlDelete}">
                                <div>${message}</div>
                                <?php if( isset( $t->feParams->extraDeleteMessage ) ): ?><div> <?= $t->feParams->extraDeleteMessage ?> </div><?php endif;?>
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="${objectId}">
                            </form>`;


        let buttons = {
            cancel: {
                label: 'Close',
                className: 'btn-secondary',
                callback: function () {
                    $('.bootbox.modal').modal('hide');
                    return false;
                }
            },
        };

        if ( superUser && !$(this).hasClass( "btn-delete-user"  ) && !$(this).hasClass( "btn-delete-c2u"  ) ){
            buttons.seeC2U = {
                label: `See Customer links`,
                display: 'none',
                className: 'btn-warning',
                callback: function () {
                    window.location.href = '<?= url( "user/edit") ?>/' + objectId;
                }
            }
        }

        buttons.submit = {
            label: 'Delete',
            className: 'btn-danger',
            callback: function () {
                $('#d2f-form-delete').submit();
            }
        };

        bootbox.dialog({
            message: html,
            title: "Delete <?= $t->feParams->titleSingular ?>",
            buttons: buttons
        });
    });
</script>