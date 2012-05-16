
{literal}
<script type="text/javascript"> /* <![CDATA[ */


$( function()
{
    $( "#switch_id" ).change( function()
    {
        $( "#switch_id" ).attr( 'disabled', 'disabled' );

        if( $(this).val() != '0' ) {
            tt_chosenClear( "#switchportid", "<option>Please wait, loading data...</option>" );

            $.getJSON( "{/literal}{genUrl controller='physical-interface' action='ajax-get-ports'}{literal}/id/"
                    + $( "#preselectPhysicalInterface" ).val() + "/switchid/" + $(this).val(), null, function( j ){
    
            	var options = "<option value=\"\">- select -</option>\n";
    
                for( var i = 0; i < j.length; i++ )
                	options += "<option value=\"" + j[i].id + "\">" + j[i].name + " (" + j[i].type + ")</option>\n";
    
                // do we have a preselect?
            	if( $( "#preselectSwitchPort" ).val() ) {
                    tt_chosenSet( "#switchportid", options, $( "#preselectSwitchPort" ).val() );
            	} else {
                    tt_chosenSet( "#switchportid", options );
            	}
            });
        }
        
        $("#switch_id").removeAttr( 'disabled' );
        
    });
});

$(document).ready(function(){

	// trigger a change on switch ID to populate ports
	$("#switch_id").trigger( 'change' );
});
	
/* ]]> */ </script>
{/literal}
