
$( "#switchid" ).on( 'change', updateSwitchPort );
$( "#fn_switchid" ).on( 'change', updateSwitchPort );

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

$( "#ipv4bgpmd5secret" ).wrap( '<div class="input-append"></div>' );
$( "#ipv4bgpmd5secret" ).after( '<button class="btn" type="button" id="genipv4bgpmd5secret"><i class="icon-refresh"></i></button>');
$( "#genipv4bgpmd5secret" ).on( 'click', function( e ) {
    $( "#ipv4bgpmd5secret" ).val( randomString( 12 ) );
});

$( "#ipv6bgpmd5secret" ).wrap( '<div class="input-append"></div>' );
$( "#ipv6bgpmd5secret" ).after( '<button class="btn" type="button" id="genipv6bgpmd5secret"><i class="icon-retweet"></i></button>');
$( "#genipv6bgpmd5secret" ).on( 'click', function( e ) {
    $( "#ipv6bgpmd5secret" ).val( $( "#ipv4bgpmd5secret" ).val().trim() === '' ? randomString( 12 ) : $( "#ipv4bgpmd5secret" ).val() );
});

function updateSwitchPort(){

    $( this ).attr( 'disabled', 'disabled' );

    var prep = "#";
    var type = "peering";
    if( $( this ).attr( "id" ).substr( 0, 3 ) == "fn_" )
    {
        prep += "fn_";
        type = "fanout";
    }

    if( $(this).val() != '0' ) {
        ossChosenClear( prep + "switchportid", "<option>Please wait, loading data...</option>" );

        $.getJSON( "{genUrl controller='switch-port' action='ajax-get'}/id/" +
            $( prep + "preselectPhysicalInterface" ).val() + "/type/" + type +  "/switchid/" + $(this).val(), function( j ) {

            var options = "<option value=\"\">- select -</option>\n";

            for( var i = 0; i < j.length; i++ )
                options += "<option value=\"" + j[i].id + "\">" + j[i].name + " (" + j[i].type + ")</option>\n";

            // do we have a preselect?
            if( $( prep + "preselectSwitchPort" ).val() ) {
                ossChosenSet( prep + "switchportid", options, $( prep + "preselectSwitchPort" ).val() );
            } else {
                ossChosenSet( prep + "switchportid", options );
            }
        });
    }

    $( this).removeAttr( 'disabled' );

}

$( "#switchportid" ).change( function() {
    $( "#preselectSwitchPort" ).val( $( "#switchportid" ).val() );
});

$( "#fn_switchportid" ).change( function() {
    $( "#fn_preselectSwitchPort" ).val( $( "#fn_switchportid" ).val() );
});

$( "#vlanid" ).on( 'change', function( event ) {

    $( "#vlanid" ).attr( 'disabled', 'disabled' );

    if( $(this).val() != '0' ) {

        //ossChosenClear( "#ipv4addressid", "<option>Please wait, loading data...</option>" );
        //ossChosenClear( "#ipv6addressid", "<option>Please wait, loading data...</option>" );

        $.getJSON( "{genUrl controller='ipv4-address' action='ajax-get-for-vlan'}/vliid/" +
                $( "#preselectVlanInterface" ).val() + "/vlanid/" + $(this).val(), null, function( j ){

            var options = "<option value=\"\">- select -</option>\n";

            for( var i = 0; i < j.length; i++ )
                options += "<option value=\"" + j[i].address + "\">" + j[i].address + "</option>\n";

            // do we have a preselect?
            if( $( "#preselectIPv4Address" ).val() ) {
                ossChosenSet( "#ipv4addressid_osschzn", options, $( "#preselectIPv4Address" ).val() );
            } else {
                ossChosenSet( "#ipv4addressid_osschzn", options );
            }
        });

        $.getJSON( "{genUrl controller='ipv6-address' action='ajax-get-for-vlan'}/vliid/" +
                $( "#preselectVlanInterface" ).val() + "/vlanid/" + $(this).val(), null, function( j ){

            var options = "<option value=\"\">- select -</option>\n";

            for( var i = 0; i < j.length; i++ )
                options += "<option value=\"" + j[i].address + "\">" + j[i].address + "</option>\n";

            // do we have a preselect?
            if( $( "#preselectIPv6Address" ).val() ) {
                ossChosenSet( "#ipv6addressid_osschzn", options, $( "#preselectIPv6Address" ).val() );
            } else {
                ossChosenSet( "#ipv6addressid_osschzn", options );
            }
        });

    }

    $("#vlanid").removeAttr( 'disabled' );

});


$( "#ipv4addressid" ).change( function() {
    $( "#preselectIPv4Address" ).val( $( "#ipv4addressid" ).val() );
});

$( "#ipv6addressid" ).change( function() {
    $( "#preselectIPv6Address" ).val( $( "#ipv6addressid" ).val() );
});

$(document).ready( function() {

    // trigger a change on selects to populate dependant fields
    $("#switchid").trigger( 'change' );
    $("#custid").trigger( 'change' );
    $("#vlanid").trigger( 'change' );


    $(
        '#ipv4enabled' ).on( 'click', function( event ){

        if( $( '#ipv4enabled' ).is(':checked') )
            $( '#ipv4details' ).slideDown();
        else
            $( '#ipv4details' ).slideUp();

        $( window ).trigger( "resize" );
    });

    $( '#ipv6enabled' ).on( 'click', function( event ){

        if( $( '#ipv6enabled' ).is(':checked') )
            $( '#ipv6details' ).slideDown();
        else
            $( '#ipv6details' ).slideUp();

        $( window ).trigger( "resize" );
    });

    if( $( '#ipv4enabled' ).is(':checked') )
        $( '#ipv4details' ).show();

    if( $( '#ipv6enabled' ).is(':checked') )
        $( '#ipv6details' ).show();

    {if $resellerMode }
        $("#fn_switchid").trigger( 'change' );

        $( '#fanout' ).on( 'click', function( event ){
            if( $( this ).prop( 'checked' ) )
                $( '#fanoutdetails' ).slideDown();
            else
                $( '#fanoutdetails' ).slideUp();
        });

        if( $( '#fanout' ).prop( 'checked' ) )
            $( '#fanoutdetails' ).show();

        $( '#custid' ).on( 'change', function(){
            var resoldCusts = {$resoldCusts};
            if( resoldCusts[ $(this).val() ] === undefined )
            {
                $( '#fanout' ).removeAttr( "checked" );
                $( '#fanoutdetails' ).slideUp();
                $( '#fanoutbox' ).slideUp();
            }
            else
                $( '#fanoutbox' ).slideDown();
        });
    {/if}
});
