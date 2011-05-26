

{if $element->isErrors()}
    <div id="message2">
        <div class="message message-error">
            <ul>
            {foreach from=$element->getMessages() item=elements}
                {foreach from=$elements item=messages}
                    {foreach from=$messages item=msg}
                        <li>{$msg}</li>
                    {/foreach}
                {/foreach}
            {/foreach}
            </ul>
        </div>
    </div>
{/if}

<table border="0">

<tr>
    <td width="20"></td>

    <td>
        <strong>Name:</strong>
    </td>

    <td width="20"></td>

    <td>
        {$element->name}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>Cabinet:</strong>
    </td>
    <td></td>
    <td>
        {$element->cabinetid}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>Colocation Reference:</strong>
    </td>
    <td></td>
    <td>
        {$element->colo_ref}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>Cable Type:</strong>
    </td>
    <td></td>
    <td>
        {$element->cable_type}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>Interface Type:</strong>
    </td>
    <td></td>
    <td>
        {$element->interface_type}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>Allow Duplex:</strong>
    </td>
    <td></td>
    <td>
        {$element->allow_duplex}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>Notes:</strong>
    </td>
    <td></td>
    <td>
        {$element->notes}
    </td>
</tr>


</table>

