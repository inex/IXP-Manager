{include file="header.tpl"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="{genUrl controller='vlan-interface' action='list'}">VLAN Interfaces</a> <span class="divider">/</span>
    </li>
    <li class="active">
        Quick Add
    </li>
</ul>

{$form}


{literal}
<script type="text/javascript"> /* <![CDATA[ */


$( function()
{
    $( "#switch_id" ).change( function()
    {
        $( "#switch_id" ).attr( 'disabled', 'disabled' );
        $( "#switchportid").html( "<option>Please wait, loading data...</option>" );

        $.getJSON( "{/literal}{genUrl controller='physical-interface' action='ajax-get-ports'}{literal}/switchid/"
                + $(this).val(), null, function( j ){

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
    
    $( "#switchportid" ).change( function()
    {
    	$( "#preselectSwitchPort" ).val( $( "#switchportid" ).val() );
    });

    $( "#vlanid" ).change( function()
    {
        $( "#vlanid" ).attr( 'disabled', 'disabled' );
        $( "#ipv4addressid").html( "<option>Please wait, loading data...</option>" );
        $( "#ipv6addressid").html( "<option>Please wait, loading data...</option>" );

        $.getJSON( "{/literal}{genUrl controller='vlan-interface' action='ajax-get-ipv4'}{literal}/vlanid/"
                + $(this).val(), null, function( j ){

        	var options = "<option value=\"\">- select -</option>\n";

            for( var i = 0; i < j.length; i++ )
            	options += "<option value=\"" + j[i].id + "\">" + j[i].address + "</option>\n";

            $("#ipv4addressid").html( options );

            // do we have a preselect?
        	if( $( "#preselectIPv4Address" ).val() )
        		$( "#ipv4addressid" ).val( $( "#preselectIPv4Address" ).val() );
            
        });

        $.getJSON( "{/literal}{genUrl controller='vlan-interface' action='ajax-get-ipv6'}{literal}/vlanid/"
                + $(this).val(), null, function( j ){

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


    $( "#ipv4addressid" ).change( function()
    {
    	$( "#preselectIPv4Address" ).val( $( "#ipv4addressid" ).val() );
    });

    $( "#ipv6addressid" ).change( function()
	{
    	$( "#preselectIPv6Address" ).val( $( "#ipv6addressid" ).val() );
    });


});

$(document).ready(function(){

	// trigger a change on selects to populate dependant fields
	$("#switch_id").trigger( 'change' );
	$("#vlanid").trigger( 'change' );
});


/*
$(document).ready( function() {
	
    $( '#ipv6address' ).bind( 'focus', function() {

        asn  = jQuery.trim( $( '#custid option:selected' ).text() );
        v6   = jQuery.trim( $( '#ipv6address' ).val() );

		asn = asn.substring( asn.indexOf( '[ASN' ) + 4 );
		asn = asn.substring( 0, asn.length - 1 );

        ipv6_start = '2001:7f8:1c:3000::';
		ipv6_end   = ':1';
        
        if( asn != '' && v6 == '' )
        {
            if( asn.length > 4 )
            {
                b = asn.substring( 0, asn.length - 4 ) + ':';
                a = asn.substring( asn.length - 4 );
            }
            else
            {
                b = '';
                a = asn;
            }
            
			$( '#ipv6address' ).val( ipv6_start + b + a + ipv6_end );
        }
    });
});
*/
/* ]]> */ </script>
{/literal}



{include file="footer.tpl"}
