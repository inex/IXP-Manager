<script type="module">

    $( '.btn-delete' ).click( function( event ) {
        event.preventDefault();

        let objectId    = $( this ).attr( "data-object-id" );
        let urlDelete   =   this.href;
        let nbC2U       = $( this ).attr( "data-nb-c2u" );
        let superUser   = <?= Auth::user()->isSuperUser() ? 'true' : 'false' ?> ;
        let message;
        let objectName  = 'User';

        if( superUser && !$(this).hasClass( "btn-delete-c2u"  )  ) {
            message = `<?= __c( 'Are you sure you want to delete this user and its :count :customer links?', [ 'count' => '${nbC2U}' ] ) ?>`;
        } else {
            message = '<?= __c( 'Do you really want to unlink this :customer from this user ?' ) ?>';
            objectName = '<?= __c( ':Customer To User' ) ?>';
        }

        let html = `<form id="d2f-form-delete" method="POST" action="${urlDelete}">
                      <div>${message}</div>
                      <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                      <input type="hidden" name="_method" value="delete" />
                   </form>`;

        let buttons = {
            cancel: {
                id: "btn-delete-user-cancel",
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
                id: "btn-delete-user-see-links",
                label: `<?= __c( 'See :customer links' ) ?>`,
                display: 'none',
                className: 'btn-warning',
                callback: function () {
                    window.location.href = '<?= url( "user/edit") ?>/' + objectId;
                }
            }
        }

        buttons.submit = {
            id: "btn-delete-user-submit",
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