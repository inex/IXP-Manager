<script>
    $('.table-responsive-ixp-with-header').show();

    $('.table-responsive-ixp-with-header').DataTable( {
        stateSave: true,
        stateDuration : DATATABLE_STATE_DURATION,
        responsive: true,
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: -1 }
        ],
        fnDrawCallback: function( oSettings ) {
            $( '.btn-reset-ts' ).click( resetRouterTimestamp );
            $( '.btn-resume' ).click( resumeRouterUpdates );
            $( '.btn-pause' ).click( pauseRouterUpdates );
            $( '.btn-delete' ).click( deleteRouter );
        },
    } );



    function deleteRouter( e ) {
        e.preventDefault();
        let url = this.href;

        let html = `<form id="form-delete" method="POST" action="${url}">
                        <div>Do you want to delete this router?</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="_method" value="delete" />
                    </form>`;

        bootbox.dialog({
            message: html,
            title: "Delete Router",
            buttons: {
                cancel: {
                    label: 'Close',
                    className: 'btn-secondary',
                    callback: function () {
                        $( '.bootbox.modal' ).modal('hide');
                        return false;
                    }
                },
                submit: {
                    label: 'Delete',
                    className: 'btn-danger',
                    callback: function () {
                        $( '#form-delete' ).submit();
                    }
                },
            }
        });
    }


    function pauseRouterUpdates( e ) {
        e.preventDefault();
        let url = this.href;

        let html = `<form id="form-pause" method="POST" action="${url}">
                        <div>Do you want to pause this router from automatic updates?</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    </form>`;

        bootbox.dialog({
            message: html,
            title: "Pause Router",
            buttons: {
                cancel: {
                    label: 'Close',
                    className: 'btn-secondary',
                    callback: function () {
                        $( '.bootbox.modal' ).modal('hide');
                        return false;
                    }
                },
                submit: {
                    label: 'Pause',
                    className: 'btn-danger',
                    callback: function () {
                        $( '#form-pause' ).submit();
                    }
                },
            }
        });
    }



    function resumeRouterUpdates( e ) {
        e.preventDefault();
        let url = this.href;

        let html = `<form id="form-resume" method="POST" action="${url}">
                        <div>Do you want to resume automatic updates for this router?</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    </form>`;

        bootbox.dialog({
            message: html,
            title: "Resume Router",
            buttons: {
                cancel: {
                    label: 'Close',
                    className: 'btn-secondary',
                    callback: function () {
                        $( '.bootbox.modal' ).modal('hide');
                        return false;
                    }
                },
                submit: {
                    label: 'Resume',
                    className: 'btn-danger',
                    callback: function () {
                        $( '#form-resume' ).submit();
                    }
                },
            }
        });
    }



    function resetRouterTimestamp( e ) {
        e.preventDefault();
        let url = this.href;

        let html = `<form id="form-reset-ts" method="POST" action="${url}">
                        <div>Do you want to reset the update timestamps for this router?</div>
                        <br>
                        <div>This is typically something you may need to do when an update script fails or you have corrected an issue.</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    </form>`;

        bootbox.dialog({
            message: html,
            title: "Reset Router Update Timestamps",
            buttons: {
                cancel: {
                    label: 'Close',
                    className: 'btn-secondary',
                    callback: function () {
                        $( '.bootbox.modal' ).modal('hide');
                        return false;
                    }
                },
                submit: {
                    label: 'Reset',
                    className: 'btn-danger',
                    callback: function () {
                        $( '#form-reset-ts' ).submit();
                    }
                },
            }
        });
    }
</script>