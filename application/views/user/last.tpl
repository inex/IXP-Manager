{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="{genUrl controller=$controller action="list"}">{$frontend.pageTitle}</a> <span class="divider">/</span>
    </li>
    <li class="active">
        Last Logins
    </li>
</ul>
        
<table id="ixpDataTable" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" style="display: none;">
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

{literal}
<script type="text/javascript">
	oTable = $('#ixpDataTable').dataTable({
		"aaSorting": [[ 2, 'desc' ]],
		"sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
		"sPaginationType": "bootstrap",
		"iDisplayLength": 25,
	});

	$('#ixpDataTable').show();
</script>
{/literal}


{include file="footer.tpl"}
