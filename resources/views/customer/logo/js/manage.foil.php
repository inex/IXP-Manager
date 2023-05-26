<script>
    $( '#btn-delete' ).click( function( e ) {
        e.preventDefault();
        let url = this.href;
        let html = `<form id="form-delete" method="POST" action="${url}">
                        <div>Do you really want to delete this logo?</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="_method" value="delete" />
                    </form>`;

        bootbox.dialog({
            title: "Delete Logo",
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
                        $('#form-delete').submit();
                    }
                },
            }
        });
    });
</script>
