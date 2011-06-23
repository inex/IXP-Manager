{tmplinclude file="header.tpl" pageTitle="IXP Manager :: "|cat:$frontend.pageTitle}

<script>
    YAHOO.namespace( 'IXP' );

    // View panel
    YAHOO.IXP.initViewPanel = function() {ldelim}
        var viewPanel = new YAHOO.widget.Panel( "viewPanel", {ldelim}
            close:       true,
            visible:     false,
            modal:       true,
            width:       '500px',
            fixedcenter: true
        {rdelim} );

        viewPanel.render();

        YAHOO.IXP.showViewPanel = function( o, p ) {ldelim}
            YAHOO.util.Dom.setStyle( 'viewPanel', 'display', 'block' );
            // YAHOO.util.Dom.get( 'viewPanelHeader' ).innerHTML = 'Barry ' + p.controller + ' ' + p.id;
            // load the appropriate view content

            var ajaxSuccess = function( o ) {ldelim}

                YAHOO.util.Dom.get( 'viewPanel' ).innerHTML = o.responseText;

                YAHOO.util.Event.addListener(
                    'view-container-close',
                    'click',
                    function() {ldelim}
                        viewPanel.hide();
                        YAHOO.util.Dom.get( 'viewPanel' ).innerHTML = '';
                    {rdelim}
                );

            {rdelim}

            var ajaxFailure = function( o ) {ldelim}

                YAHOO.util.Dom.get( 'viewPanel' ).innerHTML = " \
                    <div class=\"hd\">AJAX Error</div>\
                    <div class=\"bd\">\
                        <p>Error executing AJAX request:</p>\
                        <p>" + o.status + ": " + o.statusText + "\
                    </div>\
                    <div class=\"ft\">AJAX Error</div>";

            {rdelim}

            var callback = {ldelim}
                success: ajaxSuccess,
                failure: ajaxFailure
            {rdelim};

            var ajaxRequest = YAHOO.util.Connect.asyncRequest(
                    "GET",
                    "{genUrl}/" + p.controller + "/view/id/" + p.id + "/perspective/panel",
                    callback,
                    null
            );

            viewPanel.show();

        {rdelim}
    {rdelim}

    YAHOO.util.Event.onDOMReady( YAHOO.IXP.initViewPanel );

</script>

<div class="yui-g" style="height: 600px">

<table class="adminheading" border="0" style="display: block;">
<tr>
    <th class="{$frontend.name}">
        {$frontend.pageTitle}
    </th>
</tr>
</table>

{tmplinclude file="message.tpl"}

<div id="ajaxMessage"></div>


{assign var='_inc_file' value=$controller|cat:'/list-pretable.tpl'}
{include_if_exists file=$_inc_file}

{literal}
<style>
#autocomplete, #autocomplete_sn {
    height: 25px;
}
#dt_input {
    width: 150px;
}
#dt_input_sn {
    width: 100px;
}

/* This hides the autocomplete drop downs */
#dt_ac_container, #dt_ac_sn_container {
    display: none;
}

#dataTable {
    text-align: center;
    padding-top: 20px;
}

#dataTable table {
    margin-left:auto;
    margin-right:auto;
}

#dataTable, #dataTable .yui-dt-loading {
    text-align: center;
    background-color: transparent;
}

</style>
{/literal}

<table width="700" id="centre">
<tr>
    <td>
        <div id="autocomplete_cust">
            <label for="member">Customer Filter: </label><input id="member" name="member" type="text" value="">
        </div>
    </td>
    <td>
        <form action="{genUrl controller=$controller action='add'}" method="post">
            <input type="submit" name="submit" class="button" value="Add New" />
        </form>
    </td>
</tr>
</table>

<div id="myDatatableContainer">

<div id="myDatatable">

<table id="myTable">

<thead>
<tr>
    {foreach from=$frontend.columns.displayColumns item=col}
        <th>{$col}{$frontend.columns.$col.label}</th>
    {/foreach}
</tr>
</thead>

