
<script>

    $(document).ready( function() {
        // allow enough space for form labels:
        $( 'label.col-lg-2' ).removeClass('col-lg-2');
        $( '#div-well' ).show();

        if( $( '#ipv4-enabled' ).is(":checked") ) {
            $( "#ipv4-area" ).slideDown();
        }
        if( $( '#ipv6-enabled' ).is(":checked") ) {
            $( "#ipv6-area" ).slideDown();
        }

        if( $( "#switch" ).val() != null ){

            $( "#switch" ).change();

        }
        checkResoldCusts();
        checkFanout();

        setIPVx();

        <?php if( config( 'ixp.reseller.enabled') ): ?>


            $( "#cust" ).on( 'change', checkResoldCusts );
            $( '#fanout' ).on( 'click', checkFanout );
            setFanoutSp();

        <?php endif; ?>


        $( "#ipv4-address" ).select2({
            tags: true,
            width: '100%'
        });

        $( "#ipv6-address" ).select2({
            tags: true,
            width: '100%'
        });

    });

    function checkFanout(){
            if( $( '#fanout' ).prop( 'checked' ) )
                $( '#fanout-area' ).slideDown();
            else
                $( '#fanout-area' ).slideUp();
    }

    function setFanoutSp(){
        if( $( '#fanout' ).prop( 'checked' ) && $( "#switch-fanout" ).val() != null ){
            $( "#switch-fanout" ).change();
        }
    }
    function checkResoldCusts() {

        var resoldCusts = <?= $t->resoldCusts ?>;
        if( resoldCusts[ $('#cust').val() ] === undefined )
        {
            $( '#fanout' ).removeAttr( "checked" );
            $( '#fanout-area' ).slideUp();
            $( '#fanout-box' ).slideUp();
        }
        else{
            $( '#fanout-box' ).slideDown();
        }
    }

    $( "#vlan" ).on( 'change', setIPVx );
    $( "#switch" ).on( 'change', updateSwitchPort );
    $( "#switch-fanout" ).on( 'change', updateSwitchPort );

    $( "#ipv4-address" ).on( 'change', usedAcrossVlans );
    $( "#ipv6-address" ).on( 'change', usedAcrossVlans );


    function usedAcrossVlans(){
        inputName = $( this ).attr( "id" );
        ipAddress = $( '#' + inputName ).val();

        console.log( ipAddress );
        $( '#alert-' + inputName ).html( '' ).hide();
        if( ipAddress ) {
            url = "<?= url( '/api/v4/ip-address/used-across-vlans/' )?>/" + ipAddress ;

            $.ajax( url )
                .done( function( data ) {
                    $.each( data.vlans, function( key, vlan ){
                        $( '#alert-' + inputName ).append( "<div>- The IP address " + ipAddress + " is already used by the customer " + vlan.customer.name + " on the VLAN " + vlan.vlan.name + " </div>");
                    });
                    if(  data.vlans.length > 0 ){
                        $( '#alert-' + inputName ).show();
                    }

                })
                .fail( function() {
                    alert( "Error running ajax query for " + url );
                    throw new Error( "Error running ajax query for " + url );
                })
                .always( function() {

                });
        }
    }

    function updateSwitchPort(){
        var type = "";
        var arrayType = [ <?= \Entities\SwitchPort::TYPE_UNSET ?>,  <?= \Entities\SwitchPort::TYPE_PEERING ?>];
        var excludeSp = $( "#switch-port-fanout" ).val();

        if( $( this ).attr( "id" ).substr( -6 ) == "fanout" )
        {
            type = "-fanout";
            arrayType = [ <?= \Entities\SwitchPort::TYPE_UNSET ?>, <?= \Entities\SwitchPort::TYPE_FANOUT ?> ];
            excludeSp = $( "#switch-port" ).val();
        }

        $( "#switch-port" + type ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );

        switchId = $( "#switch" + type ).val();

        url = "<?= url( '/api/v4/switch' )?>/" + switchId + "/ports";

        var options;

        $.ajax( url )
            .done( function( data ) {
                options = "<option value=\"\">Choose a switch port</option>\n";


                $.each( data.switchports, function( key, port ){
                    if( port.pi_id == null && arrayType.indexOf( port.sp_type ) != -1 && excludeSp != port.sp_id ) {
                        options += "<option value=\"" + port.sp_id + "\">" + port.sp_name + " (" + port.sp_type_name + ")</option>\n";
                    }
                });
                $( "#switch-port" + type ).html( options );
            })
            .fail( function() {
                options = "<option value=\"\">ERROR</option>\n";
                $( "#switch-port" + type ).html( options );
                alert( "Error running ajax query for " + url );
                throw new Error( "Error running ajax query for " + url );
            })
            .always( function() {
                $( "#switch-port" + type ).trigger( "chosen:updated" );
            });
    }


    function setIPVx() {
        if( $("#vlan").val() ) {

            $( "#ipv4-address" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );
            $( "#ipv6-address" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );

            $( '.ip-is-used-alert').html( '' ).hide();

            vlanid = $("#vlan").val();
            var options;

            $.ajax( "<?= url( '/api/v4/vlan' )?>/" + vlanid + "/ip-addresses" )
                .done( function( data ) {

                    options = "<option value=\"\">Choose an IPv4 Address</option>\n";
                    $.each( data.ipv4, function( key, ip ) {
                        // if the address is free:
                        if( ip.vli_id == null ) {
                            options += "<option value=\"" + ip.address + "\">" + ip.address + " </option>\n";
                        }
                    });
                    $( "#ipv4-address" ).html( options );

                    options = "<option value=\"\">Choose an IPv6 Address</option>\n";
                    $.each( data.ipv6, function( key, ip ) {
                        // if the address is free:
                        if( ip.vli_id == null ) {
                            options += "<option value=\"" + ip.address + "\">" + ip.address + " </option>\n";
                        }
                    });
                    $( "#ipv6-address" ).html( options );

                })
                .fail( function() {
                    options = "<option value=\"\">ERROR</option>\n";
                    $( "#ipv4-address" ).html( options ).trigger( "chosen:updated" );
                    $( "#ipv6-address" ).html( options ).trigger( "chosen:updated" );

                    alert( "Error running ajax query for " + url );
                    throw new Error( "Error running ajax query for " + url );
                })
                .always( function() {
                    $( "#ipv4-address" ).trigger( "chosen:updated" );
                    $( "#ipv6-address" ).trigger( "chosen:updated" );
                });

        } else {
            $( "#ipv4-address" ).html( "<option value=\"\">Select a VLAN above!</option>\n" ).trigger( "chosen:updated" );
            $( "#ipv6-address" ).html( "<option value=\"\">Select a VLAN above!</option>\n" ).trigger( "chosen:updated" );
        }
    }

    function randomString( length ) {
        var result = '';

        // if we do not have a cryptographically secure version of a PRNG, just alert and return
        if( window.crypto.getRandomValues === undefined ) {
            alert( 'No cryptographically secure PRNG available.' );
        } else {
            var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            var array = new Uint32Array(length);

            window.crypto.getRandomValues(array);
            for( var i = 0; i < length; i++ )
                result += chars[ array[i] % chars.length ];
        }

        return result;
    }

    $( ".glyphicon-generator-ipv4" ).parent().on( 'click', function( e ) {
        $( "#ipv4-bgp-md5-secret" ).val( randomString( 12 ) );
    });

    $( ".glyphicon-generator-ipv6" ).parent().on( 'click', function( e ) {
        $( "#ipv6-bgp-md5-secret" ).val( $( "#ipv4-bgp-md5-secret" ).val().trim() === '' ? randomString( 12 ) : $( "#ipv4-bgp-md5-secret" ).val() );
    });

    /**
     * hide the help block at loading
     */
    $('p.help-block').hide();

    /**
     * display / hide help sections on click on the help button
     */
    $( "#help-btn" ).click( function() {
        $( "p.help-block" ).toggle();
    });

    /**
     * display or hide the fastlapc area
     */
    $( '#ipv4-enabled' ).change( function(){
        if( this.checked ){
            $( "#ipv4-area" ).slideDown();
        } else {
            $( "#ipv4-area" ).slideUp();
        }
    });

    /**
     * display or hide the fastlapc area
     */
    $( '#ipv6-enabled' ).change( function(){
        if( this.checked ){
            $( "#ipv6-area" ).slideDown();
        } else {
            $( "#ipv6-area" ).slideUp();
        }
    });

</script>