{include file="header.tpl"}

<div class="page-content">

    <div class="page-header">
        <h1>User Admin for {$customer.name}</h1>
    </div>

{include file="message.tpl"}
<div id="ajaxMessage"></div>

<div class="alert alert-block alert-info">
    <h4 class="alert-heading">Remember! The admin account is only intended for creating users for your organisation.</h4>
    For full IXP Manager functionality, graphs and member information, log in under one of your user accounts.
</div>


<table id="ixpDataTable" class="table table-striped table-bordered" cellspacing="0" cellpadding="0" border="0">

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
                <i class="icon-remove"></i>
            {else}
                <i class="icon-ok"></i>
            {/if}
            </a>
        </td>
        <td>
            <a class="btn btn-mini" href="{genUrl controller="cust-admin" action="edit-user" id=$u->id}">
                <i class="icon-pencil"></i>
            </a>
        </td>
    </tr>

{/foreach}

</tbody>

</table>

<p>
    <br />
    <a class="btn btn-primary" href="{genUrl controller='cust-admin' action='add-user'}">
        Add New User
    </a>
</p>



<script>

$(document).ready(function() {

	{if not $skipInstructions}
		bootbox.alert(
			"<p><strong>Welcome to IXP Manager!</strong></p>"

			+ "<p>This account is an admin account and it can only be "
			+ "used to create user accounts for use within your organisation. "
			+ "Those users can then access the full functionality of this system."
		);
	{/if}
		
});

</script>


{include file="footer.tpl"}
