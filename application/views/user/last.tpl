{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="User">
        Users :: Last Logins
    </th>
</tr>
</table>

<center>
<table id="ixpDataTable" class="display" cellspacing="0" cellpadding="0" border="0" style="display: none;">
    <thead>
    <tr>
        <th>Username</th>
        <th>Customer</th>
        <th>Last Login</th>
    </tr>
    </thead>

    <tbody>

    {foreach from=$last item=l}
    <tr>
        <td>{$l.u_username}</td>
        <td>{$l.c_shortname}</td>
        <td>{$l.up_value|date_format:"%Y-%m-%d %H:%M:%S"}</td>
    </tr>
    {/foreach}
    </tbody>
</table>
</center>

{literal}
<script type="text/javascript">
	oTable = $('#ixpDataTable').dataTable({
		"aaSorting": [[ 2, 'desc' ]],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength": 25,
	});

	$('#ixpDataTable').show();
</script>
{/literal}

</div>
</div>

{tmplinclude file="footer.tpl"}
