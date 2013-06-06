
var dialog = null;

$( document ).ready( function(){
    $( 'a[id|="add-info"]' ).on("click", openAddDialog );
    $( 'a[id|="edit-row"]' ).on("click", openEditDialog );
});

function openEditDialog( event ){
    event.preventDefault();

    var id = substr( event.delegateTarget.id, 9 );
    var name = $( event.delegateTarget ).attr( 'data-name' );
    
    $( '#dialog_title' ).html( "Edit" );
    $( '#add_dialog_save' ).html( "Save" );
    $( '#protocol-element' ).hide( 'fast' );
    
    $( '#property-fields' ).html( getPropertyFields( name ) );

    $( '#values-' + id + ' > div' ).each( function(){
        var vid =  $( this ).attr( 'data-name' );
        $( '#' + vid ).val( $( this ).html() );
        if( vid == 'protocol' )
            $( '#' + vid ).trigger( "liszt:updated" );
    });


    openDialog( name );
};

function openAddDialog( event ){
    event.preventDefault();
    var name = $( event.target ).attr( 'data-name' );

    $( '#protocol-element' ).show( 'fast' );
    $( '#dialog_title' ).html( "Add new" );
    $( '#add_dialog_save' ).html( "Add" );
    $( '#property-fields' ).html( getPropertyFields( name ) );
    $( '#protocol' ).val('{NetInfo::PROTOCOL_IPV4}').trigger( "liszt:updated" );

    openDialog( name );
};

function openDialog( name ){

    $( '#property-name' ).html( getPropertyName( name ) );

    if( getHelp( name ) )
        $( '#add-dialog-help' ).html( getHelp( name ) );
    else
        $( '#add-dialog-help' ).html( '' );

    $( '.chzn-select' ).each( function( index ){
        $( this ).chosen();
        ossChosenFixWidth( $( this ) );
    });
    
    $( '#add_dialog_save' ).off( 'click' ).on( 'click', function(){
        $( '#form-add-property' ).trigger( 'submit' );
    });

    $( '#add_dialog_cancel' ).off( 'click' ).on( 'click', function(){
        dialog.modal('hide');
    });

    ossCloseOssMessages();
    $( '#add-mbody' ).height( 200 );
    $( '#add-property' ).off( 'shown' ).on( 'shown', function(){
        $( '#add-mbody' ).height( $( '#add-mbody-inner' ).height() );
    });

    dialog = $( '#add-property' ).modal( {
            backdrop: true,
            keyboard: true,
            show: true
    });
}

function isSingleton( id )
{
    return getFieldsData()[id]['singleton'];
}

function getHelp( id )
{
    return getFieldsData()[id]['help'];
}

function getPropertyName( id )
{
    return getFieldsData()[id]['name'];
}

function getPropertyFields( id )
{
    return getFieldsData()[id]['fields'];
}

function getFieldsData(){
    return {
    {foreach $options.netinfo.property as $name => $data}
        {$name} : {
        name: '{$data.name}',
        singleton: {if !isset( $data.singleton ) || $data.singleton }true{else}false{/if},
        help: {if isset( $data.help )}"{$data.help}"{else}false{/if},
        fields: '{if isset( $data.properties)}\
            {foreach $data.properties as $property => $propData }\
                {if is_array( $propData ) }\
                    <div class="control-group">\
                        <label class="control-label" for="{$name}_{$property}">{$propData.name}</label>\
                        <div class="controls">\
                            <select id="{$name}_{$property}" name="{$name}%dot%{$property}" class="chzn-select" chzn-fix-width="1">\
                                {foreach $propData.values as $propVal}\
                                    <option value="{$propVal}">{$propVal}</option>\
                                {/foreach}\
                            </select>\
                        </div>\
                    </div>\
                {else}\
                    <div class="control-group">\
                        <label class="control-label" for="{$name}_{$property}">{$propData}</label>\
                        <div class="controls">\
                            <input type="text" name="{$name}%dot%{$property}" id="{$name}_{$property}">\
                        </div>\
                    </div>\
                {/if}\
            {/foreach}\
        {else}\
            <div class="control-group">\
                <label class="control-label" for="{$name}">{$data.name}</label>\
                <div class="controls">\
                    <input type="text" name="{$name}" id="{$name}">\
                </div>\
            </div>\
        {/if}\
        '},
    {/foreach}
    }
};
