{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Statistics">
    INEX Public Traffic Statistics
    </th>
</tr>
</table>

<h2>{$graphs.$graph} :: {foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach}</h2>

{tmplinclude file="message.tpl"}

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

<h2>{$pname} Graph</h2>

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


</div>
</div>

{tmplinclude file="footer.tpl"}

