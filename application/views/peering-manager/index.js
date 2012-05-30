
$( '#modal-peering-request' ).modal( { 'show': false, 'keyboard': false } ); 
 
$(document).ready( function() {

	$( 'button[id|="peering-request"]' ).on( 'click', function( event ){
		
		var custid = substr( event.target.id, 16 );
		
		$( '#modal-peering-request' ).modal( 'show' );
		
	});
	
});
