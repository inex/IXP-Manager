{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

{if $user.privs eq 3}
    <ul class="breadcrumb">
        <li>
            <a href="{genUrl}">Home</a> <span class="divider">/</span>
        </li>
        <li>
            <a href="{genUrl controller='customer' action='list'}">Customers</a> <span class="divider">/</span>
        </li>
        <li>
             {$customer->name} <span class="divider">/</span>
        </li>
        <li class="active">
            Statistics
            (
             {foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach}
             {if isset( $period )}
                /
                 {foreach from=$periods key=cname item=cvalue}{if $period eq $cvalue}{$cname}{/if}{/foreach}
             {/if}
            )
        </li>
    </ul>
{else}
    <div class="page-content">
        <div class="page-header">
            <h1>IXP Interface Statistics :: {$customer->name}</h1>
        </div>
{/if}

{include file="message.tpl"}
<div id='ajaxMessage'></div>


<div class="row-fluid">

    <div class="span6">

        <h3>Aggregate Traffic Statistics</h3>

        <p>
            <br />
            <a href="{genUrl controller='dashboard' action='statistics-drilldown' monitorindex='aggregate' category=$category shortname=$shortname}">
                {genMrtgImgUrlTag shortname=$shortname category=$category monitorindex='aggregate'}
            </a>
        </p>

    </div>

    <div class="span6">

        <p>
        <br /><br /><br />
        Click on a graphs for longer term statistics or change the graph time in the drop down below.
        </p>

        <form action="{genUrl controller="dashboard" action="statistics"}" method="post" class="well inline">
            <input type="hidden" name="shortname" value="{$customer.shortname}" />
            <strong>Graph Type:</strong>&nbsp;&nbsp;&nbsp;&nbsp;
            <select name="category" onchange="this.form.submit();">
                {foreach from=$categories key=cname item=cvalue}
                    <option value="{$cvalue}" {if $category eq $cvalue}selected{/if}>{$cname}</option>
                {/foreach}
            </select>
        </form>

    </div>

</div>


<div class="row-fluid">

{assign var='count' value=0}

{foreach from=$connections item=connection}

    {foreach from=$connection.Physicalinterface item=pi}

        <div class="span6">

            <div class="well">

                <h4>
                        {$pi.Switchport.SwitchTable.Cabinet.Location.name}
                        / {$pi.Switchport.SwitchTable.name}
                        / {$pi.Switchport.name} ({$pi.speed}Mb/s)
                </h4>


                <p>
                    <br />
                    <a href="{genUrl controller='dashboard' action='statistics-drilldown' monitorindex=$pi.monitorindex category=$category shortname=$shortname}">
                        {genMrtgImgUrlTag shortname=$shortname category=$category monitorindex=$pi.monitorindex}
                    </a>
                </p>

            </div>

        </div>

        {assign var='count' value=$count+1}

        {if $count%2 eq 0}
            </div><br /><div class="row-fluid">
        {/if}

    {/foreach}

{/foreach}

{if $count%2 neq 0}
    <div class="span3"></div>
{/if}

</div>


{include file="footer.tpl"}

