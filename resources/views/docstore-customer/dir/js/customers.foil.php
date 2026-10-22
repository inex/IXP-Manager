<script type="module">
    $(function () {
        $( '.btn-delete' ).click( function( e ) {
            e.preventDefault();
            let url = this.href;

            let html = `<form id="form-delete" method="POST" action="${url}">
                            <div><?= __c( 'Do you really want to purge this :customer?' ) ?>
                                <p><b><?= __c( 'All directories, subdirectories and all files for this :customer will be deleted.' ) ?></b></p>
                            </div>
                            <input type="hidden" name="_method" value="delete" />
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        </form>`;

            bootbox.dialog({
                message: html,
                title: `<?= __c( 'Delete :customer' ) ?>`,
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