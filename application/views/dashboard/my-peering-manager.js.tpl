<script type="text/javascript">

$(document).ready( function(){ldelim}

    {if $email}
	    showPeeringRequestDialog( {$email} );
    {/if}

    // If the user has not been here before, show them the instructions
    {if isset( $showInstructions ) and $showInstructions}
        $.fn.colorbox( {ldelim}
            width:"750px", height: "600px",
            href:'{genUrl controller="dashboard" action="static" page="help-my-peering-manager"}'
        {rdelim});
    {/if}

    	
	oTable = $('#myPeeringMatrixTable').dataTable({ldelim}

        "aaSorting": [[ {if $ipv6[$customer.id]}4{else}3{/if}, 'asc' ]],
		"bJQueryUI": true,
		"bPaginate": false,
		"aoColumns": [
    		null,
      		null,
      		null,
            {if $ipv6[$customer.id]}null,{/if} 
      		null,
      		null,
      		null,
        	{ldelim} "sWidth": "50px" {rdelim},
      		null,
      		null
    	]
	{rdelim}).show();

{literal}

	// bind notes save to save action
	$('#notes-save').click( saveNotes );

		
});


$( "#peeringNotesDialog" ).dialog({
	width: 600,
	autoOpen: false,
	title: "Peering Notes",
	modal: true
});

$( "#sendPeeringRequestDialog" ).dialog({
	width: 800,
	autoOpen: false,
	title: "Member to Member Peering Request",
	modal: true,
	buttons: {
		"Send": sendPeeringRequest,
		"Cancel": function() {
			$( this ).dialog( "close" );
		}
	}
});


function editNotes( pId )
{
	$( "#ajaxMessage" ).hide();	

    // Render the Dialog
    $( "#peeringNotesDialog-id" ).val( 0 );
    $( "#peeringNotesDialog-member" ).html( 'Loading...' );
    $( "#peeringNotesDialog-notes" ).val( 'Loading...' ).show();

    $.getJSON( "{/literal}{genUrl controller='dashboard' action='my-peering-matrix-notes'}{literal}/id/" + pId,
            function( data ) {
                $( "#peeringNotesDialog-id" ).val( pId );
                $( "#peeringNotesDialog-member" ).html( data['name'] );
                $( "#peeringNotesDialog-notes" ).val( data['notes'] );

                $( "#peeringNotesDialog-notes" ).focus();
                //$( "#peeringNotesDialog-notes" ).setSelectionRange( data['pos'], data['pos'] );
            }
    );

	$( '#peeringNotesDialog' ).dialog( 'open' );

}

function saveNotes()
{
    pid    = $( "#peeringNotesDialog-id" ).val();
    pnotes = $( "#peeringNotesDialog-notes" ).val();

    $( '#peeringNotesDialog-notes' ).hide();
    $( '#peeringNotesDialog-member' ).html( 'Saving...' );
    
    $.post(
		"{/literal}{genUrl controller=dashboard action='my-peering-matrix-notes' save=1}{literal}",
		$('#peeringNotesForm').serialize(), inexMessage, 'json'
    );
    
	$( '#peeringNotesDialog' ).dialog( 'close' );

    return false;
}



function showPeeringRequestDialog( pId )
{
	$( "#ajaxMessage" ).hide();	

	// Render the Dialog
	$( "#sendPeeringRequestThrobber" ).hide();
    $( "#sendPeeringRequestDialog-id" ).val( 0 );
    $( "#sendPeeringRequestDialog-to" ).val( '...' );
    $( "#sendPeeringRequestDialog-from" ).val( '...' );
    $( "#sendPeeringRequestDialog-bcc" ).val( '...' );
    $( "#sendPeeringRequestDialog-subject" ).val( '...' );
    $( "#sendPeeringRequestDialog-message" ).val( 'Loading...' );
    $( "#sendPeeringRequestForm" ).show();

	$.getJSON( "{/literal}{genUrl controller='dashboard' action='my-peering-matrix-email'}{literal}/id/" + pId,
	        function( data ) {
		        $( "#sendPeeringRequestDialog-id" ).val( pId );
		        $( "#sendPeeringRequestDialog-to" ).val( data['to'] );
                $( "#sendPeeringRequestDialog-from" ).val( data['from'] );
                $( "#sendPeeringRequestDialog-bcc" ).val( data['bcc'] );
                $( "#sendPeeringRequestDialog-subject" ).val( data['subject'] );
                $( "#sendPeeringRequestDialog-message" ).val( data['message'] );
            }
    );

	$('#sendPeeringRequestDialog').dialog( "open" );

}

