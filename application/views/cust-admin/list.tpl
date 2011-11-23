{tmplinclude file="header.tpl" pageTitle="IXP Manager :: "|cat:$frontend.pageTitle}

<div class="yui-g" style="height: 600px">

<table class="adminheading" border="0">
<tr>
    <th class="User">
        User Admin for {$customer.name}
    </th>
</tr>
</table>


{tmplinclude file="message.tpl"}

<div id="ajaxMessage"></div>

<table id="ixpDataTable" class="display" cellspacing="0" cellpadding="0" border="0" style="display: none;">

<thead>
<tr>
    <th>Username</th>
    <th>E-Mail</th>
    <th>Mobile</th>
    <th>Created</th>
    <th>Enabled</th>
    <th>Edit?</th>
</tr>
</thead>

<tbody>

{foreach from=$users item=u}

    <tr>
        <td>{$u->username}</td>
        <td>{$u->email}</td>
        <td>{$u->authorisedMobile}</td>
        <td>{$u->created}</td>
        <td align="center">
            <a href="{genUrl controller="cust-admin" action="toggle-enabled" id=$u->id}">
            {if $u->disabled}
                <img src="{genUrl}/images/icon_no.png" width="16" height="16" alt="[DISABLED]" title="Disabled - click to enable" />
            {else}
                <img src="{genUrl}/images/icon_yes.png" width="16" height="16" alt="[ENABLED]" title="Enabled - click to disable" />
            {/if}
            </a>
        </td>
        <td>
            <a href="{genUrl controller="cust-admin" action="edit-user" id=$u->id}">
                <img src="{genUrl}/images/joomla-admin/menu/edit.png" width="16" height="16" alt="[EDIT]" title="Click to edit" />
            </a>
        </td>
    </tr>

{/foreach}

</tbody>

</table>

<p>
    <form action="{genUrl controller='cust-admin' action='add-user'}" method="post">
        <input type="submit" name="submit" class="button" value="Add New User" />
    </form>
</p>

<div id="instructions" title="IXP Manager - Instructions" style="display: hide;">
	<p>
		Welcome to INEX's IXP Manager!
	</p>
	<p>
		This account is a customer admin account and it can only be 
		used to create sub users. Those sub users can then access the 
		full functionality of this system.
	</p>
</div>


<script>

$(document).ready(function() {ldelim}

	oTable = $('#ixpDataTable').dataTable({ldelim}

        "aaSorting": [[ 0, 'asc' ]],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength": 25,
	{rdelim}).show();

	$( '#instructions' ).dialog({ldelim}
		"autoOpen": false,
		"model": true
	{rdelim});

	{if not $skipInstructions}
		$( '#instructions' ).dialog( 'open' );
	{/if}
		
{rdelim});

</script>


</div>

{tmplinclude file="footer.tpl"}
