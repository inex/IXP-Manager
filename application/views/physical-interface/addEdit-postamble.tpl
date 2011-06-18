
{literal}
<script type="text/javascript"> /* <![CDATA[ */ 


$( function()
{
    $( "#switch_id" ).change( function()
    {
        $( "#switch_id" ).attr( 'disabled', 'disabled' );
        $( "#switchportid").html( "<option>Please wait, loading data...</option>" );

        $.getJSON( "{/literal}{genUrl controller='physical-interface' action='ajax-get-ports'}{literal}/id/"
                + $( "#preselectPhysicalInterface" ).val() + "/switchid/" + $(this).val(), null, function( j ){

        	var options = "<option value=\"\">- select -</option>\n";

            for( var i = 0; i < j.length; i++ ) 
            	options += "<option value=\"" + j[i].id + "\">" + j[i].name + " (" + j[i].type + ")</option>\n";

            $("#switchportid").html( options );
            $("#switch_id").removeAttr( 'disabled' );

            // do we have a preselect?
        	if( $( "#preselectSwitchPort" ).val() )
        		$( "#switchportid" ).val( $( "#preselectSwitchPort" ).val() );
            
        })
    })
});

$(document).ready(function(){

	// trigger a change on switch ID to populate ports
	$("#switch_id").trigger( 'change' );
});
	
/* ]]> */ </script> 
{/literal}
