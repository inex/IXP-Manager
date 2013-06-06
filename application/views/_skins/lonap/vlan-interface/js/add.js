
var vlanid = 0;
$( "#vlanid" ).change( function() {

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
                
                if( vlanid == $( '#vlanid' ).val() && !$( "#ipv4addressid" ).val() ) {
                    $( "#ipv4addressid" ).val( $( "#preselectIPv4Address" ).val() );
                }

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
                
                if( vlanid == $( '#vlanid' ).val()  && !$( "#ipv6addressid" ).val() ){
                    $( "#ipv6addressid" ).val( $( "#preselectIPv6Address" ).val() );
                }
                
                ossChosenSet( "#ipv6addressid_osschzn", options, $( "#preselectIPv6Address" ).val() );
            } else {
                ossChosenSet( "#ipv6addressid_osschzn", options );
            }
        });

    }

    $("#vlanid").removeAttr( 'disabled' );

});

$(document).ready( function() {

    vlanid = $("#vlanid").val();

    // trigger a change on switch ID to populate ports
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
            
            if( !$( '#ipv6addressid' ).val() ){
                $.get("{genUrl controller='ipv6-address' action='ajax-get-next'}/schema/LONAP/custid/" + $('#preselectCustomer').val(), function( data ) {
                    if( data ){
                        bootbox.alert( "IPv6 address <em>" + data + "</em> was auto generated for this VlanInterface" );
                        $( '#ipv6addressid' ).val( data );
                    }
                })
            }
            $( '#ipv6details' ).slideDown();
        }
        else
            $( '#ipv6details' ).slideUp();

        $( window ).trigger( "resize" );
    });

});
