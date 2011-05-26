{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<table class="adminheading" border="0">
<tr>
    <th class="Statistics">
        IXP Interface Statistics :: {$customer.name} :: Drilldown ({foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach})
    </th>
</tr>
</table>

{include file="message.tpl"}

<div id='ajaxMessage'></div>

<div id="content">

{if $switchname eq ''}
    <h2>Aggregate Statistics for All Ports</h2>
{else}
<h2>Switch: {$switchname} &nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp; Port: {$portname}</h2>
{/if}

<p>
<form action="{genUrl controller="dashboard" action="statistics-drilldown" shortname=$shortname monitorindex=$monitorindex}" method="post">
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
</tr>
</table>
</form>
</p>

{foreach from=$periods key=pname item=pvalue}

<h2>{$pname} Graph</h2>

<p>
    {genMrtgGraphBox
            shortname=$customer->shortname
            category=$category
            monitorindex=$monitorindex
            period=$pvalue
            values=$stats.$pvalue
    }
</p>


{/foreach}

</div>

</div>

{include file="footer.tpl"}

