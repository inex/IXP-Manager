{include file="header.tpl"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    {if isset( $cust )}
        <li>
            <a href="{genUrl controller='customer' action='dashboard' id=$cust.id}">{$cust.name}</a> <span class="divider">/</span>
        </li>
    {/if}
    <li>
        <a href="{genUrl controller='virtual-interface' action='list'}">Virtual Interfaces</a> <span class="divider">/</span>
    </li>
    <li class="active">
        {if $isEdit}Edit{else}Create New Customer Interface{/if}
    </li>
</ul>

{include file="message.tpl"}

<div class="well">
{$form}
</div>

{if $isEdit}

<div>

    <h3>
        Physical Interfaces
        <a class="btn btn-mini"
            href="{genUrl controller='physical-interface' action="add" virtualinterfaceid=$object.id}"><i class="icon-plus"></i></a>
    </h3>

    <table class="table">

    <thead>
    <tr>
        <th>Location</th>
        <th>Switch</th>
        <th>Port</th>
        <th>Speed/Duplex</th>
        <th></th>
    </tr>
    </thead>

    <tbody>
    {foreach from=$phyInts item=int}

        <tr>
            <td>
                {$int.Switchport.SwitchTable.Cabinet.Location.name}
            </td>
            <td>
                {$int.Switchport.SwitchTable.name}
            </td>
            <td>
                {$int.Switchport.name}
            </td>
            <td>
                {$int.speed}/{$int.duplex}
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-mini" href="{genUrl controller='physical-interface' action="edit"   id=$int.id}"><i class="icon-pencil"></i></a>
                    <a class="btn btn-mini" id="object-delete-{$int.id}" data-controller="physical-interface"><i class="icon-trash"></i></a>
                </div>
            </td>
        </tr>

    {/foreach}

    </tbody>

    </table>

    <br /><br />
</div>


<div>

    <h3>
        VLAN Interfaces
        <a class="btn btn-mini"
            href="{genUrl controller='vlan-interface' action="add" virtualinterfaceid=$object.id}"><i class="icon-plus"></i></a>
    </h3>

    
    <table class="table">

        <thead>
            <tr>
                <th>VLAN Name</th>
                <th>VLAN ID</th>
                <th>IPv4 Address</th>
                <th>IPv6 Address</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
        {foreach from=$vlanInts item=int}

            <tr>
                <td>
                    {$int.Vlan.name}
                </td>
                <td>
                    {$int.Vlan.number}
                </td>
                <td>
                    {$int.Ipv4address.address}
                </td>
                <td>
                    {$int.Ipv6address.address}
                </td>
                <td>
                    <div class="btn-group">
                        <a class="btn btn-mini" href="{genUrl controller='vlan-interface' action="edit"   id=$int.id}"><i class="icon-pencil"></i></a>
                        <a class="btn btn-mini" id="object-delete-{$int.id}" data-controller="vlan-interface"><i class="icon-trash"></i></a>
                    </div>
                </td>
            </tr>

        {/foreach}

        </tbody>

        </table>

</div>

{/if}

{include file="confirm-dialog.tpl"}

{include file="footer.tpl"}

