<script>

    $( document ).on( 'click', '.d2f-list-delete', function( event ) {

        event.preventDefault();

        let objectId    = ( this.id ).substring( 16 );
        let urlDelete   =   this.href;
        let nbC2U       = $(this).attr( "data-nb-c2u" );
        let superUser   = <?= Auth::user()->superUser() ? 'true' : 'false' ?> ;
        let message = 'Do you really want to delete this user?';
        let objectName = 'User';

        if( superUser && !$(this).hasClass( "btn-delete-c2u"  )  ) {
            message = `Are you sure you want to delete this user and its ${nbC2U} <?= config( 'ixp_fe.lang.customer.one' )  ?> links?`;

        } else {
            message = 'Do you really want to unlink this <?=  config( 'ixp_fe.lang.customer.one' )  ?> from this user ?';
            objectName = '<?=  ucfirst( config( 'ixp_fe.lang.customer.one' ) )  ?> To User';
        }

        let html = `<form id="d2f-form-delete" method="POST" action="${urlDelete}">
                                <div>${message}</div>

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
                label: `See <?= config( 'ixp_fe.lang.customer.one' )  ?> links`,
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
            title: "Delete " + objectName,
            buttons: buttons
        });
    });
</script>