
$( "#switchid" ).on( 'change', function( event ) {
    $( "#switchid" ).attr( 'disabled', 'disabled' );

    if( $(this).val() != '0' ) {
        ossChosenClear( "#switchportid", "<option>Please wait, loading data...</option>" );

        $.getJSON( "{genUrl controller='switch-port' action='ajax-get'}/id/"
                + $( "#preselectPhysicalInterface" ).val() + "/switchid/" + $(this).val(), function( j ) {

            var options = "<option value=\"\">- select -</option>\n";

            for( var i = 0; i < j.length; i++ )
                options += "<option value=\"" + j[i].id + "\">" + j[i].name + " (" + j[i].type + ")</option>\n";

            // do we have a preselect?
            if( $( "#preselectSwitchPort" ).val() ) {
                ossChosenSet( "#switchportid", options, $( "#preselectSwitchPort" ).val() );
            } else {
                ossChosenSet( "#switchportid", options );
            }
        });
    }

    $("#switchid").removeAttr( 'disabled' );
});


$( "#switchportid" ).change( function() {
    $( "#preselectSwitchPort" ).val( $( "#switchportid" ).val() );
});


$( "#vlanid" ).on( 'change', function( event ) {

    $( "#vlanid" ).attr( 'disabled', 'disabled' );

    if( $(this).val() != '0' ) {

        //ossChosenClear( "#ipv4addressid", "<option>Please wait, loading data...</option>" );
        //ossChosenClear( "#ipv6addressid", "<option>Please wait, loading data...</option>" );

        $.getJSON( "{genUrl controller='ipv4-address' action='ajax-get-for-vlan'}/vliid/"
                + $( "#preselectVlanInterface" ).val() + "/vlanid/" + $(this).val(), null, function( j ){

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

        $.getJSON( "{genUrl controller='ipv6-address' action='ajax-get-for-vlan'}/vliid/"
                + $( "#preselectVlanInterface" ).val() + "/vlanid/" + $(this).val(), null, function( j ){

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
    $("#switch_id").trigger( 'change' );
    $("#vlanid").trigger( 'change' );

    $( '#ipv4enabled' ).on( 'click', function( event ){

        if( $( '#ipv4enabled' ).is(':checked') )
            $( '#ipv4details' ).slideDown();
        else
            $( '#ipv4details' ).slideUp();

        $( window ).trigger( "resize" );
    });

    $( '#ipv6enabled' ).on( 'click', function( event ){
        if( $( '#ipv6enabled' ).is(':checked') ){
            if( $( '#custid' ).val() && !$( '#ipv6addressid' ).val() ){
                $.get("{genUrl controller='ipv6-address' action='ajax-get-next'}/schema/LONAP/custid/" + $('#custid').val(), function( data ) {
                    if( data ){
                        bootbox.alert( "IPv6 address <em>" + data + "</em> was auto generated for this VlanInterface" );
                        $( '#ipv6addressid' ).val( data );
                    }
                });
            }
            $( '#ipv6details' ).slideDown();
        }
        else
            $( '#ipv6details' ).slideUp();

        $( window ).trigger( "resize" );
    });

    $( '#custid' ).on( 'change', function(){
        if( $( '#ipv6enabled' ).is(':checked') ){
            $.get("{genUrl controller='ipv6-address' action='ajax-get-next'}/schema/LONAP/custid/" + $('#custid').val(), function( data ) {
                if( data ){
                    bootbox.alert( "IPv6 address <em>" + data + "</em> was auto generated for this VlanInterface" );
                    $( '#ipv6addressid' ).val( data );
                }
            });
        }
    });

    if( $( '#ipv4enabled' ).is(':checked') ){
        $( '#ipv4details' ).show();
        $( window ).trigger( "resize" );
    }

    if( $( '#ipv6enabled' ).is(':checked') )
    {
        $( '#ipv6details' ).show();
        $( window ).trigger( "resize" );
    }

});
