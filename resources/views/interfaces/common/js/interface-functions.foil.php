<script>

/**
 * Takes the currently selected VLAN from the dd_vlan dropdown and calls an
 * AJAX API endpoint to get the available IP addresses.
 */
function updateIpAddresses() {
    if( dd_vlan.val() ) {

        // This function may be called on a form submission error where IPs are already chosen.
        // If that's the case, we want to remember them
        let selectedIPv6 = $( "#original-ipv6address" ).val();
        let selectedIPv4 = $( "#original-ipv4address" ).val();

        $( dd_ipv6 ).html( `<option value="">Loading, please wait...</option>` ).trigger('change.select2');
        $( dd_ipv4 ).html( `<option value="">Loading, please wait...</option>` ).trigger('change.select2');

        // Hide and clear the 'already used in another VLAN' warning (if it is displayed):
        $( '.ip-is-used-alert' ).html( '' ).hide();

        let vlanid = dd_vlan.val();
        let url    = "<?= url( '/api/v4/vlan' )?>/" + vlanid + "/ip-addresses";

        ajaxRequests.push( $.ajax( url )
            .done( function( data ) {
                // IPv6
                let options = `<option value="">Choose an IPv6 Address</option>`;
                $.each( data.ipv6, function( key, ip ) {
                    // if the address is free (or used by this interface):
                    if( ip.vliid === null || ( selectedIPv6 && selectedIPv6 === ip.address ) ) {
                        options += `<option value="${ip.address}">${ip.address}</option>\n`;
                    }
                });
                dd_ipv6.html( options );

                if( selectedIPv6 ) {
                    dd_ipv6.val( selectedIPv6 );
                }

                // IPv4
                options = `<option value="">Choose an IPv4 Address</option>`;
                $.each( data.ipv4, function( key, ip ) {
                    // if the address is free (or used by this interface):
                    if( ip.vliid === null || ( selectedIPv4 && selectedIPv4 === ip.address ) ) {
                        options += `<option value="${ip.address}">${ip.address}</option>\n`;
                    }
                });
                dd_ipv4.html( options );

                if( selectedIPv4 ) {
                    dd_ipv4.val( selectedIPv4 );
                }

            })
            .fail( function() {
                let options = `<option value="">ERROR</option>`;
                dd_ipv6.html( options ).trigger('change.select2');
                dd_ipv4.html( options ).trigger('change.select2');
                throw new Error( "Error running ajax query for " + url );
            })
            .always( function() {
                dd_ipv6.trigger('change.select2');
                dd_ipv4.trigger('change.select2');
            })
        ); // ajaxRequests.push()
    } else {
        dd_ipv6.html( `<option value="">Select a VLAN above!</option>` ).trigger('change.select2');
        dd_ipv4.html( `<option value="">Select a VLAN above!</option>` ).trigger('change.select2');
    }
}

/**
 * Function to 'smartly' update the MD5 input fields with a cryptographically secure
 * PRNG'd string.
 *
 * The excess logic allows for:
 *
 * 1. if both v4 and v6 md5 are empty, populate the selected one
 * 2. if the selected one is empty but the other is not, copy the value
 * 3. repeated clicks cycle values
 * 4. after repeated clicks, clicking the other will copy the value
 *
 * Such it and see - I think it makes sense ;-)   (barryo)
 */
