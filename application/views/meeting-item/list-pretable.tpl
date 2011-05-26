


<div class="meetings_index_container">
<div class="meetings_index">
<p>
<form name="meeting_jumpto" class="form" method="post">
    <strong>Meeting:</strong>&nbsp;

    <select
        name="meeting_id">

        <option value="0"></option>
        {foreach from=$entries item=e}
            <option value="{$e.id}" {if isset( $filter_id ) and $filter_id eq $e.id}selected{/if}>{$e.date|date_format:"%A, %B %e, %Y"}</option>
        {/foreach}

    </select>&nbsp;
    <input type="submit" name="submit" value="Filter" />
</form>
</p>
</div>
</div>

