<script type="module">
    $(document).ready( function() {
        $( '.btn-password-policy' ).click( function( e ) {
            e.preventDefault();

            let policy  =   $(this).data('policy');

            let list = $('<ul>');
            policy.forEach((item) => {
                list.append($('<li>').text(item));
            });

            bootbox.dialog({
                title: "Password Policy",
                message: list,
                buttons: {
                    cancel: {
                        label: 'Close',
                        className: 'btn-primary',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');
                            return false;
                        }
                    },
                }
            });
        });
    });
</script>
