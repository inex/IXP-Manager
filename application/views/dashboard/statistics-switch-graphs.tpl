{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Statistics">
        Switch Aggregate Graphs :: {$switches.$switch.name} :: {foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach}
    </th>
</tr>
</table>

{include file="message.tpl"}

<div id='ajaxMessage'></div>

<p>
<form action="{genUrl controller="dashboard" action="switch-graphs"}" method="post">
<table>
<tr>
    <td width="20"></td>
    <td valign="middle"><strong>Select Switch:</strong></td>
    <td>
        <select name="switch" onchange="this.form.submit();">
            {foreach from=$switches key=id item=data}
                <option value="{$id}" {if $switch eq $id}selected{/if}>{$data.name}</option>
            {/foreach}
        </select>
    </td>
    <td width="20"></td>
    <td valign="middle"><strong>Category:</strong></td>
    <td>
        <select name="category" onchange="this.form.submit();">
            {foreach from=$categories key=cname item=cval}
                <option value="{$cval}" {if $category eq $cval}selected{/if}>{$cname}</option>
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
            shortname='X_SwitchAggregate'
            period=$pvalue
            category=$category
            values=$stats.$pvalue
            graph=$switches.$switch.name
    }
</p>


{/foreach}


</div>
</div>

{include file="footer.tpl"}