<tbody>
{foreach from=$rows item=row}

    <tr>
        {foreach from=$frontend.columns.displayColumns item=col}
            {if isset( $frontend.columns.$col.type )}
                {if $frontend.columns.$col.type eq 'hasOne'}
                    {counter name='id' assign='idcount'}
                    {assign var='model' value=$frontend.columns.$col.model}
                    {assign var='field' value=$frontend.columns.$col.field}
                    <td>
                        {if $row->$model->$field neq ''}
                        <span id="viewPanel-{$frontend.columns.$col.controller}-{$row->$model->id}-{$idcount}" class="blueLink">
                            <script>
                                YAHOO.namespace( 'IXP' );
                                YAHOO.util.Event.addListener(
                                    'viewPanel-{$frontend.columns.$col.controller}-{$row->$model->id}-{$idcount}',
                                    'click',
                                    YAHOO.IXP.showViewPanel, {ldelim}
                                            controller: '{$frontend.columns.$col.controller}',
                                            id: {$row->$model->id}
                                        {rdelim}
                                    );
                            </script>
                            {$row->$model->$field}
                        </span>
                        {/if}
                    </td>
                {elseif $frontend.columns.$col.type eq 'l2HasOne'}
                    {assign var='l1model' value=$frontend.columns.$col.l1model}
                    {assign var='l2model' value=$frontend.columns.$col.l2model}
                    {assign var='field'   value=$frontend.columns.$col.field}
                    <td>
                        {if $row->$l1model->$l2model->$field neq ''}
                            <span id="viewPanel-{$frontend.columns.$col.l2controller}-{$row->$l1model->$l2model->id}-{$idcount}" class="blueLink">
                                <script>
                                    YAHOO.namespace( 'IXP' );
                                    YAHOO.util.Event.addListener(
                                        'viewPanel-{$frontend.columns.$col.l2controller}-{$row->$l1model->$l2model->id}-{$idcount}',
                                        'click',
                                        YAHOO.IXP.showViewPanel, {ldelim}
                                                controller: '{$frontend.columns.$col.l2controller}',
                                                id: {$row->$l1model->$l2model->id}
                                            {rdelim}
                                        );
                                </script>
                                {$row->$l1model->$l2model->$field}
                            </span>
                        {/if}
                    </td>
                {elseif $frontend.columns.$col.type eq 'xlate'}
                    <td>
                        {assign var='index' value=$row->$col}
                        {$frontend.columns.$col.xlator.$index}
                    </td>
                {/if}
            {else}
                <td>{$row->$col}</td>
            {/if}
        {/foreach}

    </tr>

{/foreach}

</tbody>

</table>

</div>

</div>

<div id="viewPanel" class="viewPanel">
    <div class="hd" id="viewPanelHeader"></div>
    <div class="bd" id="viewPanelBody">Loading...</div>
    <div class="ft" id="viewPanelFooter"></div>
</div>

