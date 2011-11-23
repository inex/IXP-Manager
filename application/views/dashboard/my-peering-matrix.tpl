{tmplinclude file="header.tpl" pageTitle="IXP Manager :: My Peering Manager"}

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

<table id="myPeeringMatrixTable" class="display" cellspacing="0" cellpadding="0" border="0" style="display:none;">

    <thead>
    <tr>
        <th>State</th>
        <th>PM</th>
        <th>RS</th>
        {if $ipv6[$customer.id]}<th>IPv6</th>{/if}
        <th>Name</th>
        <th>ASN</th>
        <th>Policy</th>
        <th>Peering&nbsp;Contact</th>
        <th>Member&nbsp;Since</th>
        <th></th>
    </tr>
    </thead>
    <tbody>

{foreach from=$matrix item=m}

	{if $m.dead eq 0}
	
        <tr>
            <td>
                <a href="#s{$m.Peer.id}" onClick="changeMyPeeredState( '{$m.Peer.id}', {$vlan} ); return false;">
                    <div id="myPeeredState-{$m.Peer.id}">
                        {if $m.peered eq 'YES'}
    		                <img src="{genUrl}/images/22x22/yes.png" alt="YES" title="Peered"
    		                    width="22" height="22" border="0" />
    		            {elseif $m.peered eq 'NO'}
    		                <img src="{genUrl}/images/22x22/no.png" alt="NO" title="Not peered"
    		                    width="22" height="22" border="0" />
    		            {elseif $m.peered eq 'WAITING'}
    		                <img src="{genUrl}/images/22x22/waiting.png" alt="Waiting"
    		                    title="WAITING" width="22" height="22" border="0" />
    		            {elseif $m.peered eq 'NEVER'}
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
            	{assign var='peerid' value=$m.Peer.id}
            	
            	{if $m.Peer.activepeeringmatrix and isset( $pmatrix.$peerid )}
                    {if $pmatrix.$peerid.peering_status eq 'YES'}
                        <img src="{genUrl}/images/yes.gif" alt="YES" title="Peered" width="21" height="21" border="0" />
                    {elseif $pmatrix.$peerid.peering_status eq 'NO'}
                        <img src="{genUrl}/images/no.gif" alt="NO" title="Not peered" width="21" height="21" border="0" />
                    {elseif $pmatrix.$peerid.peering_status eq 'INCONSISTENT_X'}
                        <img src="{genUrl}/images/inconsistent1.gif" alt="INCONSISTENT_X" title="INCONSISTENT_X" width="21" height="21" border="0" />
                    {elseif $pmatrix.$peerid.peering_status eq 'INCONSISTENT_Y'}
                        <img src="{genUrl}/images/inconsistent2.gif" alt="INCONSISTENT_Y" title="INCONSISTENT_Y" width="21" height="21" border="0" />
                    {/if}
                {elseif not $m.Peer.activepeeringmatrix}
                	N/A
            	{else}
            	{/if}
            </td>
    
            <td>
                {if $rsclient[$m.Peer.id]}
                    <img src="{genUrl}/images/22x22/im-user.png"          alt="Y" width="22" height="22" border="0" />
                {else}
                     <img src="{genUrl}/images/22x22/im-user-offline.png" alt="N" width="22" height="22" border="0" />
                {/if}
            </td>
    
            {* If I'm IPv6 enabled *}
            {if $ipv6[$customer.id]}
    	        <td>
    	            {if $ipv6[$m.Peer.id]}
    	                <a href="#v6{$m.Peer.id}" onClick="changeIPv6PeeredState( '{$m.Peer.id}', {$vlan} ); return false;">
                            <div id="ipv6PeeredState-{$m.Peer.id}">
        	                    {if $m.ipv6}
    	                            <img src="{genUrl}/images/22x22/face-smile-big.png" alt="PEERED OVER IPv6"      title="Peered over IPv6"     width="22" height="22" border="0" />
    	                        {else}
    	                            <img src="{genUrl}/images/22x22/face-crying.png"    alt="NOT PEERED OVER IPv6" title="Not peered over IPv6" width="22" height="22" border="0" />
    	                        {/if}
    	                    </div>
    	                </a>
    	            {/if}
    	        </td>
            {/if}
    
            <td>{$m.Peer.name}</td>
            <td>{$m.Peer.autsys|asnumber}</td>
            <td>{$m.Peer.peeringpolicy}</td>
            <td>
                <a href="#e{$m.Peer.id}" onClick="showPeeringRequestDialog( '{$m.Peer.id}' ); return false;" title="{$m.Peer.peeringemail}"
                	>{$m.Peer.peeringemail|truncate:30}</a>
            </td>
            <td>{$m.Peer.datejoin}</td>
            <td>
                <a href="#n{$m.Peer.id}" onClick="editNotes( '{$m.Peer.id}' ); return false;">
                    <div id="myPeerNotes-{$m.Peer.id}">
                        {if $m.notes_id neq null}
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

	</tbody>
</table>


{tmplinclude file="dashboard/my-peering-manager-dialog-email.tpl"}
{tmplinclude file="dashboard/my-peering-manager-dialog-notes.tpl"}
{tmplinclude file="dashboard/my-peering-manager.js.tpl"}

{tmplinclude file="footer.tpl"}
