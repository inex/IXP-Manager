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

	<table id="ixpDataTable" class="display" cellspacing="0" cellpadding="0" border="0" style="display: none;">

	<thead>
	    <tr>
	    	<th>Shortname>
	        <th>Member</th>
	        <th>95th Percentile</th>
	        <th>Cost/M</th>
	        <th>Cost/Y</th>
	    </tr>
	</thead>
	<tbody>

	{foreach from=$traffic95thMonthly item=td}

		<tr>
	        <td>{$td.Cust.shortname}</td>
	        <td>{$td.Cust.name|truncate:30}</td>
		    <td align="right">{$td.max_95th}</td>
            <td align="right">{$td.cost}</td>
            <td align="right">{$td.cost*12}</td>
		</tr>

	{/foreach}

	</tbody>
	</table>

	</div>

</td>
<td width="20"></td>
<td width="40%" valign="top" align="left">
    <h3>Notes</h3>

    <p>
    Click on any row in the table on the right for traffic graphs.
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
    The 95th percentile is calculated by sampling maximum value of the member's traffic (the greater of traffic
    in and out) every five minutes. We then order these by value and throw away the top 5%. The greatest
    value remaining in then the 95th percentile.
    </p>
</td>
</tr>
</table>

</div>

{literal}
<script>

//Define a custom format function for scale and type
var myScale = function( data )
{
	oData = data['aData'][data['iDataColumn']];
	
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

    return retString;
};

var addEuro = function( data )
{
    return '&euro;' + number_format( data['aData'][data['iDataColumn']], 0 );
};

{/literal}

$(document).ready(function() {ldelim}

    oTable = $('#ixpDataTable').dataTable({ldelim}

        "aaSorting": [[ 2, 'desc' ]],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength": 100,
		"aoColumnDefs": [ 	
            {ldelim} "bVisible": false, "aTargets": [ 0 ] {rdelim},
        	{ldelim} "fnRender": myScale, "aTargets": [ 2 ] {rdelim},
        	{ldelim} "fnRender": addEuro, "aTargets": [ 3, 4 ] {rdelim},
        	{ldelim} "sType": "numeric", "aTargets": [ 2, 3 ] {rdelim},
        	{ldelim} "bUseRendered": false, "aTargets": [ 2, 3 ] {rdelim}
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