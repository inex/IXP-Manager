{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li class="active">
        Switch Configuration
    </li>
</ul>

{include file="message.tpl"}
<div id='ajaxMessage'></div>

<div class="list_preamble_container">
    <div class="list_preamble">
        <p>
            <form id="vlan_jumpto" method="post">
                <strong>Peering LAN:</strong>&nbsp;
            
                <select onchange="document.vlan_jumpto.submit()" name="vlan">
                    <option value=""></option>
                    {foreach from=$vlans item=vlan}
                        <option value="{$vlan.number}" {if isset( $vlannum ) and $vlannum eq $vlan.number}selected{/if}>{$vlan.name}</option>
                    {/foreach}
                </select>

            </form>
        </p>
    </div>
</div>



<table id="ixpDataTable" class="table table-striped table-bordered" cellspacing="0" cellpadding="0" border="0">
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

<script>
	
    oTable = $('#ixpDataTable').dataTable({
        {if $hasIdentity and $user.privs eq 3}
            "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
        {else}
            "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        {/if}
        "sPaginationType": "bootstrap",
    	"iDisplayLength": 100
    });

</script>

{include file="footer.tpl"}
