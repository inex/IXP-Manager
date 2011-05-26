{include file="header.tpl" pageTitle="IXP Manager :: IPv6 Addresses"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Document">
        IPv6 Addresses :: {$ips.0.Vlan.name}
    </th>
</tr>
</table>


<table class="tblist">

<tr>
    <th>IPv6 Address</th>
    <th>ARPA Entry</th>
    <th>Customer</th>
</tr>

{foreach from=$ips item=ip}

	<tr class="{cycle values="tblist_odd,tblist_even"}">
		<td class="pre">{$ip.address}</td>
		<td class="pre">{$ip.Vlaninterface.ipv4hostname}</td>
		<td>{$ip.Vlaninterface.Virtualinterface.Cust.name}</td>
	</tr>

{/foreach}

</table>


</div>

</div>

{include file="footer.tpl"}
