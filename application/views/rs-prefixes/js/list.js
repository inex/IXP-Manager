
$(document).ready(function() {

	{foreach $rsRouteTypes as $t}
	    $( '#list-table-{$t}' ).dataTable({
	        'iDisplayLength': 10,
	        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
	        "sPaginationType": "bootstrap"   
	    });
	    $( '#list-table-{$t}' ).show();
	    
    {/foreach}
	
	{if $tab}
		$( '#routes-type-tabs a[href="#{$tab}"]').tab( 'show' );
	{/if}
	
} );
