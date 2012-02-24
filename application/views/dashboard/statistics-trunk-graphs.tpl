{include file="header.tpl"}

{if $user.privs eq 3}
    <ul class="breadcrumb">
        <li>
            <a href="{genUrl}">Home</a> <span class="divider">/</span>
        </li>
        <li>
            Trunk Graphs <span class="divider">/</span>
        </li>
        <li class="active">
            {$graphs.$graph}
        </li>
    </ul>
{else}
    <div class="page-content">
        <div class="page-header">
            Trunk Graphs :: {$graphs.$graph}
        </div>
{/if}

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

<div class="row-fluid">

{assign var='count' value=0}


    {foreach from=$periods key=pname item=pvalue}

    <div class="span6">

        <div class="well">

            <h3>{$pname} Graph</h3>

            <p>
                {genMrtgGraphBox
                        shortname='X_Trunks'
                        period=$pvalue
                        values=$stats.$pvalue
                        graph=$graph
                }
            </p>
        </div>
    </div>

    {assign var='count' value=$count+1}

    {if $count%2 eq 0}
        </div><br /><div class="row-fluid">
    {/if}

{/foreach}

{if $count%2 neq 0}
    <div class="span3"></div>
{/if}

</div>

{include file="footer.tpl"}

