{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g" style="margin-bottom: 70px;">

<table class="adminheading" border="0">
	<tr>
		<th class="Switch">Switch :: Port Report</th>
	</tr>
</table>

<p><br /></p>


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
