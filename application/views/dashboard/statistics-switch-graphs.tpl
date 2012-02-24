{include file="header.tpl"}

<h2>Switch Aggregate Graphs :: {$switches.$switch.name} :: {foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach}</h2>

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

<h3>{$pname} Graph</h3>

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


{include file="footer.tpl"}
