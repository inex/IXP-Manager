{tmplinclude file="header.tpl"}

<div class="yui-g">

<div class="content">

{if $isEdit}
    <h2>{$frontend.pageTitle} :: Editing</h2>
{else}
    <h2>Create New Customer Interface  </h2>
{/if}

{tmplinclude file="message.tpl"}


{$form}

{if $isEdit}

    <dl>
    <dt></dt>

    <dd>

    <fieldset>

        <legend>Physical Interfaces</legend>

        <table class="ixptable">

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


    </fieldset>

    </dd>
    </dl>





    <dl class="zend_form">
    <dt></dt>

    <dd>

    <fieldset>

        <legend>VLAN Interfaces</legend>

        <table class="ixptable" id="myVlanTable">

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

    </fieldset>

    </dd>
    </dl>

{/if}

</div>

</div>

{tmplinclude file="footer.tpl"}

