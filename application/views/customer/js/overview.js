

$(document).ready(function() {

	{if $tab}
		$( '#customer-overview-tabs a[href="#{$tab}"]').tab( 'show' );
	{/if}
	
} );
