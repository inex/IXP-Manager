
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
                
            case 'ports':
                window.location.assign( {/literal}"{genUrl controller='switch-port' action='list' switchid=''}{literal}" + aData[0] );
                break;
                
            case 'report':
                window.location.assign( {/literal}"{genUrl controller=$controller action='port-report' id=''}{literal}" + aData[0] );
                break;
		}
	}
);


{/literal}
