{tmplinclude file="header.tpl" pageTitle="IXP Manager :: "|cat:$frontend.pageTitle}
		
<div class="yui-g">

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



<table id="ixpDataTable" class="display" cellspacing="0" cellpadding="0" border="0" style="display: none;">

<thead>
<tr>
    {foreach from=$frontend.columns.displayColumns item=col}
        <th>{$frontend.columns.$col.label}</th>
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
                            <script>
                        		$( '#viewPanel-{$frontend.columns.$col.controller}-{$row.$field}-{$idcount}' ).click( function() {ldelim}
									ixpViewPanel( '{$frontend.columns.$col.label}', '{$frontend.columns.$col.controller}', {$row.$field} );
								{rdelim} );
                            </script>
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

    </tr>

{/foreach}

</tbody>

</table>

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

{if $hasCustomContextMenu}
    {tmplinclude file=$hasCustomContextMenu|cat:".html.tpl"}
{else}
    <ul id="myMenu" class="contextMenu">
        <li class="edit">
            <a href="#edit">Edit</a>
        </li>
        <li class="delete">
            <a href="#delete">Delete</a>
        </li>
    </ul>
{/if}


<div id="dialog-viewpanel" title="Loading..." style="display: none">
	<p>Loading...</p>
</div>

<script>
{literal}

function ixpViewPanel( title, controller, id ) {
	$.get(
        "{/literal}{genUrl}{literal}/" + controller + "/view/id/" + id + "/perspective/panel",
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

	oTable = $('#ixpDataTable').dataTable({

		{/literal}
		{if isset( $frontend.columns.sortDefaults )}
			{foreach from=$frontend.columns.displayColumns item=col name=items}
				{if $col eq $frontend.columns.sortDefaults.column}
			        "aaSorting": [[ {$smarty.foreach.items.index}, '{$frontend.columns.sortDefaults.order}' ]],
		        {/if}
        	{/foreach}
		{/if}
		{literal}
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength": 25,
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
                      
                  {rdelim}{if not $smarty.foreach.items.last},{/if}
              {/foreach}
        	  {literal}
  		]
	});

	$('#ixpDataTable').show();
	
	$( oTable.fnGetNodes() ).each( function( index, element ){
		{/literal}
		{if $hasCustomContextMenu}
	    	{tmplinclude file=$hasCustomContextMenu|cat:".js.tpl"}
		{else}
			{literal}
    		$( element ).contextMenu({
    
    				menu: "myMenu"
    			},
    			function( action, el, pos ) {
    
    				var aData = oTable.fnGetData( index );
    
    				switch( action )
    				{
    		            case 'edit':
    		                window.location.assign( "/ixp/{/literal}{$controller}{literal}/edit/id/"  + aData[0] );
                    		break;
    
                        case 'delete':
                            if( confirm( "Are you sure you want to delete this record?" ) )
                                window.location.assign( "/ixp/{/literal}{$controller}{literal}/delete/id/"  + aData[0] );
                            break;
    				}
    			}
    		);
    		{/literal}	
		{/if}
		{literal}		
	});

} );
{/literal}
</script>

{tmplinclude file="footer.tpl"}
