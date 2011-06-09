{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<table class="adminheading" border="0">
	<tr>
		<th class="ChangeLog">IXP Manager Change Log</th>
	</tr>
</table>

<p>
This page details updates to the IXP Manager. When you first log in, we'll also alert you
to any new changes since the last time you opened this page by marking the <em>Change
Log</em> link as updated.
</p>

{if $newOnly neq false}
    <p>
        NB: We are only showing new entries since your last visit. Please
        <a href="{genUrl controller='change-log' action='read'}">click here to view all entries</a>.
    </p>
{/if}

<div class="change_log">

{foreach from=$entries item=e}

    <div class="change_log_item">

        <table>
        <tr>
            <th>
    	        {$e.livedate} ({$e.User.username}): {$e.title}
            </th>
        </tr>
        <tr>
            <td>
    	        {$e.details}
            </td>
        </tr>
	</table>

    </div>

{/foreach}

</div>


{tmplinclude file="footer.tpl"}
