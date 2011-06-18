


<div class="list_preamble_container">
<div class="list_preamble">

<p>
<form name="switch_jumpto" class="form" method="post">
	{if isset( $switchid ) and $switchid}
		See <a href="{genUrl controller='switch' action='port-report' id=$switchid}">port report</a>.&nbsp;&nbsp;&nbsp;&nbsp;
	{/if}

    <strong>Switch:</strong>&nbsp;

    <select
    	onchange="document.switch_jumpto.submit()"
        name="switchid">

        <option value="0"></option>
        {foreach from=$switches item=s}
            <option value="{$s.id}" {if isset( $switchid ) and $switchid eq $s.id}selected{/if}>{$s.name}</option>
        {/foreach}

    </select>
</form>
</p>
</div>
</div>

