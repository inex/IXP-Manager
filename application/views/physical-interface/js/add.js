
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

$(document).ready(function(){
    // trigger a change on switch ID to populate ports
    $("#switchid").trigger( 'change' );
});
