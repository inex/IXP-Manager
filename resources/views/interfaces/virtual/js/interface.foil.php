<script>


$( document ).ready(function() {

    $('[data-toggle="tooltip"]').tooltip();

    $('.table-responsive-ixp-no-header').show();

    $('.table-responsive-ixp-no-header').DataTable( {
        stateSave: true,
        stateDuration : DATATABLE_STATE_DURATION,
        responsive: true,
        ordering: false,
        searching: false,
        paging:   false,
        info:   false,
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: -1 }
        ],
    } );

    $('.table-responsive-ixp-with-header').show();

    $('.table-responsive-ixp-with-header').DataTable( {
        stateSave: true,
        stateDuration : DATATABLE_STATE_DURATION,
        responsive: true,
        stateSave: true,
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: -1 }
        ],
    } );
});

/**
 * function to delete a virtual/physical/vlan interface
 */
function deletePopup( id, viid, type ) {

    let objectName, urlDelete;

    if( type === "vli") {
        objectName = "Vlan Interface";
        urlDelete  = "<?= url( 'interfaces/vlan/delete' ) ?>";
        urlRedirect = "<?= route( 'interfaces/vlan/list' ) ?>" ;
    } else if( type === "vi" ) {
        objectName = "Virtual Interface";
        urlDelete = "<?= url( 'interfaces/virtual/delete' ) ?>" ;
        if( $( "#custid" ).val() !== undefined ){
            urlRedirect = "<?= url( 'customer/overview' ) ?>/" + $( "#custid" ).val() + "/ports"  ;
        }else{
            urlRedirect = "<?= route( 'interfaces/virtual/list' ) ?>" ;
        }

    } else if( type === "sflr" ) {
        objectName = "Sflow Receiver";
        urlDelete = "<?= url( 'interfaces/sflow-receiver/delete' ) ?>" ;
        urlRedirect = "<?= route( 'interfaces/sflow-receiver/list' ) ?>" ;
    } else if( type === "pi" ) {
        objectName = "Physical Interface";
        urlDelete = "<?= url( 'interfaces/physical/delete' ) ?>";
        urlRedirect = "<?= route( 'interfaces/physical/list' ) ?>" ;
    }

    const btn_delete = $( '#delete-' +type+ '-' + id );

    let reltype      = "normal";
    if( btn_delete.attr( "data-type" ) === "1" ){
        reltype = "fanout";
    } else if( btn_delete.attr( "data-type" ) === "6" ){
        reltype = "peering";
    }

    let related = false;
    if( btn_delete.attr( "data-related" ) ){
        related = true;
    }

    if( !related ) {
        bootbox.confirm({
            message: `Do you really want to delete this ${objectName}?` ,
            buttons: {
                cancel: {
                    label: 'Cancel',
                    className: 'btn-secondary'
                },
                confirm: {
                    label: 'Delete',
                    className: 'btn-danger'
                }
            },
            callback: function ( result ) {
                if( result) {
                    $.ajax( urlDelete + "/" + id,{
                        type : 'POST'
                    })
                        .done( function( data ) {
                            if( type !== "vi" ) {
                                location.reload();
                            } else {
                                window.location.href = urlRedirect;
                            }
                        })
                        .fail( function(){
                            throw new Error( `Error running ajax query for ${urlDelete}/${id}` );
                        })
                }
            }
        });
    } else {
        bootbox.dialog({
            title: "",
            message: `<b>Do you really want to delete this ${objectName}? It has a related ${reltype} interface.</b>`,
            buttons: {
                cancel: {
                    label: "Cancel",
                    className: 'btn-secondary',
                },
                deleteRelated: {
                    label: `Delete with related ${reltype} interface`,
                    className: 'btn-warning',
                    callback: function(){
                        $.ajax( urlDelete + "/" + id,{
                            type : 'POST',
                            data: {
                                related : true
                            },
                        })
                        .done( function( data ) {
                            location.reload();
                        })
                        .fail( function(){
                            throw new Error( `Error running ajax query for ${urlDelete}/${id}` );
                        })
                    }
                },
                confirm: {
                    label: "Delete",
                    className: 'btn-danger',
                    callback: function(){
                        $.ajax( urlDelete + "/" + id,{
                            type : 'POST'
                        })
                        .done( function( data ) {
                            location.reload();
                        })
                        .fail( function(){
                            throw new Error( `Error running ajax query for ${urlDelete}/${id}` );
                        })
                    }
                }
            }
        });
    }
}
</script>