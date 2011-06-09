{tmplinclude file="header.tpl"}

<!-- <div class="yui-g" style="height: 600px"> -->

<table class="adminheading" border="0">
<tr>
    <th class="Customer">
        IXP Members :: League Table ({foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach})
    </th>
</tr>
</table>


{tmplinclude file="message.tpl"}

<div class="content">

<p>
<form action="{genUrl controller="customer" action="league-table"}" method="post">
<table>
<tr>
    <td valign="middle"><strong>Metric:</strong></td>
    <td valign="middle">
        <select name="metric">
            {foreach from=$metrics key=cname item=cvalue}
                <option value="{$cvalue}" {if $metric eq $cvalue}selected{/if}>{$cname}</option>
            {/foreach}
        </select>
    </td>
    <td width="20"></td>
    <td valign="middle"><strong>Statistics Type:</strong></td>
    <td>
        <select name="category">
            {foreach from=$categories key=cname item=cvalue}
                <option value="{$cvalue}" {if $category eq $cvalue}selected{/if}>{$cname}</option>
            {/foreach}
        </select>
    </td>
    <td width="20"></td>
    <td valign="middle"><strong>Day:</strong></td>
    <td valign="middle"><input type="text" name="day" value="{$day}" size="10" maxlength="10" /></td>
    <td width="20">
        <input type="submit" name="submit" class="button" value="submit" />
    </td>
</tr>
</table>
</form>
</p>

<div id="myLeagueTableDiv">

<table id="myLeagueTable">

<thead>
    <tr>
        <th>Member</th>
        <th>A In</th>
        <th>A Out</th>
        <th>A Total</th>
        <th>M In</th>
        <th>M Out</th>
        <th>M Total</th>
        <th>T In</th>
        <th>T Out</th>
        <th>T Total</th>
    </tr>
</thead>
<tbody>

{foreach from=$trafficDaily item=td}

{if $metric eq 'max'}
	<tr>
        <td>{$td.Cust.shortname}</td>
        <td>{$td.Cust.name}</td>
	    <td>{$td.day_max_in}</td>
	    <td>{$td.day_max_out}</td>
	    <td>{$td.day_max_in+$td.day_max_out}</td>
	    <td>{$td.week_max_in}</td>
	    <td>{$td.week_max_out}</td>
	    <td>{$td.week_max_in+$td.week_max_out}</td>
	    <td>{$td.month_max_in}</td>
	    <td>{$td.month_max_out}</td>
	    <td>{$td.month_max_in+$td.month_max_out}</td>
	    <td>{$td.year_max_in}</td>
	    <td>{$td.year_max_out}</td>
	    <td>{$td.year_max_in+$td.year_max_out}</td>
	</tr>
{elseif $metric eq 'average'}
	<tr>
        <td>{$td.Cust.shortname}</td>
	    <td>{$td.Cust.name}</td>
	    <td>{$td.day_avg_in}</td>
	    <td>{$td.day_avg_out}</td>
	    <td>{$td.day_avg_in+$td.day_avg_out}</td>
	    <td>{$td.week_avg_in}</td>
	    <td>{$td.week_avg_out}</td>
	    <td>{$td.week_avg_in+$td.week_avg_out}</td>
	    <td>{$td.month_avg_in}</td>
	    <td>{$td.month_avg_out}</td>
	    <td>{$td.month_avg_in+$td.month_avg_out}</td>
	    <td>{$td.year_avg_in}</td>
	    <td>{$td.year_avg_out}</td>
	    <td>{$td.year_avg_in+$td.year_avg_out}</td>
	</tr>
{else}
	<tr>
        <td>{$td.Cust.shortname}</td>
	    <td>{$td.Cust.name}</td>
	    <td>{$td.day_tot_in}</td>
	    <td>{$td.day_tot_out}</td>
	    <td>{$td.day_tot_in+$td.day_tot_out}</td>
	    <td>{$td.week_tot_in}</td>
	    <td>{$td.week_tot_out}</td>
	    <td>{$td.week_tot_in+$td.week_tot_out}</td>
	    <td>{$td.month_tot_in}</td>
	    <td>{$td.month_tot_out}</td>
	    <td>{$td.month_tot_in+$td.month_tot_out}</td>
	    <td>{$td.year_tot_in}</td>
	    <td>{$td.year_tot_out}</td>
	    <td>{$td.year_tot_in+$td.year_tot_out}</td>
	</tr>
{/if}

{/foreach}

</tbody>
</table>

</div>

</div>

{if $metric eq 'max'}
    {assign var='scalefn' value='myScale'}
{elseif $metric eq 'average'}
    {assign var='scalefn' value='myScale'}
{else}
    {assign var='scalefn' value='myScaleTotal'}
{/if}


{literal}
<script>

