{tmplinclude file="header.tpl" pageTitle="IXP Manager :: My Peering Manager"}

<link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/autocomplete/assets/skins/sam/autocomplete.css" />
<link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/paginator/assets/skins/sam/paginator.css" />
<link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/datatable/assets/skins/sam/datatable.css">
<link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/menu/assets/skins/sam/menu.css">
<link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/datatable/assets/skins/sam/datatable.css">

<script type="text/javascript" src="{genUrl}/css/yui/build/json/json-min.js"></script>
<script type="text/javascript" src="{genUrl}/css/yui/build/connection/connection-min.js"></script>
<script type="text/javascript" src="{genUrl}/css/yui/build/get/get-min.js"></script>
<script type="text/javascript" src="{genUrl}/css/yui/build/autocomplete/autocomplete-min.js"></script>
<script type="text/javascript" src="{genUrl}/css/yui/build/paginator/paginator-min.js"></script>
<script type="text/javascript" src="{genUrl}/css/yui/build/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="{genUrl}/css/yui/build/datatable/datatable-min.js"></script>
<script type="text/javascript" src="{genUrl}/css/yui/build/menu/menu-min.js"></script>



<div class="yui-g">

<table class="adminheading" border="0">
<tr>
    <th class="Peering">
        My Peering Manager
    </th>
</tr>
</table>

<p>
<form action="{genUrl controller="dashboard" action="my-peering-matrix"}" method="post">
<table>
<tr>
    <td width="20"></td>
    <td valign="middle"><strong>Peering VLAN:</strong></td>
    <td>
        <select name="vlan" onchange="this.form.submit();">
            {foreach from=$vlans item=v}
                <option value="{$v.number}" {if $vlan eq $v.number}selected{/if}>{$v.name}</option>
            {/foreach}
        </select>
    </td>
    <td>
        &nbsp;&nbsp;&nbsp;<img src="{genUrl}/images/22x22/help.png" width="22" height="22"
            border="0" alt="[Help]" title="My Peering Manager Instructions" id="help-icon" />
        <script type="text/javascript">
            $('#help-icon').colorbox( {ldelim}
                width:"750px", height: "600px",
                href:'{genUrl controller="dashboard" action="static" page="help-my-peering-manager"}'
            {rdelim} );
        </script>
    </td>
    <td>
    Download as:
        <a href="{genUrl controller="dashboard" action="my-peering-matrix" as="ascii" vlan=$vlan}">ASCII</a>
        <a href="{genUrl controller="dashboard" action="my-peering-matrix" as="csv" vlan=$vlan}">CSV</a>
        <a href="{genUrl controller="dashboard" action="my-peering-matrix" as="php" vlan=$vlan}">PHP</a>
    </td>
</tr>
</table>
</form>
</p>

{tmplinclude file="message.tpl"}

<div id='ajaxMessage'></div>

<div id="myPeeringMatrixContainer">

<table border="0" id="myPeeringMatrixTable">

    <thead>
    <tr>
        <th>Member</th>
        <th>VLAN</th>
        <th>Name</th>
        <th>ASN</th>
        <th>Policy</th>
        <th>Peering Contact</th>
        <th>Member Since</th>
    </tr>
    </thead>

