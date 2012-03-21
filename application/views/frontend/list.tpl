{include file="header.tpl" pageTitle="IXP Manager :: "|cat:$frontend.pageTitle}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li class="active">
        <a href="{genUrl controller=$controller action=$action}">{$frontend.pageTitle}</a>
    </li>
    <li class="pull-right">
        {if not isset( $frontend.disableAddNew ) or not $frontend.disableAddNew}
            <a class="btn btn-mini pull-right" href="{genUrl controller=$controller action="add"}"><i class="icon-plus"></i></a>
        {/if}
    </li>
</ul>

{include file="message.tpl"}
<div id="ajaxMessage"></div>


{if isset( $hasPreContent ) and $hasPreContent}
    {include file=$hasPreContent}
{/if}


<table id="ixpDataTable"  cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">

<thead>
<tr>
    {foreach from=$frontend.columns.displayColumns item=col}
        <th>{$frontend.columns.$col.label}</th>
    {/foreach}
    <th>&nbsp;</th>
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
                            		$( '#viewPanel-{$frontend.columns.$col.controller}-{$row->$model->id}-{$idcount}' ).click( function() {ldelim}
    									ixpViewPanel( '{$frontend.columns.$col.label}', '{$frontend.columns.$col.controller}', {$row->$model->id} );
    								{rdelim} );
                                </script>
                                {$row->$model->$field}
                            </span>
                        {/if}
                    </td>
                {elseif $frontend.columns.$col.type eq 'aHasOne'}
                    {counter name='id' assign='idcount'}
                    {assign var='model' value=$frontend.columns.$col.model}
                    {assign var='field' value=$frontend.columns.$col.ifield}
                    <td>
                        <span id="viewPanel-{$frontend.columns.$col.controller}-{$row.$field}-{$idcount}" class="blueLink">
                            {if $row.$field}
	                            <script>
	                        		$( '#viewPanel-{$frontend.columns.$col.controller}-{$row.$field}-{$idcount}' ).click( function() {ldelim}
										ixpViewPanel( '{$frontend.columns.$col.label}', '{$frontend.columns.$col.controller}', {$row.$field} );
									{rdelim} );
	                            </script>
                            {/if}
                            {$row.$col}
                        </span>
                    </td>
                {elseif $frontend.columns.$col.type eq 'l2HasOne'}
                    {assign var='l1model' value=$frontend.columns.$col.l1model}
                    {assign var='l2model' value=$frontend.columns.$col.l2model}
                    {assign var='field'   value=$frontend.columns.$col.field}
                    <td>
                        {if $row->$l1model->$l2model->$field neq ''}
                            <span id="viewPanel-{$frontend.columns.$col.l2controller}-{$row->$l1model->$l2model->id}-{$idcount}" class="blueLink">
                                <script>
                            		$( '#viewPanel-{$frontend.columns.$col.l2controller}-{$row->$l1model->$l2model->id}-{$idcount}' ).click( function() {ldelim}
    									ixpViewPanel( '{$frontend.columns.$col.label}', '{$frontend.columns.$col.l2controller}', {$row->$l1model->$l2model->id} );
    								{rdelim} );
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
                <td>{$row.$col}</td>
            {/if}
        {/foreach}
        
        <td>
            {if isset( $hasCustomContextMenu ) and $hasCustomContextMenu}
                {include file=$hasCustomContextMenu|cat:".html.tpl"}
            {else}
                <div class="btn-group">
                    <a class="btn btn-mini" href="{genUrl controller=$controller action="edit" id=$row.id}"><i class="icon-pencil"></i></a>
                    <a class="btn btn-mini" id="object-delete-{$row.id}"><i class="icon-trash"></i></a>
                </div>
            {/if}
        </td>

    </tr>

{/foreach}

</tbody>

</table>

{if isset( $hasPostContent ) and $hasPostContent}
    {include file=$hasPostContent}
{/if}


<div id="dialog-viewpanel" title="Loading..." style="display: none">
	<p>Loading...</p>
</div>

<div class="modal hide" id="modal-confirm">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Are you sure?</h3>
    </div>
    <div class="modal-body">
        <p>
            Deletion is <strong>a permanent action</strong> and it cannot be undone.
        </p>
        <p>
            Are you sure you want to delete this object?
        </p>
    </div>
    <div class="modal-footer">
        <a data-dismiss="modal" class="btn btn-success">Cancel</a>
        <a id="modal-confirm-action" href="{genUrl}" class="btn btn-danger">Delete</a>
    </div>
</div>

<script>

function ixpViewPanel( title, controller, id ) {
	$.get(
        "{genUrl}/" + controller + "/view/id/" + id + "/perspective/panel",
        function( data ){
        	$( '#dialog-viewpanel' ).html( data );
        	$( '#dialog-viewpanel' ).dialog( 'option', 'title', title );
        	$( '#dialog-viewpanel' ).dialog( 'open' );
    	});
}

$(document).ready(function() {

	$( "#dialog-viewpanel" ).dialog({
		width: 500,
		autoOpen: false,
		modal: true
	});

	$( "#modal-confirm" ).modal({
		'show': false
	});

	$('a[id|="object-delete"]').click( function( event ){

		var id = substr( $( this ).attr( 'id' ), 14 );
		$( '#modal-confirm-action' ).attr( 'href', "{genUrl controller=$controller action="delete"}/id/" + id );
		$( "#modal-confirm" ).modal( { 'show': true } );
	});
	
	{literal}
	oTable = $('#ixpDataTable').dataTable({

		{/literal}
		{if isset( $frontend.columns.sortDefaults )}
			{foreach from=$frontend.columns.displayColumns item=col name=items}
				{if $col eq $frontend.columns.sortDefaults.column}
			        "aaSorting": [[ {$smarty.foreach.items.index}, '{$frontend.columns.sortDefaults.order}' ]],
		        {/if}
        	{/foreach}
		{/if}
		{if $hasIdentity and $user.privs eq 3}
            "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
	    {else}
            "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
	    {/if}
		{literal}
		"iDisplayLength": 10,
		"sPaginationType": "bootstrap",
		"aoColumns": [
              {/literal}
              {foreach from=$frontend.columns.displayColumns item=col name=items}
                  {ldelim}
                      {if isset( $frontend.columns.$col.hidden ) and $frontend.columns.$col.hidden}
                          "bVisible": false,
                      {/if}
                      {if isset( $frontend.columns.$col.sortable) and $frontend.columns.$col.sortable}
                          "bSortable": true,
                      {else}
	                      "bSortable": false,
                      {/if}
                      
                  {rdelim},
              {/foreach}
                	  { "bSortable": false, "sWidth": '150px' }
        	  {literal}
  		]
	});

	$('#ixpDataTable').show();
	
} );
{/literal}
</script>

{include file="footer.tpl"}
