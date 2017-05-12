<script>

    /**
     * allow to refresh the table without reloading the page
     * reloading only a part of the DOM
     */
    function refreshDataTable( htmlId, urlListReload, viid) {
        $( "#area-"+htmlId).load( urlListReload+" #table-"+ htmlId ,function( ) {
            if( !viid ){
                table.destroy();
                loadDataTable( htmlId );
            }

        });
    }

    /**
     * function to delete a virtual/physical/vlan interface
     */
    function deletePopup( id, viid, type ){

        if( type == "vli"){
            objectName = "Vlan Interface";
            urlDelete = "<?= url( 'api/v4/vlan-interface/delete/' ) ?>";
            urlListReload = "<?= url( 'vlanInterface/list/' ) ?>";
        }
        else if( type == "vi" ){
            objectName = "Virtual Interface";
            urlDelete = "<?= url( 'api/v4/virtual-interface/delete' ) ?>" ;
            urlListReload = "<?= url( 'virtualInterface/list/' ) ?>";
        }
        else if( type == "sflr" ){
            objectName = "Sflow Receiver";
            urlDelete = "<?= url( 'api/v4/sflow-receiver/delete' ) ?>" ;
            urlListReload = "<?= url( 'sflowReceiver/list/' ) ?>";
        }
        else if( type == "pi" ){
            objectName = "Physical Interface";
            urlDelete = "<?= url( 'api/v4/physical-interface/delete' ) ?>";
            urlListReload = "<?= url( 'physicalInterface/list/' ) ?>";
        }

        if( viid ){
            urlListReload = "<?= url('/virtualInterface/edit/' ) ?>/"+viid ;
        }

        bootbox.confirm({
            message: "Do you really want to delete this " +objectName+ " ?",
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-primary'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if( result) {
                    $.ajax( urlDelete+"/"+id )
                        .done( function( data ) {
                            $('.bootbox.modal').modal( 'hide' );
                            result = ( data.success ) ? 'success': 'danger';
                            if( result ){
                                $( "#message-"+type ).html( "<div class='alert alert-"+result+"' role='alert'>"+ data.message +"</div>" );
                                refreshDataTable( type, urlListReload , viid);
                            }
                        })
                        .fail( function(){
                            alert( 'Could add MAC address. API / AJAX / network error' );
                            throw new Error("Error running ajax query for api/v4/l2-address/{id}/delete");
                        })
                }
            }
        });
    }

    /**
     * initialise the datatable table
     */
    function loadDataTable( tableId ){
        table = $( '#table-' + tableId ).DataTable( {
            "autoWidth": false,
            "iDisplayLength": 100
        });
    }
</script>