<script>
    $(document).ready( function() {
        $( '.btn-delete' ).click( function( e ) {
            e.preventDefault();
            let url = this.href;

            let html = `<form id="e2f-form-delete" method="POST" action="${url}">
                            <div>Do you really want to delete this contact?</div>
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="_method" value="delete" />
                        </form>`;
            bootbox.dialog({
                title: "Delete Contact",
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
                            $('#e2f-form-delete').submit();
                        }
                    },
                }
            });
        });
    });
</script>