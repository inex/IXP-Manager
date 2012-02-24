{include file="header.tpl"}


{if $user.privs eq 3}
    <ul class="breadcrumb">
        <li>
            <a href="{genUrl}">Home</a> <span class="divider">/</span>
        </li>
        <li>
            Switch Aggregate Graphs <span class="divider">/</span>
        </li>
        <li class="active">
            {$switches.$switch.name} ({foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach})
        </li>
    </ul>
{else}
    <div class="page-content">
        <div class="page-header">
            <h1>Switch Aggregate Graphs - {$switches.$switch.name} ({foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach})</h1>
        </div>
{/if}


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


<div class="row-fluid">

{assign var='count' value=0}

{foreach from=$periods key=pname item=pvalue}


    <div class="span6">

        <div class="well">

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
