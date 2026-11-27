<script type="module">
    $(function () {
        $( '.btn-delete' ).click( function( e ) {
            e.preventDefault();
            let url = this.href;
            let userType = "<?= $t->ee( $t->ee( config( 'ixp_fe.lang.customer.one' ), 'html' ), 'js' ) ?>";
            let html = `<form id="form-delete" method="POST" action="${url}">
                            <div>Do you really want to purge this ${userType}?
                                <p><b>All directories, subdirectories and all files for this ${userType} will be deleted.</b></p>
                            </div>
                            <input type="hidden" name="_method" value="delete" />
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        </form>`;

            bootbox.dialog({
                message: html,
                title: `Delete ${userType}`,
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
    });
</script>