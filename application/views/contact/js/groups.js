var groups_cnt = {$row_idx} + 1;
var groups = {$jsonGroups};

$( document ).ready( function(){
    $( "select[id|='group-type']" ).bind( "change", updateGroups );
    $( "#select-group-{$row_idx}" ).bind( "change", changeGroup );
    $( "span[id|='remove-group']" ).bind( "click", removeRow );
    $( "div[id|='remove-group']" ).bind( "click", removeRow );
});
    
function updateGroups( event ){
    row = $( event.target ).attr( 'id' ).substr( $( event.target ).attr( 'id' ).lastIndexOf( '-' ) + 1 );

    id = '#select-group-' + row;

    $( id ).parent().show( "slow" ).removeClass( 'hidden' );
    ossChosenClear( id );
    
    var options = groups[$( event.target ).val()];
    
    if( options == undefined )
	    return;
	    
    var arrOptions = Array();
    for( group in options )
    {
        arrOptions.push({
        	'string': "<option value=\"" + options[group].id + "\">" + options[group].name + "</option>\n",
        	'name': options[group].name
    	});
    }

    arrOptions = arrOptions.sort( ossSortByName );
    var ddoptions = '<option value=""></option>';

    for( var i = 0;  i < arrOptions.length;  i++ )
        ddoptions += arrOptions[i].string;

    ossChosenSet( id, ddoptions );
};
    
function changeGroup( event ){
    row = $( event.target ).attr( 'id' ).substr( $( event.target ).attr( 'id' ).lastIndexOf( '-' ) + 1 );
    $( "#groups-table" ).append( _buildGroupsRow() ); 
    $( '#group-type-' + groups_cnt ).chosen();
    ossChosenFixWidth( $( '#group-type-' + groups_cnt ) );
    $( '#select-group-' + groups_cnt ).chosen();
    ossChosenFixWidth( $( '#select-group-' + groups_cnt ) );
    
    $( "select[id|='select-group']" ).unbind( "change" );
    $( '#groups-row-' + groups_cnt ).show( "slow" ).removeClass( 'hidden' );
    $( '#remove-group-' + row ).show( "slow" ).removeClass( 'hidden' );
    
    $( '#group-type-' + groups_cnt ).bind( "change", updateGroups );
    $( '#select-group-' + groups_cnt ).bind( "change", changeGroup );
    $( '#remove-group-' + groups_cnt ).bind( "click", removeRow );
    groups_cnt++;
};
    
function removeRow( event ){
    if( $( event.target ).is( "i" ) )
       var element = $( event.target ).parent();
    else
       var element = $( event.target );
    row = element.attr( 'id' ).substr( element.attr( 'id' ).lastIndexOf( '-' ) + 1 );
    $( '#groups-row-' + row ).hide( 'slow', function(){ $( this ).remove(); });
    
};
    
    
    
function _buildGroupsRow()
{
    var nrow =  '<tr id="groups-row-' + groups_cnt + '" class="hidden">\
    <td>\
    <select id="group-type-' + groups_cnt + '" class="chzn-select" chzn-fix-width="1" style="width: 150px;" data-placeholder="Type...">\
    <option value="0"></option>';
    {foreach $groups as $name => $value}
        {if $name != "ROLE" && isset( $options['contact']['group']['types'][$name] )}
                nrow += '<option value="{$name}">{$options["contact"]["group"]["types"][$name]}</option>';
            {/if}
    {/foreach}
    nrow += '</select>\
    </td>\
    <td><div class="hidden"><select id="select-group-' + groups_cnt + '" name="group[' + groups_cnt + ']" class="chzn-select" chzn-fix-width="1" style="width: 150px;" data-placeholder="Group..."></select></div></td>\
    <td><div class="hidden btn btn-mini"  id="remove-group-' + groups_cnt + '"><i class="icon-remove"></i></div></td>\
    </tr>';
    return nrow;
}
    