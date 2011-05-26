
{assign var='side' value=$element->side->getValue()}
<h2>Port {$element->port->getValue()} ({if $side eq 1}Back{else}Front{/if})</h2>

{$element->port}
{$element->side}


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
        <strong>{$element->type->getLabel()}:</strong>
    </td>

    <td width="20"></td>

    <td>
        {$element->type}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>{$element->cable_type->getLabel()}:</strong>
    </td>
    <td></td>
    <td>
        {$element->cable_type}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>{$element->colo_ref->getLabel()}:</strong>
    </td>
    <td></td>
    <td>
        {$element->colo_ref}
    </td>
</tr>

</table>

