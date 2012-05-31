


<table class="table table-bordered table-striped">

    <thead>
        <tr>
            <th>Member</th>
            <th>ASN</th>
            <th>Policy</th>
            
            {if isset( $me.vlaninterfaces.10 )}
                <th>LAN 1</th>
            {/if}
            
            {if isset( $me.vlaninterfaces.12 )}
                <th>LAN 2</th>
            {/if}
            
            <th></th>
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
                
                <td width="200px">
                    <div class="btn-group">
                        <button id="peering-request-{$c.id}" data-days="{if isset( $peers[$c.id] )}{$peers[$c.id].email_days}{else}-1{/if}" class="btn btn-mini {if not $c.ispotential}disabled" disabled="disabled{/if}">
                            <i id="peering-request-icon-{$c.id}" class="{if isset( $peers[$c.id] ) and $peers[$c.id].emails_sent}icon-repeat{else}icon-envelope{/if}"></i> Request Peering
                        </button>
                        <button id="peering-notes-{$c.id}" class="btn btn-mini">
                            <i id="peering-notes-icon-{$c.id}" class="{if isset( $peers[$c.id] ) and strlen( $peers[$c.id].notes )}icon-star{else}icon-star-empty{/if}"></i> Notes
                        </button>
                    </div>
                </td>
            </tr>
            
            
        {/foreach}

    </tbody>
</table>


