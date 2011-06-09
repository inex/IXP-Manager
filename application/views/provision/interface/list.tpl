{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Provision New Interface"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
    <tr>
        <th class="Provision">Provisioning: New Interfaces</th>
    </tr>
</table>

{tmplinclude file="message.tpl"}

<div id="ajaxMessage"></div>



<div id="myDatatableContainer">
<div id="myDatatable">
<table id="myTable">

<thead>
<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Created By</th>
    <th>Started At</th>
    <th>Progress</th>
</tr>
</thead>

<tbody>

    {foreach from=$outstanding item=o}
    <tr>
        <td>{$o.id}</td>
        <td><a href="{genUrl controller="customer" action="edit" id=$o.id}">{$o.Cust.name}</a></td>
        <td><a href="{genUrl controller="user"     action="edit" id=$o.id}">{$o.CreatedBy.username}</a></td>
        <td>{$o.created_at|strtotime|date_format:"%Y-%m-%d %H:%m:%d"}<td>
        <td>{$o->stepsComplete()}</td>
    </tr>
    {/foreach}
</tbody>
</table>
</div>
</div>


<table align="right">
<tr><td>
    <form action="{genUrl controller="provision" action="interface-overview" new="yes"}" method="post">
        <input type="submit" name="submit" value="Provision New Interface..."  />
    </form>
</td></tr>
</table>







</div> <!-- content -->
</div> <!--  yui-g -->


<script type="text/javascript">
YAHOO.util.Event.addListener( window, "load", function() {ldelim}

    YAHOO_IXP_TableGenerator = new function() {ldelim}

        var myColumnDefs = [
            {ldelim} key:"id",      label:"ID",         sortable:true {rdelim},
            {ldelim} key:"cust",    label:"Customer",   sortable:true {rdelim},
            {ldelim} key:"user",    label:"Started By", sortable:true {rdelim},
            {ldelim} key:"started", label:"Started At", sortable:true {rdelim},
            {ldelim} key:"percent", label:"Progress", sortable:true {rdelim}
        ];

        this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get( "myTable" ) );
        this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
        this.myDataSource.responseSchema = {ldelim}
            fields: [
                {ldelim} key:"id"      {rdelim},
                {ldelim} key:"cust"    {rdelim},
                {ldelim} key:"user"    {rdelim},
                {ldelim} key:"started" {rdelim},
                {ldelim} key:"percent" {rdelim}
            ]
        {rdelim};

        var oConfigs = {ldelim}
            paginator: new YAHOO.widget.Paginator({ldelim}
                    rowsPerPage: 15
            {rdelim}),

            sortedBy:{ldelim}key:"started",dir:"ASC"{rdelim}
        {rdelim};

        this.myDataTable = new YAHOO.widget.DataTable( "myDatatable", myColumnDefs, this.myDataSource, oConfigs );

        // Enable row highlighting
        this.myDataTable.subscribe( "rowMouseoverEvent", this.myDataTable.onEventHighlightRow   );
        this.myDataTable.subscribe( "rowMouseoutEvent",  this.myDataTable.onEventUnhighlightRow );

        // Enable row selection
        this.myDataTable.set( "selectionMode", "single" );

        var onContextMenuClick = function( p_sType, p_aArgs, p_myDataTable ) {ldelim}
            var task = p_aArgs[1];
            if( task ) {ldelim}
                // Extract which TR element triggered the context menu
                var elRow = this.contextEventTarget;
                elRow = p_myDataTable.getTrEl( elRow );

                if( elRow )
                {ldelim}
                    var oRecord = p_myDataTable.getRecord(elRow);

                    switch( task.groupIndex )
                    {ldelim}
                        case 0:
                            switch( task.index )
                            {ldelim}
                                case 0:
                                    window.location.assign( "{genUrl controller="provision" action="interface-overview"}/id/"  + oRecord.getData( 'id' ) );
                                    break;

                                case 1:
                                    if( confirm("Are you sure you want to delete this record?" ) )
                                        window.location.assign( "{genUrl controller="provision" action="interface-delete"}/id/"  + oRecord.getData( 'id' ) );
                                    break;
                            {rdelim}
                    {rdelim}
                {rdelim}
            {rdelim}
        {rdelim};

        var myContextMenu = new YAHOO.widget.ContextMenu( "mycontextmenu",
            {ldelim}trigger:this.myDataTable.getTbodyEl(){rdelim}
        );

        myContextMenu.addItem("Edit", 0);
        myContextMenu.addItem("Delete", 0);

        myContextMenu.render("myDatatable");
        myContextMenu.clickEvent.subscribe( onContextMenuClick, this.myDataTable );


    {rdelim};
{rdelim});

</script>

{tmplinclude file="footer.tpl"}
