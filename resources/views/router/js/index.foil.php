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
    } );

    $( "a[id|='delete-router']" ).on( 'click', function( e ) {
        e.preventDefault();
        let rtid = ( this.id ).substring( 14 );

        let html = `<form id="form-delete" method="POST" action="<?= route('router@delete' ) ?>">
                        <div>Do you want to delete this router ?</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="id" value="${rtid}">
                    </form>`;

        bootbox.dialog({
            message: html,
            title: "Delete Router",
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