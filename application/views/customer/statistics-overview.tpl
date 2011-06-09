{tmplinclude file="header.tpl"}

<!-- <div class="yui-g" style="height: 600px"> -->

<table class="adminheading" border="0">
<tr>
    <th class="Customer">
        IXP Members :: Statistics Overview
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
<form action="{genUrl controller="customer" action="statistics-overview"}" method="post">
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
</tr>
</table>
</form>
</p>


<table align="center" border="1">
<tr>

{assign var='count' value=0}
{foreach from=$custs item=cust}


<td>

<h3>{$cust.name}</h3>

<a href="{genUrl controller="dashboard" action="statistics" shortname=$cust.shortname monitorindex=aggregate category=$category}">
	<img
	    src="{genMrtgImgUrl shortname=$cust.shortname category=$category period=$period monitorindex='aggregate'}"
	    width="300"
	/>
</a>

</td>

{assign var='count' value="`$count+1`"}

{if $count%3 eq 0}
</tr><tr>
{/if}

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