
$(document).ready(function() {

	{foreach $rsRouteTypes as $t}
	    $( '#summary-table-{$t}' ).dataTable({
	        'iDisplayLength': 25,
	        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
	        "sPaginationType": "bootstrap",
	        "aoColumns": [
                null,
                { "sType": "num-html" },
                { "sType": "num-html" },
                { "sType": "num-html" }
            ]
	    });
	    $( '#summary-table-{$t}' ).show();
	    
    {/foreach}
	
} );
