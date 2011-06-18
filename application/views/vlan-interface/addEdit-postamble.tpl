
{literal}
<script type="text/javascript"> /* <![CDATA[ */ 


$( function()
{
    $( "#vlanid" ).change( function()
    {
        $( "#vlanid" ).attr( 'disabled', 'disabled' );
        $( "#ipv4addressid").html( "<option>Please wait, loading data...</option>" );
        $( "#ipv6addressid").html( "<option>Please wait, loading data...</option>" );

        $.getJSON( "{/literal}{genUrl controller='vlan-interface' action='ajax-get-ipv4'}{literal}/id/"
                + $( "#preselectVlanInterface" ).val() + "/vlanid/" + $(this).val(), null, function( j ){

        	var options = "<option value=\"\">- select -</option>\n";

            for( var i = 0; i < j.length; i++ ) 
            	options += "<option value=\"" + j[i].id + "\">" + j[i].address + "</option>\n";

            $("#ipv4addressid").html( options );

            // do we have a preselect?
        	if( $( "#preselectIPv4Address" ).val() )
        		$( "#ipv4addressid" ).val( $( "#preselectIPv4Address" ).val() );
            
        });

        $.getJSON( "{/literal}{genUrl controller='vlan-interface' action='ajax-get-ipv6'}{literal}/id/"
                + $( "#preselectVlanInterface" ).val() + "/vlanid/" + $(this).val(), null, function( j ){

        	var options = "<option value=\"\">- select -</option>\n";

            for( var i = 0; i < j.length; i++ ) 
            	options += "<option value=\"" + j[i].id + "\">" + j[i].address + "</option>\n";

            $("#ipv6addressid").html( options );

            // do we have a preselect?
        	if( $( "#preselectIPv6Address" ).val() )
        		$( "#ipv6addressid" ).val( $( "#preselectIPv6Address" ).val() );
            
        });

        $("#vlanid").removeAttr( 'disabled' );
        
    });
});

$(document).ready(function(){

	// trigger a change on switch ID to populate ports
	$("#vlanid").trigger( 'change' );
});
	
/* ]]> */ </script> 
{/literal}
