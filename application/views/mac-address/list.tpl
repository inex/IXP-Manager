{include file="header.tpl"}


<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li class="active">
        <a href="{genUrl controller=$controller action=$action}">{$frontend.pageTitle}</a>
    </li>
</ul>

{include file="message.tpl"}


<table id="ixpDataTable"  cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered hide">

<thead>
    <tr>
        <th>Customer</th>
        <th>Interface</th>
        <th>IPv4</th>
        <th>IPv6</th>
        <th>MAC</th>
        <th>First Seen</th>
        <th>Last Seen</th>
    </tr>
</thead>
<tbody>
{foreach from=$macs item=m}

	<tr>
	    <td><a href="{genUrl controller="customer" action="dashboard" id=$m.Virtualinterface.Cust.id}">{$m.Virtualinterface.Cust.name}</a></td>
	    <td>
	        {foreach $m.Virtualinterface.Physicalinterface as $pi}
	            <a href="{genUrl controller="virtual-interface" action="edit" id=$m.Virtualinterface.id}">{$pi.Switchport.SwitchTable.name} / {$pi.Switchport.name}</a>
	            {if not $pi@last}<br />{/if}
	        {/foreach}
	    </td>
	    <td class="pre">
	        {foreach $m.Virtualinterface.Vlaninterface as $vli}
	            {$vli.Ipv4address.address}
	            {if not $vli@last}<br />{/if}
	        {/foreach}
        </td>
	    <td class="pre">
	        {foreach $m.Virtualinterface.Vlaninterface as $vli}
	            {$vli.Ipv6address.address}
	            {if not $vli@last}<br />{/if}
	        {/foreach}
        </td>
	    <td class="pre">{$m.mac}</td>
	    <td>{$m.firstseen}</td>
	    <td>{$m.lastseen}</td>
    </tr>

{/foreach}
</tbody>
</table>

<script type="text/javascript">

$(document).ready(function() {
    
    	oTable = $('#ixpDataTable').dataTable({
    
            "aaSorting": [[ 0, 'asc' ]],
            "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
    		"iDisplayLength": 100,
    		"sPaginationType": "bootstrap"
    	});
    
    	$('#ixpDataTable').show();
    });

</script>

{include file="footer.tpl"}
