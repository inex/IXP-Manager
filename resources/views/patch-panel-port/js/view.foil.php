<script>
    $(document).ready(function() {

        let hash = window.location.hash;
        hash && $('ul.nav a[href="' + hash + '"]').tab('show');

        $('.nav-tabs a').click(function (e) {
            $(this).tab('show');
            window.location.hash = this.hash;
        });

        $( '.table-responsive-ixp-no-header' ).show();

        $( '.table-responsive-ixp-no-header' ).DataTable({
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
        $( ".nav-tabs li" ).click( function(){
            if( $( this ).children().text() != "Current" ){
                $( ".extra-action" ).addClass( 'disabled' )
            } else{
                $( ".extra-action" ).removeClass( 'disabled' )
            }
        });

        $( '.table-responsive-ixp-no-header' ).on( 'click', '.file-toggle-private',  function (e) {
            e.preventDefault();

            let pppfid = (this.id).substring(20);

            $.ajax( "<?= url('patch-panel-port/toggle-file-privacy') ?>/" + pppfid, {
                type : 'POST'
            } )
                .done( function( data ) {
                    if( data.isPrivate ) {
                        $( '#file-toggle-private-i-' + pppfid ).removeClass('fa-lock').removeClass('fa-unlock').addClass('fa-unlock');
                        $( '#file-private-state-' + pppfid ).removeClass('fa-lock').removeClass('fa-unlock').addClass('fa-lock');
                    } else {
                        $( '#file-toggle-private-i-' + pppfid ).removeClass('fa-lock').removeClass('fa-unlock').addClass('fa-lock');
                        $( '#file-private-state-' + pppfid ).removeClass('fa-lock').removeClass('fa-unlock').addClass('fa-unlock');
                    }
                });
            });

    });

    function deletePopup( idFile, idHistory, objectType ){
        bootbox.confirm({
            title: "Delete",
            message: "Are you sure you want to delete this object ?",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel',
                    className: 'btn-secondary'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm'
                }
            },
            callback: function ( result ) {
                if( result ){

                    let urlAction = objectType == 'ppp' ? "<?= url('patch-panel-port/delete-file') ?>" : "<?= url('patch-panel-port/delete-history-file') ?>";

                    $.ajax( urlAction + "/" + idFile , {
                        type : 'POST'
                    })
                        .done( function( data ) {
                            if( data.success ){
                                $( "#area_file_"+idHistory+'_'+objectType ).load( "<?= route('patch-panel-port@view' , [ 'id' => $t->ppp->getId() ] ) ?> #list_file_"+idHistory+'_'+objectType );
                                $( '.bootbox.modal' ).modal( 'hide' );
                            }
                            else{
                                $( '#message-'+idHistory+'-'+objectType ).html("<div class='alert alert-danger' role='alert'>" + data.message + "</div>");
                                $( '#delete_'+idFile ).remove();
                            }
                        })
                        .fail( function() {
                            throw new Error( "Error running ajax query for patch-panel-port/deleteFile/" );
                            alert( "Error running ajax query for patch-panel-port/deleteFile/" );
                            $( "#customer" ).html("");
                        })
                }
            }
        });
    }
</script>