{include file="header.tpl"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        Statistics <span class="divider">/</span>
    </li>
    <li class="active">
        Graphs
        (
         {foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach}
        /
         {foreach from=$periods key=cname item=cvalue}{if $period eq $cvalue}{$cname}{/if}{/foreach}
        )
    </li>
</ul>

{include file="message.tpl"}

<p>
<form action="{genUrl controller="customer" action="statistics-overview"}" method="post">
<table>
<tr>
    <td width="20"></td>
    <td valign="middle"><strong>Graph Type:</strong></td>
    <td>
        &nbsp;
        <select name="category" class="chzn-select" onchange="this.form.submit();">
            {foreach from=$categories key=cname item=cvalue}
                <option value="{$cvalue}" {if $category eq $cvalue}selected{/if}>{$cname}</option>
            {/foreach}
        </select>
    </td>
    <td width="20"></td>
    <td valign="middle"><strong>Period:</strong></td>
    <td>
        &nbsp;
        <select name="period" class="chzn-select" onchange="this.form.submit();">
            {foreach from=$periods key=cname item=cvalue}
                <option value="{$cvalue}" {if $period eq $cvalue}selected{/if}>{$cname}</option>
            {/foreach}
        </select>
    </td>
</tr>
</table>
</form>
</p>


<div class="row-fluid">

{assign var='count' value=0}
{foreach from=$custs item=cust}

    <div class="span3">

        <div class="well">
            <h4 style="vertical-align: middle">
                {$cust.name}
                {if $category eq 'bits' or $category eq 'pkts'}
                    <span class="btn btn-mini" style="float: right">
                        <a href="{genUrl controller="dashboard" action="p2p" shortname=$cust.shortname category=$category period=$period}"><i class="icon-random"></i></a>
                    </span>
                {/if}
            </h4>

            <p>
                <br />
                <a href="{genUrl controller="dashboard" action="statistics" shortname=$cust.shortname monitorindex=aggregate category=$category}">
                    <img
                        src="{genMrtgImgUrl shortname=$cust.shortname category=$category period=$period monitorindex='aggregate'}"
                        width="300"
                    />
                </a>
            </p>
        </div>

    </div>

    {assign var='count' value=$count+1}

    {if $count%4 eq 0}
        </div><br /><div class="row-fluid">
    {/if}

{/foreach}

{if $count%4 neq 0}
    <div class="span3"></div>
    {assign var='count' value=$count+1}
    {if $count%4 neq 0}
        <div class="span3"></div>
        {assign var='count' value=$count+1}
        {if $count%4 neq 0}
            <div class="span3"></div>
            {assign var='count' value=$count+1}
        {/if}
    {/if}
{/if}

</div>


{include file="footer.tpl"}