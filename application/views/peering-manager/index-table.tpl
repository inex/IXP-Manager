


<table class="table table-bordered table-striped">

    <thead>
        <tr>
            <th>Member</th>
            <th>ASN</th>
            <th>Peering</th>
            
            {if isset( $me.vlaninterfaces.10 )}
                <th>LAN 1</th>
            {/if}
            
            {if isset( $me.vlaninterfaces.12 )}
                <th>LAN 2</th>
            {/if}
        </tr>
    </thead>

    <tbody>

        {foreach from=$listOfCusts key=as item=p}
        
            {assign var=c value=$custs.$as}
        
            <tr>
                <td>{$c.name}</td>
                <td>{$c.autsys}</td>
                <td>{$c.peeringpolicy}</td>
                
                {foreach from=$vlans item=vlan}
                    {if isset( $c.$vlan )}
                        <td>
                            {foreach from=$protos item=proto}
                                {if isset( $c.$vlan.$proto )}
                                    {if $c.$vlan.$proto}
                                        <span class="badge badge-success">IPv4</span>
                                    {else}
                                        <span class="badge badge-important">IPv4</span>
                                    {/if}
                                {/if}
                            {/foreach}
                        </td>
                    {elseif isset( $me.vlaninterfaces.$vlan )}
                        <td></td>
                    {/if}
                {/foreach}
            </tr>
            
        {/foreach}

    </tbody>
</table>


