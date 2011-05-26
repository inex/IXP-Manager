
{foreach from=$connections item=connection}

    <h2>Connection #{counter}</h2>

    <table border="0">
    <tr>
        <td>
            <strong>Switch</strong>
        </td>
        <td>
            <input type="text" readonly  class="boxed" value="{$connection.Physicalinterface.0.Switchport.SwitchTable.name}" />
        </td>
        <td width="50"></td>
        <td>
            <strong>Switch Port</strong>
        </td>
        <td>
            <input type="text" readonly  class="boxed" value="{$connection.Physicalinterface.0.Switchport.name}" />
        </td>
    </tr>
    <tr>
        <td>
            <strong>Speed</strong>
        </td>
        <td>
            <input type="text" readonly  class="boxed" value="{$connection.Physicalinterface.0.speed} Mbps" />
        </td>
        <td></td>
        <td>
            <strong>Duplex</strong>
        </td>
        <td>
            <input type="text" readonly  class="boxed" value="{$connection.Physicalinterface.0.duplex}" />
        </td>
    </tr>
    </table>


    {foreach from=$connection.Vlaninterface item=interface}

        {assign var='vlanid' value=$interface.vlanid}

        <blockquote>

            <h3>Port {$connection.Physicalinterface.0.Switchport.SwitchTable.name}:{$connection.Physicalinterface.0.Switchport.name} - {$interface.Vlan.name}</h3>

            <table border="0">
            <tr>
                <td>
                    <strong>IPv4 Address</strong>
                </td>
                <td>
                    <input type="text" readonly  class="boxed" value="{$interface.Ipv4address.address}/{$networkInfo.$vlanid.4.masklen}" />
                </td>
                <td width="50"></td>
                <td>
                    <strong>IPv6 Address</strong>
                </td>
                <td>
                    <input type="text" readonly  class="boxed" value="{if $interface.Ipv6address.address}{$interface.Ipv6address.address}/{$networkInfo.$vlanid.6.masklen}{/if}" />
                </td>
            </tr>

            <tr>
                <td>
                    <strong>Multicast Enabled</strong>
                </td>
                <td>
                    <input type="text" readonly  class="boxed" value="{if $interface.mcastenabled eq 1}yes{else}no{/if}" />
                </td>
                <td></td>
                <td>
                    <strong>IPv6 Enabled</strong>
                </td>
                <td>
                    <input type="text" readonly  class="boxed" value="{if $interface.ipv6enabled eq 1}yes{else}no{/if}" />
                </td>
            </tr>

            <tr>
                <td>
                    <strong>Route Server Client?</strong>
                </td>
                <td>
                    <input type="text" readonly  class="boxed" value="{if $interface.rsclient eq 1}yes{else}no{/if}" />
                </td>
                <td></td>
                <td>
                    <strong>AS112 Client?</strong>
                </td>
                <td>
                    <input type="text" readonly  class="boxed" value="{if $interface.as112client eq 1}yes{else}no{/if}" />
                </td>
            </tr>

            </table>

        </blockquote>

    {/foreach}


{/foreach}
