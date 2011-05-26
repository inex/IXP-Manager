{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

{include file="message.tpl"}

<div id='ajaxMessage'></div>

<h2>Details for INEX Member: {$cust.name}</h2>

{literal}
<style>

#myDetailsTable td {
    padding: 1px;
    margin: 10px;
}

#myDetailsTable td#value {
    border: 1px solid #BABABA;
    color: #333;
}
</style>
{/literal}

<table id="myDetailsTable">

<tr>
    <td width="200">Member Type:</td>
    <td width="200" id="value">{$cust->getMemberTypeString()}</td>
    <td width="40"></td>
    <td width="200">Member Status:</td>
    <td width="200" id="value">{$cust->getMemberStatusString()}</td>
</tr>

<tr>
    <td>&nbsp;</td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td>AS Number:</td>
    <td id="value">{$cust.autsys|asnumber}</td>
    <td></td>
    <td>Peering Macro:</td>
    <td id="value">{$cust.peeringmacro}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td>Peering Policy:</td>
    <td id="value">{$cust.peeringpolicy}</td>
    <td></td>
    <td></td>
    <td></td>
</tr>

<tr>
    <td>&nbsp;</td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td>Peering Email:</td>
    <td id="value">{$cust.peeringemail}</td>
    <td></td>
    <td>NOC Email</td>
    <td id="value">{$cust.nocemail}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td>NOC Phone:</td>
    <td id="value">{$cust.nocphone}</td>
    <td></td>
    <td>NOC 24 Hour Phone</td>
    <td id="value">{$cust.noc24hphone}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td>Dedicated NOC Web:</td>
    <td id="value"><a href="{$cust.nocwww}">{$cust.nocwww}</a></td>
    <td></td>
    <td>NOC Fax</td>
    <td id="value">{$cust.nocfax}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td>NOC Hours:</td>
    <td id="value">{$cust.nochours}</td>
    <td></td>
    <td></td>
    <td></td>
</tr>

<tr>
    <td>&nbsp;</td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td>Corporate Web:</td>
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

<h2>Connection {counter name=numconnections}</h2>

<table id="myDetailsTable">

<tr>
    <td width="200">Switch:</td>
    <td width="200" id="value">{$connection.Physicalinterface.0.Switchport.SwitchTable.name}.inex.ie</td>
    <td width="40"></td>
    <td width="200">Switch Port:</td>
    <td width="200" id="value">{$connection.Physicalinterface.0.Switchport.name}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td>Speed:</td>
    <td id="value">{$connection.Physicalinterface.0.speed} Mbps</td>
    <td></td>
    <td>Duplex:</td>
    <td id="value">{$connection.Physicalinterface.0.duplex}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td>Location:</td>
    <td id="value">{$connection.Physicalinterface.0.Switchport.SwitchTable.Cabinet.Location.name}</td>
    <td></td>
    <td>Colo Cabinet ID:</td>
    <td id="value">{$connection.Physicalinterface.0.Switchport.SwitchTable.Cabinet.name}</td>
</tr>

<tr>
    <td>&nbsp;</td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td>AS Number:</td>
    <td id="value">AS{$cust.autsys}</td>
    <td></td>
    <td>Peering Macro:</td>
    <td id="value">{$cust.peeringmacro}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

</table>

{foreach from=$connection.Vlaninterface item=interface}
{assign var='vlanid' value=$interface.vlanid}

<h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$interface.Vlan.name}:</h3>

<table id="myDetailsTable">

<tr>
    <td width="200">IPv4 Address:</td>
    <td width="200" id="value">{$interface.Ipv4address.address}/{$networkInfo.$vlanid.4.masklen}</td>
    <td width="40"></td>
    <td width="200">IPv6 Address:</td>
    <td width="200" id="value">{if $interface.Ipv6address.address}{$interface.Ipv6address.address}/{$networkInfo.$vlanid.6.masklen}{/if}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td>Multicast Enabled:</td>
    <td id="value">{if $interface.mcastenabled}Yes{else}No{/if}</td>
    <td></td>
    <td>IPv6 Enabled:</td>
    <td id="value">{if $interface.ipv6enabled}Yes{else}No{/if}</td>
</tr>

<tr>
    <td></td><td></td><td></td><td></td><td></td>
</tr>

<tr>
    <td>Route Server Client:</td>
    <td id="value">{if $interface.rsclient}Yes{else}No{/if}</td>
    <td></td>
    <td>AS112 Client:</td>
    <td id="value">{if $interface.as112client}Yes{else}No{/if}</td>
</tr>

</table>

{/foreach}

<br /><br />

{/foreach}

</div>

</div>

{include file="footer.tpl"}
