<script type="module">

    <?php if( config( 'google2fa.enabled' ) ): ?>

    $( '.table' ).on( 'click', '.reset-2fa-lockout-state', function( event ) {

        event.preventDefault();

        let url = this.href;

        let html = $('<p>').text('Do you really want to reset the 2FA failure count for this user?')
            .add(
                $('<form>', {
                    id: 'd2f-form-reset-2fa-lockout-state',
                    method: 'POST',
                    action: url
                }).append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: '<?= csrf_token() ?>'
                }))
            );

        bootbox.dialog( {
            message: html,
            title: "Reset 2FA Lockout State",
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
                    label: 'Reset',
                    className: 'btn-danger',
                    callback: function () {
                        $('#d2f-form-reset-2fa-lockout-state').submit();
                    }
                },
            }
        } );

    });

    <?php endif; ?>

</script>