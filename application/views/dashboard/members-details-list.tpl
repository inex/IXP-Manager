{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

{if $user.privs eq 3}
    <ul class="breadcrumb">
        <li>
            <a href="{genUrl}">Home</a> <span class="divider">/</span>
        </li>
        <li class="active">
            Member Details
        </li>
    </ul>
{else}
    <div class="page-content">
    
        <div class="page-header">
            <h1>Member Details</h1>
        </div>
{/if}
    
    
{include file="message.tpl"}
<div id='ajaxMessage'></div>

<table id="ixpDataTable" class="table table-striped table-bordered">
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

<script>

    oTable = $('#ixpDataTable').dataTable({
        {if $hasIdentity and $user.privs eq 3}
            "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
        {else}
            "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        {/if}
        "sPaginationType": "bootstrap",
    	"iDisplayLength": 100
    });

</script>

{include file="footer.tpl"}
