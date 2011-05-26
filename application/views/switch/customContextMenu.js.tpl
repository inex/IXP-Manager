
{literal}

var onContextMenuClick = function( p_sType, p_aArgs, p_myDataTable )
{
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
                            window.location.assign( {/literal}"{genUrl controller=$controller action='edit'}{literal}/id/"  + oRecord.getData( 'id' ) );
                            break;

                        case 1:
                            if( confirm("Are you sure you want to delete this switch entry?" ) )
                                window.location.assign( {/literal}"{genUrl controller=$controller action='delete'}{literal}/id/"  + oRecord.getData( 'id' ) );
                            break;
                    }
                    break;

                case 1:
                    switch( task.index )
                    {
                        case 0:
                            window.location.assign( {/literal}"{genUrl controller=$controller action='port-report' id=''}{literal}"  + oRecord.getData( 'id' ) );
                            break;
                    }
				    break;

            }
        }
    }
};

var myContextMenu = new YAHOO.widget.ContextMenu( "mycontextmenu",
        {trigger:this.myDataTable.getTbodyEl()}
);

myContextMenu.addItem("Edit", 0   );
myContextMenu.addItem("Delete", 0 );

myContextMenu.addItem("View Port Report...", 1 );

myContextMenu.render("myDatatable");
myContextMenu.clickEvent.subscribe( onContextMenuClick, this.myDataTable );



{/literal}
