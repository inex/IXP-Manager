<script>

    <?php if( config( 'google2fa.enabled' ) ): ?>
    
        $( '.table' ).on( 'click', '.remove-2fa', function( event ) {

            event.preventDefault();

            let url = '<?= url('') ?>/2fa/delete/' + $( "#" + this.id ).attr( "data-object-id" );

            let html = `<p>Do you really want to delete the 2FA for this user?</p>
                                <form id="d2f-form-delete" method="POST" action="` + url + `">
                                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                </form>`;

            bootbox.dialog({
                message: html,
                title: "Delete 2FA",
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

</script>