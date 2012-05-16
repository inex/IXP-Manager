
{literal}
<script type="text/javascript"> /* <![CDATA[ */


$( function()
{
    $( "#vlanid" ).change( function()
    {
        $( "#vlanid" ).attr( 'disabled', 'disabled' );

        if( $(this).val() != '0' ) {
            
            tt_chosenClear( "#ipv4addressid", "<option>Please wait, loading data...</option>" );
            tt_chosenClear( "#ipv6addressid", "<option>Please wait, loading data...</option>" );
            
            $.getJSON( "{/literal}{genUrl controller='vlan-interface' action='ajax-get-ipv4'}{literal}/id/"
                    + $( "#preselectVlanInterface" ).val() + "/vlanid/" + $(this).val(), null, function( j ){
    
            	var options = "<option value=\"\">- select -</option>\n";
    
                for( var i = 0; i < j.length; i++ )
                	options += "<option value=\"" + j[i].id + "\">" + j[i].address + "</option>\n";
    
                // do we have a preselect?
            	if( $( "#preselectIPv4Address" ).val() ) {
                    tt_chosenSet( "#ipv4addressid", options, $( "#preselectIPv4Address" ).val() );
            	} else {
                    tt_chosenSet( "#ipv4addressid", options );
            	}
            });
    
            $.getJSON( "{/literal}{genUrl controller='vlan-interface' action='ajax-get-ipv6'}{literal}/id/"
                    + $( "#preselectVlanInterface" ).val() + "/vlanid/" + $(this).val(), null, function( j ){
    
            	var options = "<option value=\"\">- select -</option>\n";
    
                for( var i = 0; i < j.length; i++ )
                	options += "<option value=\"" + j[i].id + "\">" + j[i].address + "</option>\n";
    
                // do we have a preselect?
            	if( $( "#preselectIPv6Address" ).val() ) {
                    tt_chosenSet( "#ipv6addressid", options, $( "#preselectIPv6Address" ).val() );
            	} else {
                    tt_chosenSet( "#ipv6addressid", options );
            	}
            });

        }
        
        $("#vlanid").removeAttr( 'disabled' );
        
    });

});

$(document).ready(function(){

	// trigger a change on switch ID to populate ports
	$("#vlanid").trigger( 'change' );

	$( '#ipv4enabled' ).on( 'click', function( event ){

		if( $( '#ipv4enabled' ).is(':checked') )
		    $( '#ipv4details' ).slideDown();
		else
		    $( '#ipv4details' ).slideUp();
	});
	
	$( '#ipv6enabled' ).on( 'click', function( event ){

		if( $( '#ipv6enabled' ).is(':checked') )
		    $( '#ipv6details' ).slideDown();
		else
		    $( '#ipv6details' ).slideUp();
	});

	if( $( '#ipv4enabled' ).is(':checked') )
	    $( '#ipv4details' ).show();
	
	if( $( '#ipv6enabled' ).is(':checked') )
	    $( '#ipv6details' ).show();
	
	
});
	
/* ]]> */ </script>
{/literal}