//Define a custom format function for scale and type
var myScale = function( elCell, oRecord, oColumn, oData )
{
    switch( "{/literal}{$category}{literal}" )
    {
        case 'bytes':
            var strFormat = new Array( "Bytes", "KBytes", "MBytes", "GBytes", "TBytes" );
            // According to http://oss.oetiker.ch/mrtg/doc/mrtg-logfile.en.html, data is stored in bytes
            // oData = oData / 8.0;
            break;
        case 'errs':
        case 'discs':
        case 'pkts':
            var strFormat = new Array( "pps", "Kpps", "Mpps", "Gpps", "Tpps" );
            break;
        default:
            // According to http://oss.oetiker.ch/mrtg/doc/mrtg-logfile.en.html, data is stored in bytes
            oData = oData * 8.0;
            var strFormat = new Array( "bps", "Kbps", "Mbps", "Gbps", "Tbps" );
            break;
    }

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

var myScaleTotal = function( elCell, oRecord, oColumn, oData )
{
    switch( "{/literal}{$category}{literal}" )
    {
        case 'errs':
        case 'discs':
        case 'pkts':
            var strFormat = new Array( "p", "Kp", "Mp", "Gp", "Tp" );
            break;
        default:
            var strFormat = new Array( "B", "KB", "MB", "GB", "TB" );
            // According to http://oss.oetiker.ch/mrtg/doc/mrtg-logfile.en.html, data is stored in bytes
            // oData /= 8;
            break;
    }

    var retString = "";

    for( var i = 0; i < strFormat.length; i++ )
    {
        if( ( oData / 1000 < 1 ) || ( strFormat.length == i + 1 ) )
        {
            retString =  number_format( oData, 1 ) + strFormat[i];
            break;
        }
        else
        {
            oData = oData / 1000;
        }
    }

    elCell.innerHTML = retString;
};

var myDataSource = new YAHOO.util.DataSource( YAHOO.util.Dom.get( "myLeagueTable" ) );
myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
myDataSource.responseSchema = {
    fields: [
        { key: "shortname" },
        { key: "Member" },
        { key: "D In",    parser: "number" },
        { key: "D Out",   parser: "number" },
        { key: "D Total", parser: "number" },
        { key: "W In",    parser: "number" },
        { key: "W Out",   parser: "number" },
        { key: "W Total", parser: "number" },
        { key: "M In",    parser: "number" },
        { key: "M Out",   parser: "number" },
        { key: "M Total", parser: "number" },
        { key: "Y In",    parser: "number" },
        { key: "Y Out",   parser: "number" },
        { key: "Y Total", parser: "number" }
    ]
};

var myColumnDefs = [
        { key: "shortname", hidden: true },
        { key: "Member" },

        { key: "Day", children:
	            [
	                { key: "D In",    label: "In",    formatter: {/literal}{$scalefn}{literal}, sortable: true },
	                { key: "D Out",   label: "Out",   formatter: {/literal}{$scalefn}{literal}, sortable: true },
	                { key: "D Total", label: "Total", formatter: {/literal}{$scalefn}{literal}, sortable: true }
	            ]
	    },

        { key: "Week", children:
                [
                    { key: "W In",    label: "In",    formatter: {/literal}{$scalefn}{literal}, sortable: true },
                    { key: "W Out",   label: "Out",   formatter: {/literal}{$scalefn}{literal}, sortable: true },
                    { key: "W Total", label: "Total", formatter: {/literal}{$scalefn}{literal}, sortable: true }
                ]
        },

        { key: "Month", children:
                [
	                { key: "M In",    label: "In",    formatter: {/literal}{$scalefn}{literal}, sortable: true },
	                { key: "M Out",   label: "Out",   formatter: {/literal}{$scalefn}{literal}, sortable: true },
	                { key: "M Total", label: "Total", formatter: {/literal}{$scalefn}{literal}, sortable: true }
	            ]
        },

        { key: "Year", children:
                [
                    { key: "Y In",    label: "In",    formatter: {/literal}{$scalefn}{literal}, sortable: true },
                    { key: "Y Out",   label: "Out",   formatter: {/literal}{$scalefn}{literal}, sortable: true },
                    { key: "Y Total", label: "Total", formatter: {/literal}{$scalefn}{literal}, sortable: true }
                ]
        }
];

var myDataTable = new YAHOO.widget.DataTable(
	"myLeagueTableDiv",
	myColumnDefs,
	myDataSource,
	{}
);

// sort by month total by default
myDataTable.sortColumn( myDataTable.getColumn( "M Total" ), YAHOO.widget.DataTable.CLASS_DESC );

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
                innerWidth: '650px',
                height: '80%'
            }
        );
        //alert( "shortname is:" + oRecord.getData( "shortname" ) );
    }
);

</script>

{/literal}


{tmplinclude file="footer.tpl"}