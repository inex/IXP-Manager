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

<table id="ixpDataTable" class="display" cellspacing="0" cellpadding="0" border="0" style="display: none;">

<thead>
    <tr>
        <th class="ui-state-default" ></th>
        <th class="ui-state-default" ></th>
        <th class="ui-state-default" colspan="3">Day</th>
        <th class="ui-state-default" colspan="3">Week</th>
        <th class="ui-state-default" colspan="3">Month</th>
        <th class="ui-state-default" colspan="3">Year</th>
    </tr>
    <tr>
        <th></th>
        <th>Member</th>
        <th>In</th>
        <th>Out</th>
        <th>Total</th>
        <th>In</th>
        <th>Out</th>
        <th>Total</th>
        <th>In</th>
        <th>Out</th>
        <th>Total</th>
        <th>In</th>
        <th>Out</th>
        <th>Total</th>
    </tr>
</thead>
<tbody>

{foreach from=$trafficDaily item=td}

{if $metric eq 'max'}
	<tr>
        <td>{$td.Cust.shortname}</td> 
        <td>{$td.Cust.name}</td>
	    <td align="right">{$td.day_max_in}</td>
	    <td align="right">{$td.day_max_out}</td>
	    <td align="right">{$td.day_max_in+$td.day_max_out}</td>
	    <td align="right">{$td.week_max_in}</td>
	    <td align="right">{$td.week_max_out}</td>
	    <td align="right">{$td.week_max_in+$td.week_max_out}</td>
	    <td align="right">{$td.month_max_in}</td>
	    <td align="right">{$td.month_max_out}</td>
	    <td align="right">{$td.month_max_in+$td.month_max_out}</td>
	    <td align="right">{$td.year_max_in}</td>
	    <td align="right">{$td.year_max_out}</td>
	    <td align="right">{$td.year_max_in+$td.year_max_out}</td>
	</tr>
{elseif $metric eq 'average'}
	<tr>
        <td>{$td.Cust.shortname}</td>  
	    <td>{$td.Cust.name}</td>
	    <td align="right">{$td.day_avg_in}</td>
	    <td align="right">{$td.day_avg_out}</td>
	    <td align="right">{$td.day_avg_in+$td.day_avg_out}</td>
	    <td align="right">{$td.week_avg_in}</td>
	    <td align="right">{$td.week_avg_out}</td>
	    <td align="right">{$td.week_avg_in+$td.week_avg_out}</td>
	    <td align="right">{$td.month_avg_in}</td>
	    <td align="right">{$td.month_avg_out}</td>
	    <td align="right">{$td.month_avg_in+$td.month_avg_out}</td>
	    <td align="right">{$td.year_avg_in}</td>
	    <td align="right">{$td.year_avg_out}</td>
	    <td align="right">{$td.year_avg_in+$td.year_avg_out}</td>
	</tr>
{else}
	<tr>
        <td>{$td.Cust.shortname}</td>
	    <td>{$td.Cust.name}</td>
	    <td align="right">{$td.day_tot_in}</td>
	    <td align="right">{$td.day_tot_out}</td>
	    <td align="right">{$td.day_tot_in+$td.day_tot_out}</td>
	    <td align="right">{$td.week_tot_in}</td>
	    <td align="right">{$td.week_tot_out}</td>
	    <td align="right">{$td.week_tot_in+$td.week_tot_out}</td>
	    <td align="right">{$td.month_tot_in}</td>
	    <td align="right">{$td.month_tot_out}</td>
	    <td align="right">{$td.month_tot_in+$td.month_tot_out}</td>
	    <td align="right">{$td.year_tot_in}</td>
	    <td align="right">{$td.year_tot_out}</td>
	    <td align="right">{$td.year_tot_in+$td.year_tot_out}</td>
	</tr>
{/if}

{/foreach}

</tbody>
</table>


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
var myScale = function( data )
{
	oData = data['aData'][data['iDataColumn']];

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
            retString =  number_format( oData, 0 ) + '&nbsp;' + strFormat[i];
            break;
        }
        else
        {
            oData = oData / 1000;
        }
    }

    return retString;
};

var myScaleTotal = function( data )
{
	oData = data['aData'][data['iDataColumn']];

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
            retString =  number_format( oData, 0 ) + strFormat[i];
            break;
        }
        else
        {
            oData = oData / 1000;
        }
    }

    return retString;
};

{/literal}

$(document).ready(function() {ldelim}

    oTable = $('#ixpDataTable').dataTable({ldelim}

        "aaSorting": [[ 6, 'desc' ]],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength": 100,
		"aoColumnDefs": [ 	
            {ldelim} "bVisible": false, "aTargets": [ 0 ] {rdelim},
        	{ldelim} "fnRender": {$scalefn}, "aTargets": [ 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13 ] {rdelim},
        	{ldelim} "sType": "numeric", "aTargets": [ 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13 ] {rdelim},
        	{ldelim} "bUseRendered": false, "aTargets": [ 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13 ] {rdelim}
        ],
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {ldelim}
                $( nRow ).click( function(){ldelim}
    	            $.fn.colorbox({ldelim}
                        open: true,
                        iframe: true,
                        href: '{genUrl
                                   controller='dashboard'
                                   action='statistics-drilldown'
                                   monitorindex='aggregate'
                                   mini='1'
                                   category=$category
                               }/shortname/' + aData[0],
                        transition: 'elastic',
                        innerWidth: '650px',
                        height: '80%'
                    {rdelim});
                {rdelim});
                 
      			return nRow;
	  		{rdelim}
	{rdelim}).show();

{rdelim});

</script>



{tmplinclude file="footer.tpl"}