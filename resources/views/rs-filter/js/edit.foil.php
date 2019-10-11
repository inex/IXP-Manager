<script>
    /////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const dd_peer         = $( "#peer_id" );
    const dd_vlan         = $( "#vlan_id" );
    const dd_protocol     = $( "#protocol" );


    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    dd_vlan.change(     () => { setCustomer();    } );
    dd_protocol.change( () => { setCustomer();    } );

    /**
     * set data to the switch port dropdown when we select a switch
     */
    function setCustomer(){
        let url;

        url = "<?= route( 'customer@byVlanAndProtocol' )?>";

        dd_peer.html( `<option value=''>Loading please wait</option>` ).trigger('change.select2');

        $.ajax( url , {
            data: {
                vlan_id:     dd_vlan.val(),
                protocol:   dd_protocol.val()
            },
            type: 'POST'
        })
            .done( function( data ) {

                options = "<option value=\"\">Choose a Peer</option>\n";

                $.each( data.listCustomers, function( key, value ){
                    options += `<option value="${key}">${value}</option>\n`;
                });

                dd_peer.html( options );
            })
            .fail( function() {
                options = "<option value=\"\">ERROR</option>\n";
                dd_peer.html( options );
                throw new Error( "Error running ajax query for " + url );
                alert( "Error running ajax query for " + url );
            })
            .always( function() {
                dd_peer.trigger('change.select2');
            });
    }
</script>