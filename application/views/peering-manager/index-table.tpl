


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
        
            {if $p}
            <tr>
                <td id="peer-name-{$c.id}"}>{$c.name}</td>
                <td>{$c.autsys}</td>
                <td>{$c.peeringpolicy}</td>
                
                {foreach from=$vlans item=vlan}
                    {if isset( $c.$vlan )}
                        <td>
                            {foreach from=$protos item=proto}
                                {if isset( $c.$vlan.$proto )}
                                    {if $c.$vlan.$proto}
                                        <span class="badge badge-success">IPv{$proto}</span>
                                    {else}
                                        <span class="badge badge-important">IPv{$proto}</span>
                                    {/if}
                                {/if}
                            {/foreach}
                        </td>
                    {elseif isset( $me.vlaninterfaces.$vlan )}
                        <td></td>
                    {/if}
                {/foreach}
                
                <td width="220px">
                    <div class="btn-group">
                        <button id="peering-request-{$c.id}" data-days="{if isset( $peers[$c.id] )}{$peers[$c.id].email_days}{else}-1{/if}" class="btn btn-mini {if not $c.ispotential}disabled" disabled="disabled{/if}">
                            <i id="peering-request-icon-{$c.id}" class="{if isset( $peers[$c.id] ) and $peers[$c.id].emails_sent}icon-repeat{else}icon-envelope{/if}"></i> Request Peering
                        </button>
                        <button id="peering-notes-{$c.id}" class="btn btn-mini">
                            <i id="peering-notes-icon-{$c.id}" class="{if isset( $peers[$c.id] ) and strlen( $peers[$c.id].notes )}icon-star{else}icon-star-empty{/if}"></i> Notes
                        </button>
                        <button class="btn btn-mini {if isset( $peers[$c.id] ) and ( $peers[$c.id].peered or $peers[$c.id].rejected )}btn-info{/if} dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{genUrl controller="peering-manager" action="mark-peered" custid=$c.id}">
                                    {if isset( $peers[$c.id] ) and $peers[$c.id].peered}
                                        Unmark as Peered
                                    {else}
                                        Move to Peered
                                    {/if}
                                </a>
                            </li>
                            <li>
                                <a href="{genUrl controller="peering-manager" action="mark-rejected" custid=$c.id}">
                                    {if isset( $peers[$c.id] ) and $peers[$c.id].rejected}
                                        Unmark as Rejected / Ignored
                                    {else}
                                        Move to Rejected / Ignored
                                    {/if}
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            {/if}
            
        {/foreach}

    </tbody>
</table>


