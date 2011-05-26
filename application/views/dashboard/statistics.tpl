{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Statistics">
        IXP Interface Statistics :: {$customer->name}
    </th>
</tr>
</table>

{include file="message.tpl"}

<div id='ajaxMessage'></div>


<p>
Click on a graph below for longer term statistics or change the graph time in the drop down below.
</p>

<p>
<form action="{genUrl controller="dashboard" action="statistics"}" method="post">
<input type="hidden" name="shortname" value="{$customer.shortname}" />
<table>
<tr>
    <td width="20"></td>
    <td valign="middle"><strong>Graph Type:</strong></td>
    <td>
        <select name="category" onchange="this.form.submit();">
            {foreach from=$categories key=cname item=cvalue}
                <option value="{$cvalue}" {if $category eq $cvalue}selected{/if}>{$cname}</option>
            {/foreach}
        </select>
    </td>
</tr>
</table>
</form>
</p>

<h2>Aggregate Traffic Statistics</h2>

            <p>
		<a href="{genUrl controller='dashboard' action='statistics-drilldown' monitorindex='aggregate' category=$category shortname=$shortname}">
                    {genMrtgImgUrlTag shortname=$shortname category=$category monitorindex='aggregate'}
		</a>
            </p>


{foreach from=$connections item=connection}

    {foreach from=$connection.Physicalinterface item=pi}

        <h2>
            Connection:
                    {$pi.Switchport.SwitchTable.Cabinet.Location.name}
                / {$pi.Switchport.SwitchTable.name}
                / {$pi.Switchport.name} ({$pi.speed}Mb/s)
        </h2>


        <p>
            <a href="{genUrl controller='dashboard' action='statistics-drilldown' monitorindex=$pi.monitorindex category=$category shortname=$shortname}">
                {genMrtgImgUrlTag shortname=$shortname category=$category monitorindex=$pi.monitorindex}
            </a>
        </p>

    {/foreach}

{/foreach}

</div>
</div>

{include file="footer.tpl"}

