{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Statistics">
        Trunk Graphs :: {$graphs.$graph}
    </th>
</tr>
</table>

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

<h2>{$pname} Graph</h2>

<p>
    {genMrtgGraphBox
            shortname='X_Trunks'
            period=$pvalue
            values=$stats.$pvalue
            graph=$graph
    }
</p>


{/foreach}


</div>
</div>

{include file="footer.tpl"}

