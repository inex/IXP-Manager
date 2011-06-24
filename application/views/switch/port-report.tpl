{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g" style="margin-bottom: 70px;">

<table class="adminheading" border="0">
	<tr>
		<th class="Switch">Switch :: Port Report</th>
	</tr>
</table>

<div class="list_preamble_container">
<div class="list_preamble">

<p>
<form name="switch_jumpto" class="form" method="post" action="{genUrl controller="switch" action="port-report}">
    <strong>Switch:</strong>&nbsp;

    <select onchange="document.switch_jumpto.submit()" name="id">

        <option value="0"></option>
        {foreach from=$switches item=s}
            <option value="{$s.id}" {if isset( $switchid ) and $switchid eq $s.id}selected{/if}>{$s.name}</option>
        {/foreach}

    </select>
</form>
</p>
</div>
</div>


<div id="portReportContainer">
    <table id="portReportTable">
        <thead>
            <tr>
                <th>Port Name</th>
                <th>Type</th>
                <th>Speed/Duplex</th>
                <th>Customer</th>
            </tr>
        </thead>
        <tbody>

            {foreach from=$ports item=p}

            <tr>
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
</div>


<script type="text/javascript">
    {literal}
    var portReportDataSource = new YAHOO.util.DataSource( YAHOO.util.Dom.get( "portReportTable" ) );
    portReportDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;

    portReportDataSource.responseSchema = {
        fields: [
            {key:'Port Name'},
            {key:'Type'},
            {key:'Speed/Duplex'},
            {key:'Customer'}        ]
    };

    var portReportColumnDefs = [
        {key:'Port Name'},
        {key:'Type'},
        {key:'Speed / Duplex'},
        {key:'Customer'}
    ];

    var portReportDataTable = new YAHOO.widget.DataTable( "portReportContainer", portReportColumnDefs, portReportDataSource );
    {/literal}
</script>

</div>

{tmplinclude file="footer.tpl"}
