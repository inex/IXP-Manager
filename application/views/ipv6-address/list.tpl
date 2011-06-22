{tmplinclude file="header.tpl" pageTitle="IXP Manager :: IPv6 Addresses"}

<div class="yui-g">

<div id="content">

<div class="list_preamble_container">
<div class="list_preamble">

<p>
<form name="vlan_jumpto" class="form" method="post">
    <strong>VLAN:</strong>&nbsp;

    <select onchange="document.vlan_jumpto.submit()" name="vlanid">

        <option value=""></option>
        {foreach from=$vlans item=v}
            <option value="{$v.id}" {if $vlan.id eq $v.id}selected{/if}>{$v.name}</option>
        {/foreach}

    </select>
</form>
</p>
</div>
</div>


<table class="adminheading" border="0" style="margin-top: -50px; margin-bottom: 20px;">

<tr>
    <th class="Document">
        IPv6 Addresses :: {$vlan.name}
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

{tmplinclude file="footer.tpl"}
