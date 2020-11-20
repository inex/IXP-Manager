<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:

    const table               = $( '.table-responsive-ixp-no-header' );

    $(document).ready(function() {
        let hash = window.location.hash;
        hash && $( 'ul.nav a[href="' + hash + '"]' ).tab( 'show' );

        $('.nav-tabs a').click(function ( e ) {
            $( this ).tab( 'show' );
            window.location.hash = this.hash;
        });

        table.show();

        table.DataTable({
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            ordering: false,
            searching: false,
            paging:   false,
            info:   false,
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 },
            ]
        });

        // Hide actions if PPP history is selected
        $( '.nav-tabs li' ).click( function(){
            if( $( this ).children().hasClass( 'current-ppp' ) ){
                $( '.extra-action' ).removeClass( 'disabled' )
            } else{
                $( '.extra-action' ).addClass( 'disabled' )
            }
        });

        $( '.file-toggle-private' ).click( function (e) {
            e.preventDefault();
            let fileid = $( this ).attr( 'data-object-id' );
            let url = $( this ).attr( 'href' );

            $.ajax( url, {
                type : 'POST'
            } )
            .done( function( data ) {
                if( data.isPrivate ) {
                    $( '#file-toggle-private-i-' + fileid ).removeClass().addClass( 'fa fa-unlock' );
                    $( '#file-private-state-' + fileid ).removeClass().addClass( 'fa fa-lock fa-lg' );
                } else {
                    $( '#file-toggle-private-i-' + fileid ).removeClass().addClass( 'fa fa-lock' );
                    $( '#file-private-state-' + fileid ).removeClass().addClass( 'fa fa-unlock fa-lg' );
                }
            });
        });
    });

    $( '.btn-delete-file' ).click( function( event ) {
        event.preventDefault();
        let url = $( this ).attr( 'href');
        let html = `<form id="form-delete-file" method="POST" action="${url}">
                        <div>Do you really want to delete this file?</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="_method" value="delete" />
                    </form>`;

        bootbox.dialog({
            title: "Delete File",
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
                        $( '#form-delete-file' ).submit();
                    }
                },
            }
        });
    });
</script>