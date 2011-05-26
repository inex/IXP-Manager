

{literal}
<script>
$(document).ready(function() {

	$("#auto_gen_ports_cb").click( function(){
	    if ($("#auto_gen_ports_cb").is(":checked"))
	    {
	        $("#auto_gen_ports_div").show("slow");
	    }
	    else
	    {
	        $("#auto_gen_ports_div").hide("slow");
	    }
	});

	$("#auto_gen_ports_cb:checked").each( function() {
        $("#auto_gen_ports_div").show();
	} );
});
</script>
{/literal}

<p>
<br /><br />
{$element->cb_autogen} {$element->cb_autogen->getLabel()}
</p>

<div id="auto_gen_ports_div" style="display: none;">

<p>
The below specified number of ports (front and back) will be automatically generated for the above patch panel. Each port
will be given a co-location reference as per the above the with suffix <code>.$port_number</code>. By
selecting <em>Edit before committing to database?</em>, you'll have the opportunity to change the
interface type, cable type and co-location reference.
</p>

<h3>Generate Ports for the Patch Panel</h3>

{if $element->isErrors()}
    <div id="message">
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
        <strong>{$element->num_ports->getLabel()}:</strong>
    </td>

    <td width="20"></td>

    <td>
        {$element->num_ports}
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <strong>{$element->edit->getLabel()}</strong>
    </td>
    <td></td>
    <td>
        {$element->edit}
    </td>
</tr>

</table>

<br /><br />
</div>


