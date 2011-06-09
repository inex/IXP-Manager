{tmplinclude file="header.tpl"}

<!-- <div class="yui-g" style="height: 600px"> -->

<table class="adminheading" border="0">
<tr>
    <th class="Customer">
        IXP Members :: Traffic Usage as 95th Percentile
    </th>
</tr>
</table>


{tmplinclude file="message.tpl"}

<div class="content">

<p>
<form action="{genUrl controller="customer" action="ninety-fifth"}" method="post">
<table>
<tr>
    <td valign="middle"><strong>Month:</strong></td>
    <td valign="middle">
        <select name="month">
            {foreach from=$months key=mName item=mValue}
                <option value="{$mValue}" {if $month eq $mValue}selected{/if}>{$mName}</option>
            {/foreach}
        </select>
    </td>
    <td width="20"></td>
    <td valign="middle"><strong>Cost / Mbps:</strong></td>
    <td valign="middle">
        &euro; <input name="cost" value="{$cost}" size="6" maxlength="10" />
    </td>
    <td width="20"></td>
    <td width="20">
        <input type="submit" name="submit" class="button" value="submit" />
    </td>
</tr>
</table>
</form>
</p>


<table width="100%" border="0">
<tr>
<td>

	<div id="myLeagueTableDiv">

	<table id="myLeagueTable">

	<thead>
	    <tr>
	        <th>Member</th>
	        <th>95th Percentile</th>
	    </tr>
	</thead>
	<tbody>

	{foreach from=$traffic95thMonthly item=td}

		<tr>
	        <td>{$td.Cust.shortname}</td>
	        <td>{$td.Cust.name}</td>
		    <td>{$td.max_95th}</td>
            <td>{$td.cost}</td>
		</tr>

	{/foreach}

	</tbody>
	</table>

	</div>

</td>
<td width="50"></td>
<td width="50%" valign="top" align="left">
    <h3>Notes</h3>

    <p>
    Click on any row in the table on the right for traffic graphs.
    </p>

    <p>
    Only values for May at the end of May should be considered completely accurate.
    </p>

    <p>
    Transit costs vary greatly from supplier to supplier and the more you commit to, the cheaper it gets.
    Ask Barry / Nick for sensible values. <strong>Different members should not be compared by cost.</strong>
    </p>

    <p>
    The <em>95<sup>th</sup> Percentile</em> is the standard way that IP transit providers measure and bill
    customers for traffic.
    </p>

    <p>
    E.g. As an IP transit customer, I may contract to commit to purchasing 100Mbps @ &euro;5/Mb and
    then pay &euro;7 for every Mbps burst above that. If on any given month, my traffic is 80Mbps as measured
    by the 95th percentile, my invoice will be 100Mbps x &euro;5 = &euro;500. But if it were 120Mbps, the
    invoice would be 100Mbps x &euro;5 + 20Mbps x &euro;7 = &euro;640.
    </p>

    <p>
    For INEX, by calculating the 95th percentile of our members' traffic, we can better evaluate the cost /
    benefit ratio of INEX membership versus transit.
    </p>

    <p>
    The 95th percentile is calculated by sampling maximum value of the member's traffic (the great of traffic
    in and out) every five minutes. The then order these by value and throw away the top 5%. The greatest
    value remaining in then the 95th percentile.
    </p>
</td>
</tr>
</table>

</div>

{literal}
<script>

//Define a custom format function for scale and type
var myScale = function( elCell, oRecord, oColumn, oData )
{
    // According to http://oss.oetiker.ch/mrtg/doc/mrtg-logfile.en.html, data is stored in bytes
    // BUT at point of data insertion to DB, we changed to bits
    var strFormat = new Array( "bps", "Kbps", "Mbps", "Gbps", "Tbps" );

    var retString = "";

    for( var i = 0; i < strFormat.length; i++ )
    {
        if( ( oData / 1000 < 1 ) || ( strFormat.length == i + 1 ) )
        {
            retString =  number_format( oData, 1 ) + '&nbsp;' + strFormat[i];
            break;
        }
        else
        {
            oData = oData / 1000;
        }
    }

    elCell.innerHTML = retString;
};

var addEuro = function( elCell, oRecord, oColumn, oData )
{
    elCell.innerHTML = '&euro;' + oData;
};


var myDataSource = new YAHOO.util.DataSource( YAHOO.util.Dom.get( "myLeagueTable" ) );
myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
myDataSource.responseSchema = {
    fields: [
        { key: "shortname" },
        { key: "Member" },
        { key: "nfth",    parser: "number" },
        { key: "cost",    parser: "currency" }
    ]
};

var myColumnDefs = [
        { key: "shortname", hidden: true },
        { key: "Member" },
        { key: "nfth", label: "95th",    formatter: myScale, sortable: true },
        { key: "cost", label: "Sample Cost/m",  formatter: addEuro },
];

var myDataTable = new YAHOO.widget.DataTable(
	"myLeagueTableDiv",
	myColumnDefs,
	myDataSource,
	{}
);


myDataTable.sortColumn( myDataTable.getColumn( "nfth" ), YAHOO.widget.DataTable.CLASS_DESC );

myDataTable.subscribe( 'rowClickEvent', function( oArgs )
	{
        var oRecord = this.getRecord( oArgs.target );
        $.fn.colorbox(
            {
                open: true,
                iframe: true,
                href: '{/literal}{genUrl
                        controller='dashboard'
                        action='statistics-drilldown'
                        monitorindex='aggregate'
                        mini='1'
                        category=$category
                    }{literal}/shortname/' + oRecord.getData( 'shortname' ),
                transition: 'elastic',
                innerWidth: '670px',
                height: '80%'
            }
        );
    }
);

</script>

{/literal}


{tmplinclude file="footer.tpl"}