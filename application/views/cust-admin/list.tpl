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

<div id="myDatatable">

<table id="myTable">

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

</div>

<p>
    <form action="{genUrl controller='cust-admin' action='add-user'}" method="post">
        <input type="submit" name="submit" class="button" value="Add New User" />
    </form>
</p>

<div id="instructions"></div>


<script>

YAHOO.util.Event.addListener( window, "load", function() {ldelim}
    var TableGenerator = new function() {ldelim}

        var myColumnDefs = [
            {ldelim} key:"username", label:"Username", sortable:true {rdelim},
            {ldelim} key:"email",    label:"Email",    sortable:true {rdelim},
            {ldelim} key:"mobile",   label:"Mobile",   sortable:true {rdelim},
            {ldelim} key:"created",  label:"Created",  sortable:true {rdelim},
            {ldelim} key:"disabled", label:"Enabled",  sortable:true {rdelim},
            {ldelim} key:"edit", label:"Edit",  sortable:true {rdelim}
        ];

        this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get( "myTable" ) );
        this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
        this.myDataSource.responseSchema = {ldelim}
            fields: [
                     {ldelim} key:"username"{rdelim},
                     {ldelim} key:"email"{rdelim},
                     {ldelim} key:"mobile"{rdelim},
                     {ldelim} key:"created"{rdelim},
                     {ldelim} key:"disabled"{rdelim},
                     {ldelim} key:"edit"{rdelim}
            ]
        {rdelim};

        var oConfigs = {ldelim}
            paginator: new YAHOO.widget.Paginator({ldelim}
                    rowsPerPage: 15
            {rdelim}),

            sortedBy: {ldelim}key:"username", dir:"ASC"{rdelim}
        {rdelim};

        this.myDataTable = new YAHOO.widget.DataTable( "myDatatable", myColumnDefs, this.myDataSource, oConfigs );

        // Enable row highlighting
        this.myDataTable.subscribe( "rowMouseoverEvent", this.myDataTable.onEventHighlightRow   );
        this.myDataTable.subscribe( "rowMouseoutEvent",  this.myDataTable.onEventUnhighlightRow );

    {rdelim};

{rdelim});

{if not $skipInstructions}
//Define various event handlers for Dialog
var handleYes = function() {ldelim}
    this.hide();
{rdelim};

// Instantiate the Dialog
var instructionDialog =
    new YAHOO.widget.SimpleDialog( "instructionDialog",
             {ldelim}
               width: "500px",
               fixedcenter: true,
               visible: false,
               draggable: false,
               modal: true,
               close: true,
               text: "<p>Welcome to INEX's IXP Manager!</p><p>This account is a customer admin account and it can only be used to create sub users. Those sub users can then access the full functionality of this system.</p>",
               icon: YAHOO.widget.SimpleDialog.ICON_HELP,
               constraintoviewport: true,
               buttons: [ {ldelim} text:"OK",  handler:handleYes, isDefault:true {rdelim} ]
             {rdelim}
    );

instructionDialog.setHeader( "Welcome!" );
instructionDialog.render( "instructions" );
instructionDialog.show();
{/if}

</script>


</div>

{tmplinclude file="footer.tpl"}
