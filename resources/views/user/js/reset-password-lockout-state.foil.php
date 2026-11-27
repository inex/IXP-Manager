<script type="module">

    $( '.table' ).on( 'click', '.reset-password-lockout-state', function( event ) {

        event.preventDefault();

        let url = this.href;

        let html = $('<p>').text('Do you really want to reset the password failure count for this user?')
            .add(
                $('<form>', {
                    id: 'd2f-form-reset-password-lockout-state',
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
            title: "Reset Password Lockout",
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
                        $('#d2f-form-reset-password-lockout-state').submit();
                    }
                },
            }
        } );

    });

</script>