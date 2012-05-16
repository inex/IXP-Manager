{include file="header.tpl"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="{genUrl controller='vlan-interface' action='list'}">Interfaces</a> <span class="divider">/</span>
    </li>
    <li class="active">
        Wizard Add
    </li>
</ul>

{$form}


<script type="text/javascript">

$( "#switch_id" ).on( 'change', function( event ){
    $( "#switch_id" ).attr( 'disabled', 'disabled' );

    if( $(this).val() != '0' ) {
        tt_chosenClear( "#switchportid", "<option>Please wait, loading data...</option>" );

        $.getJSON( "{genUrl controller='physical-interface' action='ajax-get-ports'}/id/"
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

$( "#switchportid" ).change( function()
{
	$( "#preselectSwitchPort" ).val( $( "#switchportid" ).val() );
});

$( "#vlanid" ).on( 'change', function( event ) {
    $( "#vlanid" ).attr( 'disabled', 'disabled' );

    if( $(this).val() != '0' ) {
        
        tt_chosenClear( "#ipv4addressid", "<option>Please wait, loading data...</option>" );
        tt_chosenClear( "#ipv6addressid", "<option>Please wait, loading data...</option>" );
        
        $.getJSON( "{genUrl controller='vlan-interface' action='ajax-get-ipv4'}/id/"
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

        $.getJSON( "{genUrl controller='vlan-interface' action='ajax-get-ipv6'}/id/"
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


$( "#ipv4addressid" ).change( function()
{
	$( "#preselectIPv4Address" ).val( $( "#ipv4addressid" ).val() );
});

$( "#ipv6addressid" ).change( function()
{
	$( "#preselectIPv6Address" ).val( $( "#ipv6addressid" ).val() );
});

$(document).ready(function(){

	// trigger a change on selects to populate dependant fields
	$("#switch_id").trigger( 'change' );
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

</script>

{include file="footer.tpl"}
