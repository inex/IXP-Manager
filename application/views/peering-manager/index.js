
$( '#modal-peering-request' ).modal( { 'show': false, 'keyboard': false } ); 
 
$(document).ready( function() {

	$( '#modal-peering-request-footer-close' ).on( 'click', function( event ){
		$( '#modal-peering-request' ).modal( 'hide' );
	});
	
	$( 'button[id|="peering-request"]' ).on( 'click', function( event ){
		
		var custid = substr( event.target.id, 16 );
		
		// make sure we're "clean"
		$( '#modal-peering-request-body' ).html( "<p>Please wait... loading...</p>" );
		$( '#modal-peering-request-footer-close' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
		$( '#modal-peering-request-footer-send' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
		
		$( '#modal-peering-request' ).modal( 'show' );
		
		$.get( '{genUrl controller="peering-manager" action="peering-request"}/custid/' + custid, function( data ) {
		    
			if( substr( data, 0, 4 ) == 'ERR:' ) {
				bootbox.dialog( substr( data, 4 ), { "OK": function() {} }, { "animate": false } );
				$( '#modal-peering-request' ).modal( 'hide' );
				return;
			}
				
			if( substr( data, 0, 11 ) != '<!-- OK -->' ) {
				bootbox.dialog( "Unexpected error. Please contact support.", { "OK": function() {} }, { "animate": false } );
				$( '#modal-peering-request' ).modal( 'hide' );
				return;
			}
			
			$( '#modal-peering-request-body' ).html( data );
			$( '#peering-request-form-custid' ).val( custid );
		});
		
	});

	$( '#modal-peering-request-footer-send' ).on( 'click', function( event ){
		
		$( '#modal-peering-request-footer-close' ).attr('disabled', 'disabled' ).addClass( 'disabled' );
		$( '#modal-peering-request-footer-send' ).attr('disabled', 'disabled' ).addClass( 'disabled' );

		var Throb = tt_throbberWithOverlay( 200, 15, 5, "#peering-request-container" );
		
		$.post( '{genUrl controller="peering-manager" action="peering-request"}', $( '#peering-request-form' ).serialize(), function( data ) {
			
			if( substr( data, 0, 4 ) == 'ERR:' ) {
				bootbox.dialog( substr( data, 4 ), { "OK": function() {
						Throb.stop();
						$("#overlay").fadeOut( "slow", function(){ $("#overlay").remove(); });
						$( '#modal-peering-request-footer-close' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
						$( '#modal-peering-request-footer-send' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
					} }, { "animate": false } 
				);
				return;
			}
				
			if( substr( data, 0, 3 ) == 'OK:' ) {
				Throb.stop();
				$("#overlay").remove(); 
				$( '#modal-peering-request' ).modal( 'hide' );
				
				bootbox.alert( substr( data, 3 ) );
				return;
			}
	
			$( '#modal-peering-request-body' ).html( data );
			Throb.stop();
			$("#overlay").fadeOut( "slow", function(){ $("#overlay").remove(); });
			$( '#modal-peering-request-footer-close' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
			$( '#modal-peering-request-footer-send' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
			
		});
		
	});
	
	
});
