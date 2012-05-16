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

<div class="well">

    <h3>Physical Interfaces</h3>

    <table class="table table-striped table-bordered">

    <thead>
    <tr>
        <th>Location</th>
        <th>Switch</th>
        <th>Port</th>
        <th>Speed/Duplex</th>
        <th></th>
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
                <form action="{genUrl controller='physical-interface' action='edit' id=$int.id}" method="post">
                    <input type='hidden' name='return' value="virtual-interface/edit{'/id/'|cat:$object.id}" />
                    <input type="submit" name="submit" class="button" value="edit" />
                </form>
            </td>
            <td>
                <form action="{genUrl controller='physical-interface' action='delete' id=$int.id}" method="post">
                    <input type='hidden' name='return' value="virtual-interface/edit{'/id/'|cat:$object.id}" />
                    <input type="submit" name="submit" class="button" value="delete"
                        onClick="return confirm( 'Are you sure you want to delete this tuple?' );"
                    />
                </form>
            </td>
        </tr>

    {/foreach}

    </tbody>

    </table>


    <form action="{genUrl controller='physical-interface' action='add'}" method="post" style="text-align: right">
        <input type="submit" name="submit" class="button" value="Add New" />
        <input type='hidden' name='virtualinterfaceid' value='{$object.id}' />
        <input type='hidden' name='return' value="{'virtual-interface/edit/id/'|cat:$object.id}" />
    </form>

</div>


<div class="well">

    <h3>VLAN Interfaces</h3>

    <table class="table table-striped table-bordered">

        <thead>
        <tr>
            <th>VLAN Name</th>
            <th>VLAN ID</th>
            <th>IPv4 Address</th>
            <th>IPv6 Address</th>
            <th></th>
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
                    <form action="{genUrl controller='vlan-interface' action='edit' id=$int->id}" method="post">
                        <input type='hidden' name='return' value="virtual-interface/edit{'/id/'|cat:$object.id}" />
                        <input type="submit" name="submit" class="button" value="edit" />
                    </form>
                </td>
                <td>
                    <form action="{genUrl controller='vlan-interface' action='delete' id=$int->id}" method="post">
                        <input type='hidden' name='return' value="virtual-interface/edit{'/id/'|cat:$object.id}" />
                        <input type="submit" name="submit" class="button" value="delete"
                            onClick="return confirm( 'Are you sure you want to delete this tuple?' );"
                        />
                    </form>
                </td>
            </tr>

        {/foreach}

        </tbody>

        </table>

        <form action="{genUrl controller='vlan-interface' action='add'}" method="post" style="text-align: right">
            <input type="submit" name="submit" class="button" value="Add New" />
            <input type='hidden' name='virtualinterfaceid' value='{$object.id}' />
            <input type='hidden' name='return' value='virtual-interface/edit/id/{$object.id}' />
        </form>

</div>

{/if}


{include file="footer.tpl"}

