<script>
    $( ".delete-rsf" ).click( function( e ) {
        e.preventDefault();
        let url = this.href;
        let html = `<form id="form-delete" method="POST" action="${url}">
                        <div>Do you want to delete this route server filter?</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="_method" value="delete" />
                    </form>`;

        bootbox.dialog({
            message: html,
            title: "Delete Route Server Filter",
            buttons: {
                cancel: {
                    label: 'Close',
                    className: 'btn-secondary delete-rsf-cancel',
                    callback: function () {
                        $('.bootbox.modal').modal('hide');
                        return false;
                    }
                },
                submit: {
                    label: 'Delete',
                    className: 'btn-danger delete-rsf-confirm',
                    callback: function () {
                        $('#form-delete').submit();
                    }
                },
            }
        });
    });

    $( "#submit-revert" ).on( 'click', function( e ) {
        e.preventDefault();

        let html = `<div>Are you sure you want to revert your changes?</div>`;

        bootbox.dialog({
            message: html,
            title: "Revert Changes",
            buttons: {
                cancel: {
                    label: 'Close',
                    className: 'btn-secondary submit-revert-cancel',
                    callback: function () {
                        $('.bootbox.modal').modal('hide');
                        return false;
                    }
                },
                submit: {
                    label: 'Revert',
                    className: 'btn-danger submit-revert-confirm',
                    callback: function () {
                        $( "#form-revert" ).submit();
                    }
                },
            }
        });
    });

    $( "#submit-commit" ).on( 'click', function( e ) {
        e.preventDefault();

        let html = `<div>
                Are you sure you want to commit your changes to production?<br>
                <?= config( 'ixp_fe.rs-filters.ttl' ) ?>
            </div>`;

        bootbox.dialog({
            message: html,
            title: "Commit Changes",
            buttons: {
                cancel: {
                    label: 'Close',
                    className: 'btn-secondary submit-commit-cancel',
                    callback: function () {
                        $('.bootbox.modal').modal('hide');
                        return false;
                    }
                },
                submit: {
                    label: 'Commit',
                    className: 'btn-danger submit-commit-confirm',
                    callback: function () {
                        $( "#form-commit" ).submit();
                    }
                },
            }
        });
    });

</script>