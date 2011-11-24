
{literal}

$( element ).contextMenu({

		menu: "myMenu"
	},
	function( action, el, pos ) {

		var aData = oTable.fnGetData( index );

		switch( action )
		{
            case 'edit':
                window.location.assign( "/ixp/{/literal}{$controller}{literal}/edit/id/" + aData[0] );
        		break;

            case 'delete':
                if( confirm( "Are you sure you want to delete this record?" ) )
                    window.location.assign( "/ixp/{/literal}{$controller}{literal}/delete/id/" + aData[0] );
                break;
                
            case 'message':
                window.location.assign( {/literal}"{genUrl controller='customer' action='send-welcome-email' id=''}{literal}" + aData[0] );
                break;
                
		}
	}
);


{/literal}
