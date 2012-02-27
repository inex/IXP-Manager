{include file="header.tpl" pageTitle="IXP Manager :: "|cat:$frontend.pageTitle}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="{genUrl controller=$controller action=$action}">Switches</a> <span class="divider">/</span>
    </li>
    <li class="active">
        Port Report for {$switch.name}
    </li>
</ul>

{include file="message.tpl"}
<div id="ajaxMessage"></div>

<div class="list_preamble_container">
<div class="list_preamble">
<p>
<form name="switch_jumpto" class="form" method="post" action="{genUrl controller="switch" action="port-report"}">
    <strong>Switch:</strong>&nbsp;

    <select onchange="document.switch_jumpto.submit()" name="id">

        <option value="0"></option>
        {foreach from=$switches item=s}
            <option value="{$s.id}" {if isset( $switch ) and $switch.id eq $s.id}selected{/if}>{$s.name}</option>
        {/foreach}

    </select>
</form>
</p>
</div>
</div>


<table id="ixpDataTable" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Port Name</th>
            <th>Type</th>
            <th>Speed/Duplex</th>
            <th>Customer</th>
        </tr>
    </thead>
    <tbody>

        {foreach from=$ports item=p}

        <tr>
        	<td>{$p.id}</td>
        	<td>{$p.name}</td>
        	<td>{$p.type}</td>
        	{if $p.connection}
        		<td>{$p.connection.speed}/{$p.connection.duplex}</td>
        		<td>{$p.connection.Virtualinterface.Cust.name}</td>
        	{else}
        		<td></td>
        		<td></td>
        	{/if}
        </tr>

        {/foreach}

	</tbody>
</table>


<script type="text/javascript">

$(document).ready(function() {ldelim}

	oTable = $('#ixpDataTable').dataTable({ldelim}

        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
	    "aaSorting": [[ 0, 'asc' ]],
		"sPaginationType": "bootstrap",
		"iDisplayLength": 50,
		"aoColumns": [
        	{ldelim} "sWidth": "50px" {rdelim},
           	{ldelim} "sWidth": "150px" {rdelim},
           	{ldelim} "sWidth": "100px" {rdelim},
           	{ldelim} "sWidth": "100px" {rdelim},
    		null
      	]
	{rdelim}).show();

{rdelim});
</script>


{include file="footer.tpl"}