<script>
YAHOO.util.Event.addListener( window, "load", function() {ldelim}
    YAHOO.IXP.TableGenerator = new function() {ldelim}

        var myColumnDefs = [
            {foreach from=$frontend.columns.displayColumns item=col name=items}
                {ldelim}
                    key:"{$col}",
                    label:"{$frontend.columns.$col.label}"
                    {if isset( $frontend.columns.$col.hidden ) and $frontend.columns.$col.hidden}
                        , hidden:true
                    {/if}
                    {if isset( $frontend.columns.$col.sortable) and $frontend.columns.$col.sortable}
                        , sortable:true
                    {/if}
                {rdelim}{if not $smarty.foreach.items.last},{/if}
            {/foreach}
        ];

        this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get( "myTable" ) );
        this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
        this.myDataSource.responseSchema = {ldelim}
            fields: [
                        {foreach from=$frontend.columns.displayColumns item=col name=columns}
                            {ldelim}key:"{$col}"{rdelim}{if not $smarty.foreach.columns.last},{/if}
                        {/foreach}
            ] {rdelim};
		{literal}
		this.myDataSource.doBeforeCallback = function ( req, raw, res, cb ) {
            // This is the filter function
            var data     = res.results || [],
                filtered = [],
                i,l;

            if (req) {
                req = req.toLowerCase();
                for (i = 0, l = data.length; i < l; ++i) {
                    if( data[i].custid.toLowerCase().indexOf(req,200) > 0 ) {
                        filtered.push(data[i]);
                    }
                }
                res.results = filtered;
            }

            return res;
        };{/literal}        

        var oConfigs = {ldelim}
	        {if !isset( $frontend.pagination ) or $frontend.pagination neq false}
    	        paginator: new YAHOO.widget.Paginator({ldelim}
        	            rowsPerPage: 15
            	{rdelim}),
        	{/if}
            {if isset( $frontend.columns.sortDefaults )}
                sortedBy:{ldelim}key:"{$frontend.columns.sortDefaults.column}",dir:"{$frontend.columns.sortDefaults.order}"{rdelim}
            {/if}
        {rdelim};

        this.myDataTable = new YAHOO.widget.DataTable( "myDatatable", myColumnDefs, this.myDataSource, oConfigs );

        // Enable row highlighting
        this.myDataTable.subscribe( "rowMouseoverEvent", this.myDataTable.onEventHighlightRow   );
        this.myDataTable.subscribe( "rowMouseoutEvent",  this.myDataTable.onEventUnhighlightRow );

        // Enable row selection
        this.myDataTable.set( "selectionMode", "single" );
        this.myDataTable.subscribe( "rowDblclickEvent", function ( oArgs ) {ldelim}
                var elTarget = oArgs.target;
                var oRecord = this.getRecord( elTarget );
                window.location.assign( "/ixp/{$frontend.controller}/view/id/"  + oRecord.getData( 'id' ) );
            {rdelim}
        );

        {literal}
        this.filterTimeout = null;
        YAHOO.IXP.updateFilter  = function () {
            // Reset timeout
        	YAHOO.IXP.TableGenerator.filterTimeout = null;

            // Reset sort
            var state = YAHOO.IXP.TableGenerator.myDataTable.getState();
                state.sortedBy = { key:'username', dir:YAHOO.widget.DataTable.CLASS_ASC};

            // Get filtered data
            YAHOO.IXP.TableGenerator.myDataSource.sendRequest( YAHOO.util.Dom.get( 'member' ).value,{
                success : YAHOO.IXP.TableGenerator.myDataTable.onDataReturnInitializeTable,
                failure : YAHOO.IXP.TableGenerator.myDataTable.onDataReturnInitializeTable,
                scope   : YAHOO.IXP.TableGenerator.myDataTable,
                argument: state
            });
        };

        YAHOO.util.Event.on( 'member', 'keyup',function (e) {
            clearTimeout( this.filterTimeout );
            setTimeout( "YAHOO.IXP.updateFilter()", 600 );
        });        
        {/literal}

        {if $hasCustomContextMenu}
            {tmplinclude file=$hasCustomContextMenu}
        {else}

            {literal}
                var onContextMenuClick = function( p_sType, p_aArgs, p_myDataTable ) {
                    var task = p_aArgs[1];
                    if( task ) {
                        // Extract which TR element triggered the context menu
                        var elRow = this.contextEventTarget;
                        elRow = p_myDataTable.getTrEl( elRow );

                        if( elRow )
                        {
                            var oRecord = p_myDataTable.getRecord(elRow);

                            switch( task.groupIndex )
                            {
                                case 0:
                                    switch( task.index )
                                    {
                                        case 0:
                                            window.location.assign( "/ixp/{/literal}{$controller}{literal}/edit/id/"  + oRecord.getData( 'id' ) );
                                            break;

                                        case 1:
                                            if( confirm("Are you sure you want to delete this record?" ) )
                                                window.location.assign( "/ixp/{/literal}{$controller}{literal}/delete/id/"  + oRecord.getData( 'id' ) );
                                            break;
                                    }
                            }
                        }
                    }
                };

                var myContextMenu = new YAHOO.widget.ContextMenu( "mycontextmenu",
                        {trigger:this.myDataTable.getTbodyEl()}
                );

                myContextMenu.addItem("Edit", 0);
                myContextMenu.addItem("Delete", 0);

                myContextMenu.render("myDatatable");
                myContextMenu.clickEvent.subscribe( onContextMenuClick, this.myDataTable );
            {/literal}

        {/if}


    {rdelim};
{rdelim});
</script>

{if not isset( $frontend.disableAddNew ) or not $frontend.disableAddNew}

    <p>
        <form action="{genUrl controller=$controller action='add'}" method="post">
            <input type="submit" name="submit" class="button" value="Add New" />
        </form>
    </p>

{/if}

{if $hasPostContent}
    {tmplinclude file=$hasPostContent}
{/if}

</div>

{tmplinclude file="footer.tpl"}