function updateMD5( e ) {
    const element         = $(this).children();
    const in_ipv6_md5     = $( "#ipv6bgpmd5secret" );
    const in_ipv4_md5     = $( "#ipv4bgpmd5secret" );

    const v6                = in_ipv6_md5.val().trim();
    const v4                = in_ipv4_md5.val().trim();

    const target  = element.hasClass( 'glyphicon-generator-ipv6' ) || element.find( '.glyphicon-generator-ipv6' ).length !== 0 ? in_ipv6_md5 : in_ipv4_md5;
    const other   = target === in_ipv6_md5 ? in_ipv4_md5 : in_ipv6_md5;

    const vtarget = target.val().trim();
    const vother  = other.val().trim();

    let repeat = false;
    if( window.ixp_md5_last_toggle === target.attr('id') ) {
        repeat = true;
    } else {
        window.ixp_md5_last_toggle = target.attr('id');
    }

    if( v4 === "" && v6 === "" ) {
        target.val( ixpRandomString( 12 ) );
        return;
    }

    if( vtarget === "" && vother !== "" ) {
        target.val( vother );
        return;
    }

    if( repeat ) {
        target.val( ixpRandomString( 12 ) );
    } else {
        target.val( vother );
    }
}


/**
 * Fn to perform an API query to see if a select IP address is in use across
 * any VLAN and, if it is, add a warning message.
 */
function usedAcrossVlans() {
    const inputName = $( this ).attr( "id" );
    const ipAddress = $( '#' + inputName ).val();

    $( '#alert-' + inputName ).html( '' ).hide();

    if( ipAddress ) {
        let html = "<ul>";

        ajaxRequests.push( $.ajax({
                url: "<?= route( 'vlan@used-across-vlans' )?>",
                method: "POST",
                data: { ip: ipAddress }
            })
            .done( function( data ) {
                $.each( data, function( key, vli ){
                    html += `<li>${ipAddress} is in use by ${vli.cabbreviatedname} on ${vli.vname}</li>\n`;
                });
            })
            .fail( function() {
                html += "<li>Error running ajax query for " + url + "</li>";
                throw new Error( "Error running ajax query for " + url );
            })
            .always( function() {
                if( html !== "<ul>" ) {
                    console.log( html );
                    $('#alert-' + inputName ).html( html + '</ul>' ).show();
                }
            })
        );
    }
}

/**
 * Takes the currently selected switch from the dd_switch dropdown and calls an
 * AJAX API endpoint to get the available ports.
 */
function updateSwitchPort( e ) {
    let dd_sp, arrayType, selectedPort;
    let sw = $( e.target );

    if( $( this ).attr( 'data-value' ) === "fanout" ) {
        dd_sp     = $( "#switch-port-fanout" );
        arrayType = [ <?= \IXP\Models\SwitchPort::TYPE_UNSET ?>, <?= \IXP\Models\SwitchPort::TYPE_FANOUT ?> ];
        selectedPort = $( "#original-switch-port-fanout" ).val();
    } else {
        dd_sp     = $( "#switchportid" );
        arrayType = [ <?= \IXP\Models\SwitchPort::TYPE_UNSET ?>,  <?= \IXP\Models\SwitchPort::TYPE_PEERING ?>, <?= \IXP\Models\SwitchPort::TYPE_CORE ?> ];
        selectedPort = $( "#original-switch-port" ).val();
    }

    let url = "<?= url( '/api/v4/switch' )?>/" + sw.val() + "/ports";

    datas = {
        types : arrayType,
        notAssignToPI: 1,
        piNull: 0,
        spIdsExcluded : []
    };

    dd_sp.html( "<option value=\"\">Loading, please wait...</option>\n" ).trigger('change.select2');

    $.ajax( url , {
        data: datas,
        method: "GET",
        _token : "<?= csrf_token() ?>"
    })
    .done( function( data ) {
        let options = "<option value=\"\">Choose a switch port</option>\n";

        $.each( data.ports, function( key, port ) {
            if( ( port.pi_id === null || port.id === parseInt( selectedPort ) ) ) {
                options += `<option value="${port.id}">${port.name} (${port.type})</option>\n`;
            }
        });

        dd_sp.html( options );

        if( selectedPort ) {
            dd_sp.val( selectedPort );
        }
    })
    .fail( function() {
        let options = "<option value=\"\">ERROR</option>\n";
        dd_sp.html( options );
        throw new Error( "Error running ajax query for " + url );
    })
    .always( function() {
        dd_sp.trigger('change.select2');
    });
}
</script>