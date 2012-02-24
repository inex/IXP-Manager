{include file="header.tpl"}

<h2>INEX Public Traffic Statistics</h2>

<h3>{$graphs.$graph} :: {foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach}</h3>

{include file="message.tpl"}
<div id='ajaxMessage'></div>

<p>
<form action="{genUrl controller="dashboard" action="traffic-stats"}" method="post">
<table>
<tr>
    <td width="20"></td>
    <td valign="middle"><strong>Select Infrastructure:</strong></td>
    <td>
        <select name="graph" onchange="this.form.submit();">
            {foreach from=$graphs key=id item=name}
                <option value="{$id}" {if $graph eq $id}selected{/if}>{$name}</option>
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
            shortname='X_Peering'
            period=$pvalue
            category=$category
            values=$stats.$pvalue
            graph=$graph
    }
</p>


{/foreach}



{include file="footer.tpl"}

