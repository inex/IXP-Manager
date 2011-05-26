{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

{include file="message.tpl"}

<div id='ajaxMessage'></div>

<div id="content">

<table>
<tr>
<td width="100%">

<table align="right">
<tr>
    <form method="post">
       <td>
	        <label for="dt_input">INEX Network: </label>
	        <select id="dt_input" name="vlan">
	            <option></option>
	            {foreach from=$vlans item=vlan}
	                <option value="{$vlan.number}" {if isset( $vlannum ) and $vlannum eq $vlan.number}selected{/if}>{$vlan.name}</option>
	            {/foreach}
	        </select>
        </td>
        <td>
            <input type="submit" name="submit" class="button" value="Filter" />
        </td>
    </form>
</tr>
</table>

</td>
</tr>
<tr>
<td>

<div id="mySwitchConfiguration">
    <table id="mySwitchConfigurationTable">
        <thead>
            <tr>
                <th>Member</th>
                <th>Switch</th>
                <th>Port</th>
                <th>Speed</th>
                <th>ASN</th>
                <th>Route Server</th>
                <th>IPv4 Address</th>
                <th>IPv6 Address</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
{foreach from=$swconf item=swc}

            <tr>
                <td><a href="{$swc.corpwww}">{$swc.name}</a></td>
                <td>{$swc.ViewSwitchDetailsByCustid.switch}</td>
                <td>{$swc.ViewSwitchDetailsByCustid.switchport}</td>
                <td>{$swc.ViewSwitchDetailsByCustid.speed}Mbps</td>
                <td>{$swc.autsys|asnumber}</td>
                <td>{if $swc.ViewSwitchDetailsByCustid.ViewVlaninterfaceDetailsByCustid.rsclient}Yes{else}No{/if}</td>
                <td>{$swc.ViewSwitchDetailsByCustid.ViewVlaninterfaceDetailsByCustid.ipv4address}</td>
                <td>{$swc.ViewSwitchDetailsByCustid.ViewVlaninterfaceDetailsByCustid.ipv6address}</td>
                <td>{$swc.ViewSwitchDetailsByCustid->getPhysicalInterfaceStatusString()}</td>
            </tr>

{/foreach}

        </tbody>
    </table>
</div>

</td>
</tr>
</table>

</div>
</div>

{literal}
<script>

	var myDataSource = new YAHOO.util.DataSource( YAHOO.util.Dom.get( "mySwitchConfigurationTable" ) );
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;

	myDataSource.responseSchema = {
	    fields: [
	        { key: "Member" },
	        { key: "Switch" },
            { key: "Port" },
            { key: "Speed" },
            { key: "ASN" },
            { key: "Route Server" },
            { key: "IPv4 Address" },
            { key: "IPv6 Address" },
            { key: "Status" }
	    ]
	};

	var myColumnDefs = [
	    { key: "Member" },
	    { key: "Switch" },
	    { key: "Port" },
        { key: "Speed" },
        { key: "ASN" },
        { key: "Route Server" },
        { key: "IPv4 Address" },
        { key: "IPv6 Address" },
        { key: "Status" }
	];

	var myDataTable = new YAHOO.widget.DataTable( "mySwitchConfiguration", myColumnDefs, myDataSource);

</script>
{/literal}

{include file="footer.tpl"}

