
$(document).ready(function() {

	{if $tab}
		$( '#routes-type-tabs a[href="#{$tab}"]').tab( 'show' );
	{/if}
	
} );
