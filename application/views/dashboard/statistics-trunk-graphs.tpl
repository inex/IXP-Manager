{include file="header.tpl"}

<h2>Trunk Graphs :: {$graphs.$graph}</h2>

{include file="message.tpl"}
<div id='ajaxMessage'></div>

<p>
<form action="{genUrl controller="dashboard" action="trunk-graphs"}" method="post">
<table>
<tr>
    <td width="20"></td>
    <td valign="middle"><strong>Select Trunk:</strong></td>
    <td>
        <select name="trunk" onchange="this.form.submit();">
            {foreach from=$graphs key=image item=name}
                <option value="{$image}" {if $graph eq $image}selected{/if}>{$name}</option>
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
            shortname='X_Trunks'
            period=$pvalue
            values=$stats.$pvalue
            graph=$graph
    }
</p>


{/foreach}

{include file="footer.tpl"}

