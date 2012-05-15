{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

{if $user.privs eq 3}
    <ul class="breadcrumb">
        <li>
            <a href="{genUrl}">Home</a> <span class="divider">/</span>
        </li>
        <li>
            <a href="{genUrl controller="dashboard" action="member-details"}">Member Details</a> <span class="divider">/</span>
        </li>
        <li class="active">
            {$cust.name}
        </li>
    </ul>
{else}
    <div class="page-content">
    
        <div class="page-header">
            <h1>Member Details - {$cust.name}</h1>
        </div>
{/if}
    

{include file="message.tpl"}
<div id='ajaxMessage'></div>

<table id="ixpDataTable">

<tr>
    <td width="200"><strong>Member Type:</strong></td>
    <td width="200" id="value">{$cust->getMemberTypeString()}</td>
    <td width="40"></td>
    <td width="200"><strong>Member Status:</strong></td>
    <td width="200" id="value">{$cust->getMemberStatusString()}</td>
</tr>

<tr>
    <td>&nbsp;</td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td><strong>AS Number:</strong></td>
    <td id="value">{$cust.autsys|asnumber}</td>
    <td></td>
    <td><strong>Peering Macro:</strong></td>
    <td id="value">{$cust.peeringmacro}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td><strong>Peering Policy:</strong></td>
    <td id="value">{$cust.peeringpolicy}</td>
    <td></td>
    <td></td>
    <td></td>
</tr>

<tr>
    <td>&nbsp;</td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td><strong>Peering Email:</strong></td>
    <td id="value">{$cust.peeringemail}</td>
    <td></td>
    <td><strong>NOC Email</strong></td>
    <td id="value">{$cust.nocemail}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td><strong>NOC Phone:</strong></td>
    <td id="value">{$cust.nocphone}</td>
    <td></td>
    <td><strong>NOC 24 Hour Phone</strong></td>
    <td id="value">{$cust.noc24hphone}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td><strong>Dedicated NOC Web:</strong></td>
    <td id="value"><a href="{$cust.nocwww}">{$cust.nocwww}</a></td>
    <td></td>
    <td><strong>NOC Fax</strong></td>
    <td id="value">{$cust.nocfax}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td><strong>NOC Hours:</strong></td>
    <td id="value">{$cust.nochours}</td>
    <td></td>
    <td></td>
    <td></td>
</tr>

<tr>
    <td>&nbsp;</td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td><strong>Corporate Web:</strong></td>
    <td id="value"><a href="{$cust.corpwww}">{$cust.corpwww}</a></td>
    <td></td>
    <td></td>
    <td></td>
</tr>

<tr>
    <td>&nbsp;</td><td></td><td></td><td></td><td></td>
</tr>

</table>


{foreach from=$connections item=connection}

<hr width="95%"></hr>

<h3>
    Connection {counter name=numconnections}
    <small>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Infrastructure {if $connection.Vlaninterface.0.Vlan.number % 10 == 0}1{else}2{/if}
        {if count( $connection.Physicalinterface ) > 1}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LAG Port
        {/if}
    </small>
</h3>

<table id="myDetailsTable">

<tr>
    <td width="200"><strong>Switch:</strong></td>
    <td width="200" id="value">{$connection.Physicalinterface.0.Switchport.SwitchTable.name}.inex.ie</td>
    <td width="40"></td>
    <td width="200"><strong>Switch Port:</strong></td>
    <td width="200" id="value">
        {foreach from=$connection.Physicalinterface item=pi}
            {$pi.Switchport.name}<br />
        {/foreach}
    </td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td><strong>Speed:</strong></td>
    <td id="value">{$connection.Physicalinterface.0.speed} Mbps</td>
    <td></td>
    <td><strong>Duplex:</strong></td>
    <td id="value">{$connection.Physicalinterface.0.duplex}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td><strong>Location:</strong></td>
    <td id="value">{$connection.Physicalinterface.0.Switchport.SwitchTable.Cabinet.Location.name}</td>
    <td></td>
    <td><strong>Colo Cabinet ID:</strong></td>
    <td id="value">{$connection.Physicalinterface.0.Switchport.SwitchTable.Cabinet.name}</td>
</tr>

<tr>
    <td><br /></td><td></td><td></td><td></td><td></td>
</tr>

</table>

{foreach from=$connection.Vlaninterface item=interface}
{assign var='vlanid' value=$interface.vlanid}

<h4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$interface.Vlan.name}:</h4>

<table id="myDetailsTable">

<tr>
    <td width="40"></td>
    <td width="200"><strong>IPv4 Address:</strong></td>
    <td width="200" id="value">{$interface.Ipv4address.address}/{$networkInfo.$vlanid.4.masklen}</td>
    <td width="40"></td>
    <td width="200"><strong>IPv6 Address:</strong></td>
    <td width="200" id="value">{if isset( $interface.Ipv6address ) and $interface.Ipv6address.address}{$interface.Ipv6address.address}/{$networkInfo.$vlanid.6.masklen}{/if}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td></td>
    <td><strong>Multicast Enabled:</strong></td>
    <td id="value">{if $interface.mcastenabled}Yes{else}No{/if}</td>
    <td></td>
    <td><strong>IPv6 Enabled:</strong></td>
    <td id="value">{if $interface.ipv6enabled}Yes{else}No{/if}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td></td>
    <td><strong>Route Server Client:</strong></td>
    <td id="value">{if $interface.rsclient}Yes{else}No{/if}</td>
    <td></td>
    <td><strong>AS112 Client:</strong></td>
    <td id="value">{if $interface.as112client}Yes{else}No{/if}</td>
</tr>

</table>

{/foreach}

<br /><br />

{/foreach}


{include file="footer.tpl"}
