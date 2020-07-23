<script>
    /////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const dd_peer         = $( "#peer_id" );
    const dd_vlan         = $( "#vlan_id" );
    const dd_protocol     = $( "#protocol" );
    const input_prefix    = $( "#prefix" );

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:
    dd_vlan.change(     () => { setCustomer(); } );
    dd_protocol.change( () => { setCustomer(); } );


    $( document ).ready(function() {
        disablePrefixInput( dd_protocol.val() );
    });

    /**
     * set data to the switch port dropdown when we select a switch
     */
    function setCustomer(){
        let url = "<?= route( 'customer@byVlanAndProtocol' )?>";
        dd_peer.html( `<option value=''>Loading please wait</option>` ).trigger('change.select2');

        $.ajax( url , {
            data: {
                vlanid:     dd_vlan.val(),
                protocol:   dd_protocol.val()
            },
            type: 'POST'
        })
        .done( function( data ) {
            options = `<option value=''>Choose a Peer</option>`;
            $.each( data.listCustomers, function( index, value ) {
                options += `<option value="${value['id']}">${value['name']}</option>\n`;
            });
            dd_peer.html( options );
        })
        .fail( function() {
            options = "<option value=\"\">ERROR</option>\n";
            dd_peer.html( options );
            alert( "Error running ajax query for " + url );
            throw new Error( "Error running ajax query for " + url );
        })
        .always( function() {
            dd_peer.trigger('change.select2');
        });
    }

    dd_protocol.change(function() {
        disablePrefixInput( $(this).val() );
    });

    /**
     * Disable the prefix input if the user select the value "both" as protocol
     *
     * @param value
     */
    function disablePrefixInput( value ) {
        if( value === '' ){
            input_prefix.val( "" ).attr( "disabled" , "disabled" );
        } else {
            input_prefix.removeAttr( "disabled" );
            if( input_prefix.val() === "" ){
                input_prefix.val( "*" );
            }
        }
    }
</script>