{foreach from=$matrix item=m}

	{if $m.MyPeeringMatrix.0.dead eq 0}
	
        <tr>
            <td>
                <a href="#s{$m.Y_Cust.id}" onClick="changeMyPeeredState( '{$m.Y_Cust.id}', {$vlan} ); return false;">
                    <div id="myPeeredState-{$m.Y_Cust.id}">
                        {if $m.MyPeeringMatrix.0.peered eq 'YES'}
    		                <img src="{genUrl}/images/22x22/yes.png" alt="YES" title="Peered"
    		                    width="22" height="22" border="0" />
    		            {elseif $m.MyPeeringMatrix.0.peered eq 'NO'}
    		                <img src="{genUrl}/images/22x22/no.png" alt="NO" title="Not peered"
    		                    width="22" height="22" border="0" />
    		            {elseif $m.MyPeeringMatrix.0.peered eq 'WAITING'}
    		                <img src="{genUrl}/images/22x22/waiting.png" alt="Waiting"
    		                    title="WAITING" width="22" height="22" border="0" />
    		            {elseif $m.MyPeeringMatrix.0.peered eq 'NEVER'}
    		                <img src="{genUrl}/images/22x22/never.png" alt="Never"
    		                    title="NEVER" width="22" height="22" border="0" />
    		            {else}
    		                <img src="{genUrl}/images/22x22/unknown.png" alt="Unknown" title="Unknown"
    		                    width="22" height="22" border="0" />
    		            {/if}
                    </div>
    		    </a>
            </td>
            <td>
                {if $m.peering_status eq 'YES'}
                    <img src="{genUrl}/images/yes.gif" alt="YES" title="Peered" width="21" height="21" border="0" />
                {elseif $m.peering_status eq 'NO'}
                    <img src="{genUrl}/images/no.gif" alt="NO" title="Not peered" width="21" height="21" border="0" />
                {elseif $m.peering_status eq 'INCONSISTENT_X'}
                    <img src="{genUrl}/images/inconsistent1.gif" alt="INCONSISTENT_X" title="INCONSISTENT_X" width="21" height="21" border="0" />
                {elseif $m.peering_status eq 'YES'}
                    <img src="{genUrl}/images/inconsistent2.gif" alt="INCONSISTENT_Y" title="INCONSISTENT_Y" width="21" height="21" border="0" />
                {/if}
            </td>
    
            <td>
                {if $rsclient[$m.Y_Cust.id]}
                    <img src="{genUrl}/images/22x22/im-user.png"          alt="Y" width="22" height="22" border="0" />
                {else}
                     <img src="{genUrl}/images/22x22/im-user-offline.png" alt="N" width="22" height="22" border="0" />
                {/if}
            </td>
    
            {* If I'm IPv6 enabled *}
            {if $ipv6[$customer.id]}
    	        <td>
    	            {if $ipv6[$m.Y_Cust.id]}
    	                <a href="#v6{$m.Y_Cust.id}" onClick="changeIPv6PeeredState( '{$m.Y_Cust.id}', {$vlan} ); return false;">
                            <div id="ipv6PeeredState-{$m.Y_Cust.id}">
        	                    {if $m.MyPeeringMatrix.0.ipv6}
    	                            <img src="{genUrl}/images/22x22/face-smile-big.png" alt="PEERED OVER IPv6"      title="Peered over IPv6"     width="22" height="22" border="0" />
    	                        {else}
    	                            <img src="{genUrl}/images/22x22/face-crying.png"    alt="NOT PEERED OVER IPv6" title="Not peered over IPv6" width="22" height="22" border="0" />
    	                        {/if}
    	                    </div>
    	                </a>
    	            {/if}
    	        </td>
            {/if}
    
            <td>{$m.Y_Cust.name}</td>
            <td>{$m.y_as|asnumber}</td>
            <td>{$m.Y_Cust.peeringpolicy}</td>
            <td>
                <a href="#e{$m.Y_Cust.id}" onClick="showPeeringRequestDialog( '{$m.Y_Cust.id}' ); return false;">
                    {$m.Y_Cust.peeringemail}
                </a>
            </td>
            <td>{$m.Y_Cust.datejoin}</td>
            <td>
                <a href="#n{$m.Y_Cust.id}" onClick="editNotes( '{$m.Y_Cust.id}' ); return false;">
                    <div id="myPeerNotes-{$m.Y_Cust.id}">
                        {if $m.MyPeeringMatrix.0.notes_id neq null}
                            <img src="{genUrl}/images/22x22/note.png" border="0" width="22" height="22" border="0" alt="Notes" />
                        {else}
                            <img src="{genUrl}/images/22x22/no-note.png" border="0" width="22" height="22" border="0" alt="Notes" />
                        {/if}
                    </div>
                </a>
            </td>
        </tr>
	
	{/if}
	
{/foreach}

</table>


</div>

<script type="text/javascript">
{literal}
    var myPeeringMatrixDataSource = new YAHOO.util.DataSource( YAHOO.util.Dom.get( "myPeeringMatrixTable" ) );
    myPeeringMatrixDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;

    myPeeringMatrixDataSource.responseSchema = {
        fields: [
            {key:'MPeered'},
            {key:'VPeered'},
            {key:'RSClient'},
            {/literal}{if $ipv6[$customer.id]}
            	{ldelim}key:'IPv6'{rdelim},
            {/if}{literal}
            {key:'Name'},
            {key:'ASN'},
            {key:'Policy'},
            {key:'Peering Contact'},
            {key:'Member Since', parser:'date'},
            {key:'Notes'}
        ]
    };

    var myPeeringMatrixColumnDefs = [
        {key:'MPeered',  sortable: true, label:'State'},
        {key:'VPeered',  sortable: true, label:'PM'},
        {key:'RSClient', sortable: true, label:'RS'},
        {/literal}{if $ipv6[$customer.id]}
            {ldelim}key:'IPv6', sortable: true, label:'IPv6'{rdelim},
        {/if}{literal}
        {key:'Name', sortable: true},
        {key:'ASN', sortable: true},
        {key:'Policy', sortable: true},
        {key:'Peering Contact', sortable: true},
        {key:'Member Since', sortable: true, formatter:'date'},
        {key:'Notes', sortable: true, label:''}
    ];

    var myPeeringMatrixDataTable =
        new YAHOO.widget.DataTable(
        	    "myPeeringMatrixContainer", myPeeringMatrixColumnDefs, myPeeringMatrixDataSource,
        	    { sortedBy: { key: 'ASN', dir: YAHOO.widget.DataTable.CLASS_ASC } }
        );
{/literal}
</script>


<div id="sendPeeringRequestDialog">
    <div class="hd">Member to Member Peering Request</div>
    <div class="bd">
        <form method="POST" action="{genUrl controller=dashboard action='my-peering-matrix-email' send=1}">

            <table border="0">
            <tr>
                <td align="right">
                    <strong>From:</strong>&nbsp;&nbsp;
                </td>
                <td>
                    <input id="sendPeeringRequestDialog-from" type="text" name="from"
                        value="{$m.X_Cust.peeringemail}"
                        maxlength="254" size="60"  readonly="1"
                    />
                </td>
            </tr>

            <tr>
                <td align="right">
                    <strong>To:</strong>&nbsp;&nbsp;
                </td>
                <td>
                    <input id="sendPeeringRequestDialog-to" type="text" name="to" value="Loading..." maxlength="254" size="60"  readonly="1" />
                </td>
            </tr>

            <tr>
                <td align="right">
                    <strong>BCC:</strong>&nbsp;&nbsp;
                </td>
                <td>
                    <input id="sendPeeringRequestDialog-bcc" type="text" name="bcc"
                        value="{$m.X_Cust.peeringemail}"
                        maxlength="254" size="60" readonly="1"
                    />
                </td>
            </tr>

            <tr>
                <td align="right">
                    <strong>Subject:</strong>&nbsp;&nbsp;
                </td>
                <td>
                    <input id="sendPeeringRequestDialog-subject" type="text" name="subject" value="Loading..." maxlength="254" size="60" />
                </td>
            </tr>

            <tr>
                <td align="center" colspan="2">
                    <textarea id="sendPeeringRequestDialog-message" name="message" cols="78" rows="10" class="fixedFont">Loading...</textarea>
                </td>
            </tr>

            </table>

            <input id="sendPeeringRequestDialog-id" type="hidden" name="id" value="" />
        </form>
    </div>
</div>

<div id="peeringNotesDialog">
    <div class="hd">Peering Notes Dialog</div>
    <div class="bd">
        <form method="POST" action="{genUrl controller=dashboard action='my-peering-matrix-notes' save=1}">
        <p align="center">
            <strong><div id="peeringNotesDialog-member">Loading...</div></strong>
        </p>
        <p align="center">
            <textarea id="peeringNotesDialog-notes" name="notes" cols="78" rows="10" class="fixedFont">Loading...</textarea>
            <input id="peeringNotesDialog-id" type="hidden" name="id" value="" />
        </p>
        </form>
    </div>
</div>


<script type="text/javascript">
{literal}

// Define various event handlers for Dialog
var dialogHandleSubmit = function() {
    document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-success">'
        + '<img src="{/literal}{genUrl}{literal}/images/throbber-small.gif" width="16" height="16" border="0" alt="[...]" />'
        + '&nbsp;Processing....</div>';
    this.submit();
};

var dialogHandleCancel = function() {
    this.cancel();
};

var aniObj = new YAHOO.util.Anim(
    document.getElementById( "ajaxMessage" ),
    { opacity: {from: 1, to: 0 } },
    '10',
    YAHOO.util.Easing.easeOut
);

var dialogHandleSuccess = function(o) {

    var dialogHandleSuccessClearDiv = function() {
        document.getElementById( "ajaxMessage" ).innerHTML = '';
        YAHOO.util.Dom.setStyle( document.getElementById( "ajaxMessage" ), 'opacity', '1' );
    }

    var values = [];
    values = YAHOO.lang.JSON.parse(o.responseText);

    if( values.status == '1' )
    {
        document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-success">' + values.message + '</div>';
    }
    else
    {
        document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-error">' + values.message + '</div>';
    }

    // was there a comment added as part of this action?
    if( values.commentAdded == '1' )
    {
        $('#myPeerNotes-' + values.cid ).empty();
        $('#myPeerNotes-' + values.cid ).append(
            '<img src="{/literal}{genUrl}{literal}/images/22x22/note.png" border="0" width="22" height="22" border="0" alt="Notes" />'
        );
    }

    aniObj.onComplete.subscribe( dialogHandleSuccessClearDiv );
    aniObj.animate();
};

var dialogHandleFailure = function(o) {
    document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-error">Error with AJAX communication.</div>';
};




var sendPeeringRequestDialog = new YAHOO.widget.Dialog( "sendPeeringRequestDialog",
                    {
                        width : "650px",
                        fixedcenter : true,
                        visible : false,
                        constraintoviewport : true,
                        buttons : [
                            { text:"Submit", handler:dialogHandleSubmit, isDefault:true },
                            { text:"Cancel", handler:dialogHandleCancel }
                        ]
                    }
);


// Wire up the success and failure handlers
sendPeeringRequestDialog.callback = {
        success: dialogHandleSuccess,
        failure: dialogHandleFailure
};

function showPeeringRequestDialog( pId )
{

	// Render the Dialog
    document.getElementById( "sendPeeringRequestDialog-id" ).value      = 0;
    document.getElementById( "sendPeeringRequestDialog-to" ).value      = '...';
    document.getElementById( "sendPeeringRequestDialog-from" ).value    = '...';
    document.getElementById( "sendPeeringRequestDialog-bcc" ).value     = '...';
    document.getElementById( "sendPeeringRequestDialog-subject" ).value = '...';
    document.getElementById( "sendPeeringRequestDialog-message" ).value = 'Loading...';
	sendPeeringRequestDialog.render();

	$.getJSON( "{/literal}{genUrl controller='dashboard' action='my-peering-matrix-email'}{literal}/id/" + pId,
	        function( data ) {
		        document.getElementById( "sendPeeringRequestDialog-id" ).value      = pId;
		        document.getElementById( "sendPeeringRequestDialog-to" ).value      = data['to'];
                document.getElementById( "sendPeeringRequestDialog-from" ).value    = data['from'];
                document.getElementById( "sendPeeringRequestDialog-bcc" ).value     = data['bcc'];
                document.getElementById( "sendPeeringRequestDialog-subject" ).value = data['subject'];
                document.getElementById( "sendPeeringRequestDialog-message" ).value = data['message'];
            }
    );

	sendPeeringRequestDialog.show();

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


var peeringNotesDialog = new YAHOO.widget.Dialog( "peeringNotesDialog",
        {
            width : "650px",
            fixedcenter : true,
            visible : false,
            constraintoviewport : true,
            buttons : [
                { text:"Submit", handler:dialogHandleSubmit, isDefault:true },
                { text:"Cancel", handler:dialogHandleCancel }
            ]
        }
);

//Wire up the success and failure handlers
peeringNotesDialog.callback = {
        success: dialogHandleSuccess,
        failure: dialogHandleFailure
};

function editNotes( pId )
{

    // Render the Dialog
    document.getElementById( "peeringNotesDialog-id" ).value         = 0;
    document.getElementById( "peeringNotesDialog-member" ).innerHTML = 'Loading...';
    document.getElementById( "peeringNotesDialog-notes" ).value      = 'Loading...';
	peeringNotesDialog.render();

    $.getJSON( "{/literal}{genUrl controller='dashboard' action='my-peering-matrix-notes'}{literal}/id/" + pId,
            function( data ) {
                document.getElementById( "peeringNotesDialog-id" ).value         = pId;
                document.getElementById( "peeringNotesDialog-member" ).innerHTML = data['name'];
                document.getElementById( "peeringNotesDialog-notes" ).value      = data['notes'];

                document.getElementById( "peeringNotesDialog-notes" ).focus();
                document.getElementById( "peeringNotesDialog-notes" ).setSelectionRange( data['pos'], data['pos'] );
            }
    );

    peeringNotesDialog.show();

}


$().ready( function()
    {
	    {/literal}
        {if $email}
        showPeeringRequestDialog( {$email} );
        {/if}

        // If the user has not been here before, show them the instructions
        {if isset( $showInstructions ) and $showInstructions}
	        {literal}
	        $.fn.colorbox( {
	            width:"750px", height: "600px",
	            href:'{/literal}{genUrl controller="dashboard" action="static" page="help-my-peering-manager"}{literal}'
	        } );
	        {/literal}
	    {/if}

		{literal}

    }
);

{/literal}
</script>



{tmplinclude file="footer.tpl"}


