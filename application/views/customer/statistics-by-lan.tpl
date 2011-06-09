{tmplinclude file="header.tpl"}

<!-- <div class="yui-g" style="height: 600px"> -->

<table class="adminheading" border="0">
<tr>
    <th class="Customer">
        IXP Members :: Statistics for {$lan.name}
        (
         {foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach}
        /
         {foreach from=$periods key=cname item=cvalue}{if $period eq $cvalue}{$cname}{/if}{/foreach}
        )
    </th>
</tr>
</table>


{tmplinclude file="message.tpl"}

<div class="content">

<p>
<form action="{genUrl controller="customer" action="statistics-by-lan"}" method="post">
<table>
<tr>
    <td width="20"></td>
    <td valign="middle"><strong>Graph Type:</strong></td>
    <td>
        <select name="category" onchange="this.form.submit();">
            {foreach from=$categories key=cname item=cvalue}
                <option value="{$cvalue}" {if $category eq $cvalue}selected{/if}>{$cname}</option>
            {/foreach}
        </select>
    </td>
    <td width="20"></td>
    <td valign="middle"><strong>Period:</strong></td>
    <td>
        <select name="period" onchange="this.form.submit();">
            {foreach from=$periods key=cname item=cvalue}
                <option value="{$cvalue}" {if $period eq $cvalue}selected{/if}>{$cname}</option>
            {/foreach}
        </select>
    </td>
    <td width="20"></td>
    <td valign="middle"><strong>LAN:</strong></td>
    <td>
        <select name="lan" onchange="this.form.submit();">
            {foreach from=$config.peering_matrix.public key=index item=cvalue}
                <option value="{$cvalue.number}" {if $lan.number eq $cvalue.number}selected{/if}>{$cvalue.name}</option>
            {/foreach}
        </select>
    </td>
</tr>
</table>
</form>
</p>


<table align="center" border="1">
<tr>

{assign var='count' value=0}
{foreach from=$ints item=int}

{foreach from=$int.Virtualinterface.Physicalinterface item=pi}

<td>

<h3>{$int.Virtualinterface.Cust.name}</h3>

<h4>{$pi.Switchport.SwitchTable.name} {$pi.Switchport.name} ({$pi.speed}/{$pi.duplex})</h4>

<a href="{genUrl controller="dashboard" action="statistics" shortname=$int.Virtualinterface.Cust.shortname monitorindex=$pi.monitorindex category=$category}">
	<img
	    src="{genMrtgImgUrl shortname=$int.Virtualinterface.Cust.shortname category=$category period=$period monitorindex=$pi.monitorindex}"
	    width="300"
	/>
</a>

</td>

{assign var='count' value="`$count+1`"}

{if $count%3 eq 0}
</tr><tr>
{/if}

{/foreach}

{/foreach}

{if $count%3 neq 0}
    <td></td>
    {assign var='count' value="`$count+1`"}
    {if $count%3 neq 0}
    <td></td>
    {/if}
{/if}

</tr>

</table>

{tmplinclude file="footer.tpl"}