function sendPeeringRequest()
{
    $( "#sendPeeringRequestForm" ).hide();
    $( "#sendPeeringRequestThrobber" ).show();
    
    $.post(
		"{/literal}{genUrl controller=dashboard action='my-peering-matrix-email' send=1}{literal}",
		$('#sendPeeringRequestForm').serialize(), inexMessage, 'json'
    );
    
	$( '#sendPeeringRequestDialog' ).dialog( 'close' );

    return false;
}


function inexMessage( data, textStatus, jqXHR )
{
    if( data['status'] == '1' ) {
        $( "#ajaxMessage" ).html( '<div class="message message-success">' + data['message'] + '</div>' ).show();
    } else {
        $( "#ajaxMessage" ).html( '<div class="message message-error">' + data['message'] + '</div>' ).show();
    }

    // was there a comment added as part of this action?
    if( typeof data['commentAdded'] != undefined && data['commentAdded'] == '1' )
    {
        $('#myPeerNotes-' + data['cid'] ).empty();
        $('#myPeerNotes-' + data['cid'] ).append(
            '<img src="{/literal}{genUrl}{literal}/images/22x22/note.png" border="0" width="22" height="22" border="0" alt="Notes" />'
        );
    }

}


function changeMyPeeredState( pId, pVlan )
{
    $('#myPeeredState-' + pId).empty();
    $('#myPeeredState-' + pId).append(
        '<img src="{/literal}{genUrl}{literal}/images/throbber-small.gif" width="16" height="16" border="0" alt="[...]" />'
    );

    $.getJSON( "{/literal}{genUrl controller='dashboard' action='my-peering-matrix-peered-state'}{literal}/id/" + pId + "/vlan/" + pVlan,
            function( data ) {
                $('#myPeeredState-' + pId).empty();
                switch( data['newstate'] )
                {
                    case 0:
                        $('#myPeeredState-' + pId).append(
                            '<img src="{/literal}{genUrl}{literal}/images/22x22/no.png" width="22" height="22" border="0" alt="NO" />'
                        );
                        break;
                    case 1:
                        $('#myPeeredState-' + pId).append(
                            '<img src="{/literal}{genUrl}{literal}/images/22x22/yes.png" width="22" height="22" border="0" alt="YES" />'
                        );
                        break;
                    case 2:
                        $('#myPeeredState-' + pId).append(
                            '<img src="{/literal}{genUrl}{literal}/images/22x22/waiting.png" width="22" height="22" border="0" alt="WAITING" />'
                        );
                        break;
                    case 3:
                        $('#myPeeredState-' + pId).append(
                            '<img src="{/literal}{genUrl}{literal}/images/22x22/never.png" width="22" height="22" border="0" alt="NEVER" />'
                        );
                        break;
                    case 4:
                        $('#myPeeredState-' + pId).append(
                            '<img src="{/literal}{genUrl}{literal}/images/22x22/unknown.png" width="22" height="22" border="0" alt="UNKNOWN" />'
                        );
                        break;
                    default:
                        $('#myPeeredState-' + pId).append(
                            '<img src="{/literal}{genUrl}{literal}/images/22x22/unknown.png" width="22" height="22" border="0" alt="UNKNOWN" />'
                        );
                        break;
                }
            }
    );

}


function changeIPv6PeeredState( pId, pVlan )
{
    $('#ipv6PeeredState-' + pId).empty();
    $('#ipv6PeeredState-' + pId).append(
        '<img src="{/literal}{genUrl}{literal}/images/throbber-small.gif" width="16" height="16" border="0" alt="[...]" />'
    );

    $.getJSON( "{/literal}{genUrl controller='dashboard' action='my-peering-matrix-peered-state'}{literal}/type/ipv6/id/" + pId + "/vlan/" + pVlan,
            function( data ) {
                $('#ipv6PeeredState-' + pId).empty();
                switch( data['newstate'] )
                {
                    case 1:
                        $('#ipv6PeeredState-' + pId).append(
                            '<img src="{/literal}{genUrl}{literal}/images/22x22/face-smile-big.png" alt="PEERED OVER IPv6"      title="Peered over IPv6"     width="22" height="22" border="0" />'
                        );
                        break;
                    case 0:
                        $('#ipv6PeeredState-' + pId).append(
                            '<img src="{/literal}{genUrl}{literal}/images/22x22/face-crying.png"    alt="NOT PEERED OVER IPv6" title="Not peered over IPv6" width="22" height="22" border="0" />'
                        );
                        break;
                }
            }
    );

}



{/literal}
</script>
