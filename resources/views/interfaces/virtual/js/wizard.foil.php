
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

    setIPVx();

});

$( "#switch" ).change( function(){
    $( "#switch-port" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );

    switchId = $( "#switch" ).val();

    // ask what is that ?
    var type = "peering";
    url = "<?= url( '/api/v4/switcher' )?>/" + switchId + "/switch-port-not-assign-to-pi";

    $.ajax( url , {
        data: {type : type },
        type: 'POST'
    })
        .done( function( data ) {
            var options = "<option value=\"\">Choose a switch port</option>\n";
            $.each( data.listPorts, function( key, value ){
                options += "<option value=\"" + value.id + "\">" + value.name + " (" + value.type + ")</option>\n";
            });
            $( "#switch-port" ).html( options );
        })
        .fail( function() {
            throw new Error( "Error running ajax query for api/v4/switch/$id/switch-port-not-assign-to-pi" );
            alert( "Error running ajax query for api/v4/switch/$id/switch-port-not-assign-to-pi" );
        })
        .always( function() {
            $( "#switch-port" ).trigger( "chosen:updated" );
        });
});




$( "#vlan" ).on( 'change', function( event ) {

    setIPVx();

});

function setIPVx(){
    if( $("#vlan").val() ) {

        $( "#ipv4-address" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );
        $( "#ipv6-address" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );

        vlanid = $("#vlan").val();

        $.ajax( "<?= url( '/api/v4/vlan' )?>/" + vlanid + "/ipv-address" , {
            data: {vliid: '', ipType: <?= \Entities\Router::PROTOCOL_IPV4 ?>  },
            type: 'POST'
        })
            .done( function( data ) {
                var options = "<option value=\"\">Choose an IPv4</option>\n";
                $.each( data.ipvList, function( key, value ){
                    options += "<option value=\"" + value.address + "\">" + value.address + " </option>\n";
                });
                $( "#ipv4-address" ).html( options );
            })
            .fail( function() {
                throw new Error( "Error running ajax query for api/v4/vlan/$id/ipv-address" );
                alert( "Error running ajax query for api/v4/vlan/$id/ipv-address" );
            })
            .always( function() {
                $( "#ipv4-address" ).trigger( "chosen:updated" );
            });


        $.ajax( "<?= url( '/api/v4/vlan' )?>/" + vlanid + "/ipv-address" , {
            data: {vliid: '', ipType: <?= \Entities\Router::PROTOCOL_IPV6 ?>  },
            type: 'POST'
        })
            .done( function( data ) {
                var options = "<option value=\"\">Choose an IPv6</option>\n";
                $.each( data.ipvList, function( key, value ){
                    options += "<option value=\"" + value.address + "\">" + value.address + " </option>\n";
                });
                $( "#ipv6-address" ).html( options );
            })
            .fail( function() {
                throw new Error( "Error running ajax query for api/v4/vlan/$id/ipv-address" );
                alert( "Error running ajax query for api/v4/vlan/$id/ipv-address" );
            })
            .always( function() {
                $( "#ipv6-address" ).trigger( "chosen:updated" );
            });


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