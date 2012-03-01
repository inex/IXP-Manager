
{foreach from=$connections item=connection}

<div class="row-fluid">

    <div class="span6">

        <h3>Connection #{counter}</h3>

        <br />
        
        <table class="table">
            <tr>
                <td>
                    <strong>Location</strong>
                </td>
                <td>
                    {$connection.Physicalinterface.0.Switchport.SwitchTable.Cabinet.Location.name}
                </td>
                <td width="50"></td>
                <td>
                    <strong>Cabinet</strong>
                </td>
                <td>
                    {$connection.Physicalinterface.0.Switchport.SwitchTable.Cabinet.cololocation}
                </td>
            </tr>
        <tr>
                <td>
                    <strong>Switch</strong>
                </td>
                <td>
                    {$connection.Physicalinterface.0.Switchport.SwitchTable.name}
                </td>
                <td></td>
                <td>
                    <strong>Switch Port</strong>
                </td>
                <td>
                    {$connection.Physicalinterface.0.Switchport.name}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Speed</strong>
                </td>
                <td>
                    {$connection.Physicalinterface.0.speed} Mbps
                </td>
                <td></td>
                <td>
                    <strong>Duplex</strong>
                </td>
                <td>
                    {$connection.Physicalinterface.0.duplex}
                </td>
            </tr>
        </table>


        {foreach from=$connection.Vlaninterface item=interface}
    
            {assign var='vlanid' value=$interface.vlanid}

            <br />
            <h4>{$interface.Vlan.name} - IP Details</h4>
            <br />
            
            <table  class="table table-condensed">
            <tr>
                <td>
                    <strong>IPv4 Address</strong>
                </td>
                <td>
                    {$interface.Ipv4address.address}/{$networkInfo.$vlanid.4.masklen}
                </td>
                <td width="50"></td>
                <td>
                    <strong>IPv6 Address</strong>
                </td>
                <td>
                    {if $interface.Ipv6address.address}{$interface.Ipv6address.address}/{$networkInfo.$vlanid.6.masklen}{/if}
                </td>
            </tr>

            <tr>
                <td>
                    <strong>Multicast Enabled</strong>
                </td>
                <td>
                    {if $interface.mcastenabled eq 1}yes{else}no{/if}
                </td>
                <td></td>
                <td>
                    <strong>IPv6 Enabled</strong>
                </td>
                <td>
                    {if $interface.ipv6enabled eq 1}yes{else}no{/if}
                </td>
            </tr>

            <tr>
                <td>
                    <strong>Route Server Client?</strong>
                </td>
                <td>
                    {if $interface.rsclient eq 1}yes{else}no{/if}
                </td>
                <td></td>
                <td>
                    <strong>AS112 Client?</strong>
                </td>
                <td>
                    {if $interface.as112client eq 1}yes{else}no{/if}
                </td>
            </tr>

            </table>
            
            <br /><br />
        {/foreach}

    </div>
    <div class="span6">
        
        <br /><br />
        <div class="well">
            <h4>Day Graph for {$connection.Physicalinterface.0.Switchport.SwitchTable.name} / {$connection.Physicalinterface.0.Switchport.name}</h4>
            <br />
        	<a href="{genUrl controller="dashboard" action="statistics-drilldown" shortname=$customer.shortname category='bits' monitorindex=$connection.Physicalinterface.0.monitorindex}">
    	        {genMrtgImgUrlTag shortname=$customer.shortname monitorindex=$connection.Physicalinterface.0.monitorindex}
            </a>
        </div>
        
    </div>
</div>

{/foreach}
