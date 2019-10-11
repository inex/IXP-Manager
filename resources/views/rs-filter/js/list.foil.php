<script>

    $( "a[id|='delete-rsf']" ).on( 'click', function( e ) {
        e.preventDefault();
        let rsfid = ( this.id ).substring( 11 );

        let html = `<form id="form-delete" method="POST" action="<?= route('rs-filter@delete' ) ?>">
                        <div>Do you want to delete this route server filter ?</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="id" value="${rsfid}">
                    </form>`;

        bootbox.dialog({
            message: html,
            title: "Delete Router Server Filter",
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