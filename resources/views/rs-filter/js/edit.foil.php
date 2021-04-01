<script>
    /////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const dd_peer                   = $( "#peer_id" );
    const dd_vlan                   = $( "#vlan_id" );
    const dd_protocol               = $( "#protocol" );

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:
    dd_vlan.change( () => { setCustomer(); } );
    dd_peer.change( () => { setReceivedPrefixes(); setAdvertisePrefixes(); } );

    dd_protocol.change(function() {
        setAdvertisePrefixes();
        setCustomer();
        disablePrefixesInput( $(this).val() );
    });

    $( document ).ready(function() {
        setAdvertisePrefixes()
        setReceivedPrefixes();
    });

    /**
     * set data to the peers dropdown depending on the vlan and protocol
     */
    function setCustomer() {
        let selectedPeer = dd_peer.val();

        let url = "<?= route( 'customer@byVlanAndProtocol' )?>";
        dd_peer.html( `<option value=''>Loading please wait</option>` ).trigger('change.select2');

        $.ajax( url , {
            data: {
                vlanid:     dd_vlan.val(),
                protocol:   dd_protocol.val()
            },
            type: 'POST',
        })

        .done( function( data ) {
            options = `<option value=''>Choose a Peer</option>
                        <option value='0'>All Peers</option>`;
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
            if( $(`#peer_id option[value='${selectedPeer}']`).length > 0 ){
                dd_peer.val( selectedPeer ).trigger( 'change.select2' );
            }
            setReceivedPrefixes();
        });
    }

    /**
     * set data to the received prefixes depending on the peer and protocol
     */
    function setReceivedPrefixes() {
        // if the protocol is not set to 'all'
        // if we have a peer selected and it is not 'all peers'
        if( dd_protocol.val() !== '' && dd_peer.val() !== null && dd_peer.val() !== '0' && dd_peer.val() !== ''  ) {
            let old = '<?= old( 'received_prefix' ) ?>';
            getPrefixes( dd_peer.val(), 'received', old );
        } else {
            // otherwise create the prefix as input text and disable it
            $( '#area_received_prefix' ).html( `<input class="form-control action_prefixes" id="received_prefix" type="text" name="received_prefix" value="*">` )
            if( dd_peer.val() === '0' || dd_protocol.val() === '' ){
                $( '#received_prefix' ).attr( "disabled" , "disabled" )
            }
        }
    }

    /**
     * set data to the advertise prefixes depending on the peer and protocol
     */
    function setAdvertisePrefixes() {
        // if protocol is not 'all', get the prefixes
        if( dd_protocol.val() !== '' ) {
            let old = '<?= old( 'advertised_prefix' ) ?>';
            console.log(old);
            getPrefixes( <?= $this->c->id ?>, 'advertised', old );
        } else {
            // if protocol is 'all', create the prefix as input text and disable it
            $( '#area_advertised_prefix' ).html( `<input class="form-control action_prefixes" id="advertised_prefix" type="text" name="advertised_prefix" value="*">` )
            if( dd_protocol.val() === '' ){
                $( '#advertised_prefix' ).attr( "disabled" , "disabled" )
            }
        }
    }

    /**
     * get the prefixes for a dedicated customer and protocol via Ajax request
     * And populate the dedicated input
     *
     * @var int     custid          customer id
     * @var string  input_select    which prefix should we get ? (advertised/received)
     * @var string  old             the old value if the form is not valid
     */
    function getPrefixes( custid, input_select, old ) {
        let url = "<?= route( 'irrdb-prefix@by-customer-and-protocol' )?>";
        $.ajax( url , {
            data: {
                custid:     custid,
                protocol:   dd_protocol.val()
            },
            type: 'POST'
        })
        .done( function( data ) {
            // data.prefixes = false when the customer->maxprefixes >= 2000
            // data.prefixes.length when we didn't get any result from the request
            // in those case create input text for the dedicated prefix input
            if( data.prefixes === false || data.prefixes.length === 0 ) {
                $( `#area_${input_select}_prefix` ).html( `<input class="form-control" id="${input_select}_prefix" type="text" name="${input_select}_prefix" value="*">` )
            } else {
                // create and populate the dedicated prefix dropdown
                let select = `<select class="chzn-select form-control" id="${input_select}_prefix" name="${input_select}_prefix">
                          <option value="" disabled="disabled">Choose ${input_select} prefix</option>
                          <option value="*" selected>*</option>`;

                $.each( data.prefixes, function( index, value ) {
                    select += `<option value="${value['prefix']}">${value['prefix']}</option>\n`;
                });

                select += `</select>`;
                $( `#area_${input_select}_prefix` ).html( select );
                $( `#${input_select}_prefix` ).select2({ width: '100%', placeholder: function() {
                    $(this).data('placeholder');
                }});

                if( old !== '' ) {
                    $( `#${input_select}_prefix` ).val( old ).trigger( 'change.select2' );
                }else if( $( `#${input_select}_prefix_val` ).val() ){
                    $( `#${input_select}_prefix` ).val( $( `#${input_select}_prefix_val` ).val() ).trigger( 'change.select2' );
                }
            }
        })
        .fail( function() {
            alert( "Error running ajax query for " + url );
            throw new Error( "Error running ajax query for " + url );
        })
    }

    /**
     * Disable the prefixes inputs if the user select the value "both" as protocol
     *
     * @param value
     */
    function disablePrefixesInput( value ) {
        if( value === '' ) {
            $( '.action_prefixes' ).val( "" ).attr( "disabled" , "disabled" ).val( "*" );
        } else {
            $( '.action_prefixes' ).removeAttr( "disabled" );
            if( $( '.action_prefixes' ).val() === "" ){
                $( '.action_prefixes' ).val( "*" );
            }
        }
    }
</script>