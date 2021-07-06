<script>
    $(document).ready( function() {
        $( '.btn-delete-usr' ).click( function( e ) {
            e.preventDefault();
            let urlDelete   =   this.href;

            let html = `<form id="d2f-form-delete" method="POST" action="${urlDelete}">
                            <div>Do you really want to delete this user?</div>
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="_method" value="delete" />
                        </form>`;

            bootbox.dialog({
                title: "Delete User",
                message: html,
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
    });
</script>
<?= $t->insert( 'user/js/delete-2fa' ); ?>