{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g" style="margin-bottom: 70px;">

<table class="adminheading" border="0">
	<tr>
		<th class="Meeting">INEX Members' Meetings</th>
	</tr>
</table>

<div class="meetings_index">
<p>
<form name="meeting_jumpto" class="form">
    <strong>Jump to:</strong>&nbsp;

    <select
        name="meetings_index"
        onChange="window.location.href=meeting_jumpto.meetings_index.options[selectedIndex].value">
    >

        <option></option>
        {foreach from=$entries item=e}
            <option value="#{$e.id}">{$e.date|date_format:"%A, %B %e, %Y"}</option>
        {/foreach}

    </select>
</form>
</p>
</div>


{tmplinclude file='meeting/core.tpl'}


</div>

{tmplinclude file="footer.tpl"}
