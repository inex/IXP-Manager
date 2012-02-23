{include file="header.tpl"}


<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li class="active">
        <a href="{genUrl controller=$controller action=$action}">{$frontend.pageTitle}</a>
    </li>
</ul>

{include file="message.tpl"}

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


<table id="ixpDataTable" class="table table-striped table-bordered">

<tr>
    <th>IPv4 Address</th>
    <th>ARPA Entry</th>
    <th>Customer</th>
</tr>

{foreach from=$ips item=ip}

	<tr>
		<td class="pre">{$ip.address}</td>
		<td class="pre">{$ip.Vlaninterface.ipv4hostname}</td>
		<td>{$ip.Vlaninterface.Virtualinterface.Cust.name}</td>
	</tr>

{/foreach}

</table>

{include file="footer.tpl"}
