
<form action="{$element->getAction()}" method="{$element->getMethod()}" class="zend_form">

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
        <strong>{$element->our_ref->getLabel()}:</strong>
    </td>

    <td width="20"></td>

    <td>
        {$element->our_ref}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>{$element->installed->getLabel()}:</strong>
    </td>
    <td></td>
    <td>
        {$element->installed}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>{$element->removed->getLabel()}:</strong>
    </td>
    <td></td>
    <td>
        {$element->removed}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>{$element->custid->getLabel()}:</strong>
    </td>
    <td></td>
    <td>
        {$element->custid}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>{$element->notes->getLabel()}:</strong>
    </td>
    <td></td>
    <td>
        {$element->notes}
    </td>
</tr>


</table>



<h3>Physical Segments</h3>

<table border="0">

<tr>
    <td width="20"></td>

    <td>
        <strong>Our Reference:</strong>
    </td>

    <td width="20"></td>

    <td>
        <input name="pc_our_ref[]" type="text" size="20" maxlength="255" value="" />
    </td>

    <td width="20"></td>

    <td>
        <strong>Colo / Other Reference:</strong>
    </td>

    <td width="20"></td>

    <td>
        <input name="pc_colo_ref[]" type="text" size="20" maxlength="255" value="" />
    </td>
</tr>

<tr>
    <td></td>

    <td>
        <strong>Installed:</strong>
    </td>

    <td></td>

    <td>
        <input name="pc_installed[]" type="text" size="20" maxlength="255" value="" />
    </td>

    <td></td>

    <td>
        <strong>Removed:</strong>
    </td>

    <td></td>

    <td>
        <input name="pc_removed[]" type="text" size="20" maxlength="255" value="" />
    </td>
</tr>

<tr>
    <td></td>

    <td>
        <strong>A End - Type:</strong>
    </td>

    <td></td>

    <td>
        <select name="pc_a_type[]">
            <option></option>
            {foreach from=$physicalConnectionTypes item=type}
                <option value="1">{$type}</option>
            {/foreach}
        </select>
    </td>

    <td></td>

    <td>
        <strong>A End - Port:</strong>
    </td>

    <td></td>

    <td>
        <input name="pc_a_port[]" type="text" size="20" maxlength="255" value="" />
    </td>
</tr>

<tr>
    <td></td>

    <td>
        <strong>B End - Type:</strong>
    </td>

    <td></td>

    <td>
        <select name="pc_b_type[]">
            <option></option>
            {foreach from=$physicalConnectionTypes item=type}
                <option value="1">{$type}</option>
            {/foreach}
        </select>
    </td>

    <td></td>

    <td>
        <strong>B End - Port:</strong>
    </td>

    <td></td>

    <td>
        <input name="pc_b_port[]" type="text" size="20" maxlength="255" value="" />
    </td>
</tr>

</table>

<table border="0">

<tr>
    <td width="20"></td>
    <td>
    </td>
    <td width="20"></td>
    <td>
        {$element->cancel} &nbsp;&nbsp;&nbsp;&nbsp; {$element->commit}
    </td>
</tr>

</form>

