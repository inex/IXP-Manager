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
function deletePopup( btn_delete, viid, type ) {
    let objectName, urlDelete;

    let user = 0;

    if( type === "vli") {
        objectName = "Vlan Interface";
        urlDelete  = "<?= route( 'vlan-interface@delete' ) ?>";
    } else if( type === "vi" ) {
        objectName = "Virtual Interface";
        urlDelete = "<?= route( 'virtual-interface@delete' ) ?>" ;
        if( $( "#custid" ).val() !== undefined ){
            user = $( "#custid" ).val();
        }
    } else if( type === "sflr" ) {
        objectName = "Sflow Receiver";
        urlDelete = btn_delete.attr( 'data-url');
    } else if( type === "pi" ) {
        objectName = "Physical Interface";
        urlDelete = btn_delete.attr( 'data-url');
    }

    let reltype      = "normal";
    if( btn_delete.attr( "data-type" ) === "1" ){
        reltype = "fanout";
    } else if( btn_delete.attr( "data-type" ) === "6" ){
        reltype = "peering";
    }

    let related = false;
    let extraMessage = '';
    if( btn_delete.attr( "data-related" ) ){
        related = true;
        extraMessage = `It has a related ${reltype} interface.`;
    }

    let html = `<form id="form-delete" method="POST" action="${urlDelete}">
                    <div>Do you really want to delete this ${objectName}? ${extraMessage}</div>

                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <input type="hidden" id="related" name="related" value="0">
                    <input type="hidden" id="user" name="user" value="${user}">
                    <input type="hidden" name="_method" value="delete" />
                </form>`;

    if( !related ) {
        bootbox.dialog({
            title: `Delete ${objectName}`,
            message: html ,
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
                }
            }
        });
    } else {
        bootbox.dialog({
            title: `Delete ${objectName}`,
            size: 'large',
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
                deleteRelated: {
                    label: `Delete with related ${reltype} interface`,
                    className: 'btn-warning',
                    callback: function(){
                        $( "#related" ).val( 1 );
                        $('#form-delete').submit();
                    }
                },
                confirm: {
                    label: "Delete",
                    className: 'btn-danger',
                    callback: function () {
                        $('#form-delete').submit();
                    }
                }
            }
        });
    }
}
</script>