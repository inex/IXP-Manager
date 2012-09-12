

var oDataTable;

$(document).ready(function() {

	$( 'a[id|="list-delete"]' ).on( 'click', function( event ){

		event.preventDefault();
		url = $(this).attr("href");

	    bootbox.dialog( "Are you sure you want to delete this object?", [{
	    	"label": "Cancel",
	    	"class": "btn-primary"
	    },
	    {
	    	"label": "Delete",
	    	"class": "btn-danger",
	    	"callback": function() { document.location.href = url; }
	    }]);

    });


	oDataTable = $( '#frontend-list-table' ).dataTable({
        'fnDrawCallback': function() {
                if( oss_prefs != undefined && 'iLength' in oss_prefs && oss_prefs['iLength'] != $( "select[name='frontend-list-table_length']" ).val() )
                {
                    oss_prefs['iLength'] = parseInt( $( "select[name='frontend-list-table_length']" ).val() );
                    $.jsonCookie( 'oss_prefs', oss_prefs, oss_cookie_options );
                }
            },
        'iDisplayLength': ( typeof oss_prefs != 'undefined' && 'iLength' in oss_prefs )
        		? oss_prefs['iLength']
            	: {if isset( $options.defaults.table.entries )}{$options.defaults.table.entries}{else}10{/if},
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        'aoColumns': [
            {foreach $feParams->listColumns as $col => $cconf}
                {if not is_array( $cconf ) or not isset( $cconf.display ) or $cconf.display}
                    null,
                {/if}
            {/foreach}
            { 'bSortable': false, "bSearchable": false, "sWidth": "150px" }
        ]
    });
    $( '#frontend-list-table' ).show();

});




