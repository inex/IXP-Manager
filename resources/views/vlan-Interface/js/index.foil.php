<script>
    $( document ).ready( function() {
        loadDataTable();
    } );


    /**
     * initialisation of the Clipboard even on the class in parameter
     */
    var clipboard = new Clipboard('.btn-copy');

    /**
     * initialisation of tooltip
     */
    $('.btn-copy').tooltip({
        trigger: 'click',
        placement: 'bottom'
    });

    /**
     * display a tooltip on the Clipboard button
     */
    function setTooltip(btn, message) {
        $(btn).attr('data-original-title', message)
            .tooltip('show');
    }

    /**
     * hide a tooltip on the Clipboard button
     */
    function hideTooltip(btn) {
        setTimeout(function() {
            $(btn).tooltip('hide');
        }, 1000);
    }

    /**
     * success even when using Clipboard
     */
    clipboard.on('success', function(e) {
        setTooltip(e.trigger, 'Copied!');
        hideTooltip(e.trigger);
    });


    /**
     * on click even allow to add a mac address using prompt popup
     */
    $( "#add-l2a" ).on( 'click', function( e ) {
        e.preventDefault();
        bootbox.prompt({
            title: "Enter a MAC Address.",
            inputType: 'text',
            callback: function ( result ) {
                if( result != '' ){
                    $.ajax( "<?= url( 'api/v4/l2-address/add' ) ?>", {
                        data: {
                            id : <?= $t->vli->getId() ?>,
                            mac : result,
                            _token : "<?= csrf_token() ?>"
                        },
                        type: 'POST'
                    })
                    .done( function( data ) {
                        $('.bootbox.modal').modal( 'hide' );
                        result = ( data.success ) ? 'success': 'danger';
                        if( result ){
                            refreshDataTable();
                        }

                        $( "#message" ).html( "<div class='alert alert-"+result+"' role='alert'>"+ data.message +"</div>" );
                    })
                    .fail( function(){
                        alert( 'Could add MAC address. API / AJAX / network error' );
                        throw new Error("Error running ajax query for api/v4/l2-address/add");
                    })
                }
            }
        });
    });

    /**
     * on click even allow to delete a mac address
     */
    $(document).on('click', "button[id|='delete-l2a']" ,function(e){
        e.preventDefault();
        var l2aId = (this.id).substring(11);
        deleteL2a( l2aId );
    });

    /**
     * on click even allow to ave a view of the mac address (with different format)
     */
    $(document).on('click', "a[id|='view-l2a']" ,function(e){
        e.preventDefault();
        var l2aId = (this.id).substring(9);
        $.ajax( "<?= url( 'api/v4/l2-address/detail' ) ?>/"+l2aId )
            .done( function( data ) {
                $("#mac").val( data.mac );
                $("#macComma").val( data.macFormatedComma );
                $("#macDot").val( data.macFormatedDot );
            })
            .fail( function(){
                alert( 'Could add MAC address. API / AJAX / network error' );
                throw new Error("Error running ajax query for api/v4/l2-address/detail/{id}");
            })
        $('#notes-modal').modal('show');
    });

    /**
     * allow to refresh the table without reloading the page
     * reloading only a part of the DOM
     */
    function refreshDataTable() {
        $( "#list-area").load( "<?= url('/layer-2-address/list' ).'/'.$t->vli->getId()?> #layer-2-interface-list " ,function( ) {
            table.destroy();
            loadDataTable();
        });
    }

    /**
     * function to delete a mac address using a confirm popup
     */
    function deleteL2a(l2aId){
        bootbox.confirm({
            message: "Do you really want to delete this MAC Address ?",
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
                    $.ajax( "<?= url( 'api/v4/l2-address/delete' ) ?>/"+l2aId )
                        .done( function( data ) {
                            $('.bootbox.modal').modal( 'hide' );
                            result = ( data.success ) ? 'success': 'danger';
                            if( result ){
                                refreshDataTable();
                            }

                            $( "#message" ).html( "<div class='alert alert-"+result+"' role='alert'>"+ data.message +"</div>" );
                            $( "button[id|='delete-l2a']" ).on('click', deleteL2a);

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