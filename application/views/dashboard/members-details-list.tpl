{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

{include file="message.tpl"}

<div id='ajaxMessage'></div>

<div id="content">

<div id="myMemberDetailsList">
    <table id="myMemberDetailsListTable">
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

</div>

{literal}
<script>

	var myDataSource = new YAHOO.util.DataSource( YAHOO.util.Dom.get( "myMemberDetailsListTable" ) );
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;

	myDataSource.responseSchema = {
	    fields: [
	        { key: "Member" },
	        { key: "Peering Email" },
            { key: "ASN" },
            { key: "NOC Phone" },
            { key: "NOC Hours" },
            { key: "" }
	    ]
	};

	var myColumnDefs = [
        { key: "Member" },
        { key: "Peering Email" },
        { key: "ASN" },
        { key: "NOC Phone" },
        { key: "NOC Hours" },
        { key: "" }
	];

	var myDataTable = new YAHOO.widget.DataTable( "myMemberDetailsList", myColumnDefs, myDataSource);

</script>
{/literal}

{include file="footer.tpl"}
