<script>
    const addl2a           = $('#add-l2a' );
    const spanCustAddBtn   = $('#span-cust-add-btn');


    let table; // datatable handle

    $( document ).ready( function() {
        loadDataTable();
        $( "#list-area").show();

    });

    /**
     * on click even allow to add a mac address using prompt popup
     */
    addl2a.on( 'click', function( e ) {
        e.preventDefault();

        bootbox.prompt({

            title: "Enter a MAC Address.",
            inputType: 'text',
            buttons: {
                cancel: {
                    className: "btn-secondary"
                },
                confirm: {
                    label: "Add"
                }
            },
            callback: function ( result ) {

                if( result != null ) {
                    $.ajax( "<?= action ( 'Api\V4\Layer2AddressController@add' ) ?>", {
                        type: 'POST',
                        data: {
                            vliid : <?= $t->vli->getId() ?>,
                            mac : result,
                            _token : "<?= csrf_token() ?>"
                        }
                    })
                        .done( function( data ) {
                            $('.bootbox.modal').modal( 'hide' );
                            result = ( data.success ) ? 'success': 'danger';
                            if( result ) {
                                refreshDataTable();
                            }

                            $( "#message" ).html( "<div class='alert alert-"+result+"' role='alert'>"+ data.message +"</div>" );
                        })
                        .fail( function() {
                            $('.bootbox.modal').modal( 'hide' );
                            $( "#message" ).html( "<div class='alert alert-danger' role='alert'>" +
                                "Could not add MAC address. API / AJAX / network error</div>"
                            );
                        });
                }
            }
        });
    });

    /**
     * on click even allow to delete a mac address
     */
    $(document).on('click', "button[id|='delete-l2a']" ,function(e){
        e.preventDefault();
        deleteL2a( (this.id).substring(11), this );
    });

    /**
     * allow to refresh the table without reloading the page
     * reloading only a part of the DOM
     */
    function refreshDataTable() {

        $( "#list-area").load( "<?= route( "layer2-address@forVlanInterface" , [ 'id' => $t->vli->getId() ] ) ?> #layer-2-interface-list " ,function( ) {
            table.destroy();
            loadDataTable();

            if( spanCustAddBtn.length ) {
                if( table.rows().count() >= <?= config( 'ixp_fe.layer2-addresses.customer_params.max_addresses' ) ?> ) {
                    spanCustAddBtn.hide();
                } else {
                    spanCustAddBtn.show();
                }
            }
        });
    }

    /**
     * function to delete a mac address using a confirm popup
     */
    function deleteL2a( l2aId, deleteBtn ){
        bootbox.confirm({
            message: "Do you really want to delete this MAC Address?",
            buttons: {
                confirm: {
                    label: 'Delete',
                    className: 'btn-danger'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-primary'
                }
            },
            callback: function (result) {
                if( result) {
                    $.ajax( "<?= url( 'api/v4/l2-address/delete' ) ?>/"+l2aId , {
                        type : 'POST'
                    } )
                        .done( function( data ) {
                            $('.bootbox.modal').modal( 'hide' );
                            result = ( data.success ) ? 'success': 'danger';

                            if( result ){
                                refreshDataTable();
                                $( "#message" ).html( "<div class='alert alert-"+result+"' role='alert'>"+ data.message +"</div>" );
                            }

                        })
                        .fail( function(){
                            alert( `Couldn't add MAC address. API / AJAX / network error` );
                            throw new Error("Error running ajax query for api/v4/l2-address/{id}/delete");
                        })
                }
            }
        });
    }

    /**
     * initialise the datatable table
     */
    function loadDataTable(){
        table = $( '#layer-2-interface-list' ).DataTable( {
            "autoWidth": false,
            "columnDefs": [{
                "targets": [ 0 ],
                "visible": false,
                "searchable": false,
            }],
            "order": [[ 0, "asc" ]]
        });
    }

</script>
