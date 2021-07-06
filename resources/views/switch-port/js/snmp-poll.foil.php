<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const btn_select_all = $( "#select-all"  );
    const dd_shared_type = $( "#shared-type" );

    $( document ).ready(function() {
        /**
         * Change the color of the row when selected
        */
        $( ".sp-checkbox"  ).on( 'click', function( event ) {
            event.stopPropagation();
            let id = $( this ).attr( 'id' ).substr( $( this ).attr( 'id' ).lastIndexOf( '-' ) + 1 );
            $( "#poll-tr-" + id ).css( "background", $( this ).is( ":checked" ) ? "#F0F0F0" : "" );
        });

        /**
         * Check or uncheck all the checkboxes
         */
        $( "#select-all"  ).on( 'change', function() {
            $( ".sp-checkbox"   ).prop('checked',       btn_select_all.is( ":checked" ) );
            $( ".poll-tr"       ).css( "background",    btn_select_all.is( ":checked" ) ? "#F0F0F0" : "" );
        });

        /**
         * Reverse the states of the checkboxes
         */
        $( "#checkbox-reverse"  ).on( 'click', function() {
            $( ".sp-checkbox" ).each( function( ) {
                $( this ).prop('checked', !$( this ).is(":checked") );
                $( "#poll-tr-" + $( this ).attr( 'id' ).substr( $( this ).attr( 'id' ).lastIndexOf( '-' ) + 1 ) ).css( "background", $( this ).is(":checked") ? "#F0F0F0" : "" );
            });
        });

        /**
         * Change the type of the selected switch ports via the shared dropdown
        */
        dd_shared_type.on( 'change', function() {
            if( $( this ).val() ) {
                setType( getSelectedSwitchPorts(), "shared-type" );
            }
        });

        /**
         * Change the type of a switch port via the dedicated dropdown
        */
        $( "select[id|='port-type']"  ).on( 'change', function( event ) {
            let id = $( event.target ).attr( 'id' ).substr( $( event.target ).attr( 'id' ).lastIndexOf( '-' ) + 1 );
            setType( [ id ], "port-type" );
        });

        /**
         * Change the status of the selected switch ports to active
         */
        $( "#poll-group-active"  ).on( 'click', function( event ) {
            event.preventDefault();
            changeSwitchPortStatus( 1 );
        });

        /**
         * Change the status of the selected switch ports to inactive
         */
        $( "#poll-group-inactive"  ).on( 'click', function( event ) {
            event.preventDefault();
            changeSwitchPortStatus( 0 );
        });

        /**
         * Get the ID of all the switch ports selected
         *
         *  @return    spids          array of switch port ID
         */
        function getSelectedSwitchPorts()
        {
            let spids = $('.sp-checkbox:checkbox:checked').map( function() {
                return this.id.substr( this.id.lastIndexOf( '-' ) + 1 );
            }).get();

            if( spids.length === 0 ){
                bootbox.alert("You have to select at least 1 port to do this action!");
                return false;
            }

            return spids;
        }


        /**
         *  Change the type on the selected Switch port
         *
         *  @var    id          array of switch port IDs
         *  @var    element     from where the functions has been triggered (individual dropdown, shared dropdown)
         */
        function setType( id, element )
        {
            let sharedType = dd_shared_type;
            let portType;
            let returnMessage = 1;
            let urlAction     = '<?= route( "switch-port@set-type" ) ?>';
            let type          = element === "port-type" ? $( '#port-type-' + id ).val() : sharedType.val();

            if( !id ) {
                sharedType.val( "" );
                return;
            }

            if( element === "port-type" ) {
                portType = $( '#port-type-state-' + id );
                portType.html( "" );
                portType.addClass( "spinner-border" );
                returnMessage = 0;
            } else {
                disableInputsAction();
            }

            $.ajax( urlAction, {
                data: {
                    type            : type,
                    spid            : id,
                    returnMessage   : returnMessage,
                },
                type: 'POST'
            })
            .done( function( data ) {
                if( element === "port-type" ){
                    if( data.success ) {
                        portType.html( '<i style="color:#3c763d" class="fa fa-check"></i>' );
                    } else {
                        portType.html( '<i style="color:#a94442" class="fa fa-times"></i>' );
                    }
                } else {
                    window.location.reload();
                }

            })
            .fail( function(){
                alert( 'Could not update port type(s). API / AJAX / network error' );
                throw new Error("Error running ajax query for " + urlAction);
            })
            .always( function() {
                if( portType ) {
                    portType.removeClass("spinner-border");
                }
            });
        }

        /**
         * Disable all the action button in order to avoid many submit when a request has already been submitted
         */
        function disableInputsAction(){
            $( ".input-sp-action"   ).addClass( 'disabled' );
            dd_shared_type.prop('disabled', 'disabled');
            $( ".port-type"         ).prop('disabled', 'disabled');
            $( '#loading'           ).addClass( "loader" );
        }

        /**
         * Delete the selected switch ports
         */
        $( "#poll-group-delete"  ).on( 'click', function( event ) {
            event.preventDefault();
            if( id = getSelectedSwitchPorts() ) {

                let urlAction = '<?= route( "switch-port@delete-snmp-poll" ) ?>';

                disableInputsAction();

                $.ajax( urlAction, {
                    data: {
                        spid    : id,
                    },
                    type: 'DELETE'
                })
                .done( function() {
                    window.location.reload();
                })
                .fail( function() {
                    alert( 'Could not delete switch ports' );
                    throw new Error("Error running ajax query for " + urlAction);
                });
            }
        });

        /**
         * Change the status of selected switch ports (active or inactive)
         *
         * @var    bool   active   the status wanted (active or inactive)
         */
        function changeSwitchPortStatus( active ) {
            if( id = getSelectedSwitchPorts() ) {

                let urlAction = '<?= route( "switch-port@change-status" ) ?>';
                disableInputsAction();
                $.ajax( urlAction, {
                    data: {
                        spid        : id,
                        active      : active,
                    },
                    type: 'POST'
                })
                .done( function() {
                    window.location.reload();
                })
                .fail( function() {
                    alert( 'Could not change switch port(s) status' );
                    throw new Error("Error running ajax query for " + urlAction);
                });
            }
        }
    });
</script>