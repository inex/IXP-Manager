
$( "#switchid" ).on( 'change', updateSwitchPort );
$( "#fn_switchid" ).on( 'change', updateSwitchPort );

function updateSwitchPort(){

    $( this ).attr( 'disabled', 'disabled' );
    
    var prep = "#";
    var type = "";  // was peering but we want to allow reseller and others
    if( $( this ).attr( "id" ).substr( 0, 3 ) == "fn_" )
    {
        prep += "fn_";
        type = "fanout";
    }
    
    if( $(this).val() != '0' ) {
        ossChosenClear( prep + "switchportid", "<option>Please wait, loading data...</option>" );

        $.getJSON( "{genUrl controller='switch-port' action='ajax-get'}/id/"
            + $( prep + "preselectPhysicalInterface" ).val() + "/type/" + type +  "/switchid/" + $(this).val(), function( j ) {

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

};

$(document).ready(function(){
    // trigger a change on switch ID to populate ports
    $("#switchid").trigger( 'change' );
    $("#fn_switchid").trigger( 'change' );
});
