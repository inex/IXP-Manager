{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

{tmplinclude file="message.tpl"}

<div id='ajaxMessage'></div>

<div id="content">

<table id="ixpDataTable" class="display" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <th>Member</th>
            <th>Peering Email</th>
            <th>ASN</th>
            <th>NOC Phone</th>
            <th>NOC Hours</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
{foreach from=$memberDetails item=md}

        <tr>
            <td><a href="{$md.corpwww}">{$md.name}</a></td>
            <td><a href="mailto:{$md.peeringemail}">{$md.peeringemail}</a></td>
            <td>{$md.autsys|asnumber}</td>
            <td>{$md.nocphone}</td>
            <td>{$md.nochours}</td>
            <td><a href="{genUrl controller=$controller action="member-details" id=$md.id}">view</a></td>
        </tr>

{/foreach}

    </tbody>
</table>

</div>

</div>

{literal}
<script>

oTable = $('#ixpDataTable').dataTable({
	"bJQueryUI": true,
	"sPaginationType": "full_numbers",
	"iDisplayLength": 100
});

</script>
{/literal}

{tmplinclude file="footer.tpl"}
