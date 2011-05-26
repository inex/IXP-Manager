
<p>
	Please see the <em>Statistics</em> menu above right for more graphs and options.
</p>


<p>

<form action="{genUrl controller="dashboard" action="statistics"}" method="post">
<table>
<tr>
    <td width="20"></td>
    <td valign="middle"><strong>Graph Type:</strong></td>
    <td>
        <select name="category" onchange="this.form.submit();">
            {foreach from=$categories key=cname item=cvalue}
                <option value="{$cvalue}">{$cname}</option>
            {/foreach}
        </select>
    </td>
</tr>
</table>
</form>
</p>

<h2>Aggregate Traffic Statistics</h2>

<p>
	<a href="{genUrl controller="dashboard" action="statistics-drilldown" shortname=$customer.shortname category='bits' monitorindex='aggregate'}">
	    {genMrtgImgUrlTag shortname=$customer.shortname category='bits' monitorindex='aggregate'}
    </a>
</p>


{foreach from=$connections item=connection}

    <h2>
        Connection:
                {$connection.Physicalinterface.0.Switchport.SwitchTable.Cabinet.Location.name}
            / {$connection.Physicalinterface.0.Switchport.SwitchTable.name}
            / {$connection.Physicalinterface.0.Switchport.name} ({$connection.Physicalinterface.0.speed}Mb/s)
    </h2>


    <p>
    	<a href="{genUrl controller="dashboard" action="statistics-drilldown" shortname=$customer.shortname category='bits' monitorindex=$connection.Physicalinterface.0.monitorindex}">
	        {genMrtgImgUrlTag shortname=$customer.shortname monitorindex=$connection.Physicalinterface.0.monitorindex}
        </a>
    </p>

{/foreach}
