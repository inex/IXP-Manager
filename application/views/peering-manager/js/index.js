
$( '#modal-peering-request' ).modal( { 'show': false, 'keyboard': false } ); 
$( '#modal-peering-notes' ).modal( { 'show': false, 'keyboard': false } ); 

function ixpOpenPeeringRequestDialog( custid ) {
	// make sure we're "clean"
	$( '#modal-peering-request-body' ).html( "<p>Please wait... loading...</p>" );
	
	$( '#modal-peering-request-footer-close' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
	$( '#modal-peering-request-footer-send' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
	$( '#modal-peering-request-footer-marksent' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
	$( '#modal-peering-request-footer-sendtome' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
	
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
}

function ixpSendPeeringRequest( event ) {
	
	$( '#modal-peering-request-footer-close' ).attr('disabled', 'disabled' ).addClass( 'disabled' );
	$( '#modal-peering-request-footer-sendtome' ).attr('disabled', 'disabled' ).addClass( 'disabled' );
	$( '#modal-peering-request-footer-marksent' ).attr('disabled', 'disabled' ).addClass( 'disabled' );
	$( '#modal-peering-request-footer-send' ).attr('disabled', 'disabled' ).addClass( 'disabled' );

	// close all tooltips
	$("[rel=tooltip]").tooltip( 'hide' );

	var Throb = ossThrobberWithOverlay( 200, 15, 5, "#peering-request-container" );
	var custid = $( '#peering-request-form-custid' ).val();
	
	$.post( '{genUrl controller="peering-manager" action="peering-request"}', $( '#peering-request-form' ).serialize(), function( data ) {
		
		if( substr( data, 0, 4 ) == 'ERR:' ) {
			bootbox.dialog( substr( data, 4 ), { "OK": function() {
					Throb.stop();
					$("#overlay").fadeOut( "slow", function(){ $("#overlay").remove(); });
					$( '#modal-peering-request-footer-close' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
					$( '#modal-peering-request-footer-sendtome' ).attr('disabled', 'disabled' ).addClass( 'disabled' );
					$( '#modal-peering-request-footer-marksent' ).attr('disabled', 'disabled' ).addClass( 'disabled' );
					$( '#modal-peering-request-footer-send' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
				} }, { "animate": false } 
			);
			return;
		}
			
		if( substr( data, 0, 3 ) == 'OK:' ) {
			Throb.stop();
			$("#overlay").remove(); 
			$( '#modal-peering-request' ).modal( 'hide' );
			
			if( $( '#peering-request-form-sendtome' ).val() == '0' ) {
				$( '#peering-request-' + custid ).attr( 'data-days', 0 );
				$( '#peering-request-icon-' + custid ).attr( 'class', 'icon-repeat' );
				$( '#peering-notes-icon-' + custid ).attr( 'class', 'icon-star' );
			}
			
			bootbox.alert( substr( data, 3 ) );
			return;
		}

		$( '#modal-peering-request-body' ).html( data );
		Throb.stop();
		$("#overlay").fadeOut( "slow", function(){ $("#overlay").remove(); });
		$( '#modal-peering-request-footer-close' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
		$( '#modal-peering-request-footer-sendtome' ).attr('disabled', 'disabled' ).addClass( 'disabled' );
		$( '#modal-peering-request-footer-marksent' ).attr('disabled', 'disabled' ).addClass( 'disabled' );
		$( '#modal-peering-request-footer-send' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
		
		
	});
}

$(document).ready( function() {

	$( '#modal-peering-request-footer-close' ).on( 'click', function( event ){
		$( '#modal-peering-request' ).modal( 'hide' );
	});
	
	$( '#modal-peering-notes-footer-close' ).on( 'click', function( event ){
		$( '#modal-peering-notes' ).modal( 'hide' );
	});
	
	$( 'button[id|="peering-request"]' ).on( 'click', function( event ){
		
		var custid = substr( event.target.id, 16 );
		
		var days = $( '#' + event.target.id ).attr( 'data-days' );
		if( days >= 0 && days < 30 ) {
			bootbox.confirm( "Are you sure you want to send a peering request to this member? You already sent one only " + days + " days ago.", 
				function( result ) {
				    if( result ) {
				    	return ixpOpenPeeringRequestDialog( custid );
				    } else {
				        return;
				    }
				}
			);
		}
		else
			ixpOpenPeeringRequestDialog( custid );
	});

	$( 'button[id|="peering-notes"]' ).on( 'click', function( event ){
		
		var custid = substr( event.target.id, 14 );

		// make sure we're "clean"
		$( '#modal-peering-notes-message' ).val( "Please wait... loading..." );
		
		$( '#modal-peering-notes-footer-close' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
		$( '#modal-peering-notes-footer-save' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
		$( '#modal-peering-notes-message' ).attr( 'disabled', 'disabled' ).addClass( 'disabled' );
		
		$( '#modal-peering-notes-header-h3' ).html( "Peering Notes for " + $( '#peer-name-' + custid ).html() );
		
		$( '#modal-peering-notes' ).modal( 'show' );

		$.get( '{genUrl controller="peering-manager" action="peering-notes"}/custid/' + custid, function( data ) {
		    
			if( substr( data, 0, 4 ) == 'ERR:' ) {
				bootbox.dialog( substr( data, 4 ), { "OK": function() {} }, { "animate": false } );
				$( '#modal-peering-notes' ).modal( 'hide' );
				return;
			}
				
			if( substr( data, 0, 3 ) != 'OK:' ) {
				bootbox.dialog( "Unexpected error. Please contact support.", { "OK": function() {} }, { "animate": false } );
				$( '#modal-peering-notes' ).modal( 'hide' );
				return;
			}
			
			if( strlen( data ) > 3 )
				$( '#modal-peering-notes-message' ).val( substr( data, 3 ) );
			else
				$( '#modal-peering-notes-message' ).val( '' );

			$( '#modal-peering-notes-message' ).off( 'focus' );
			$( '#modal-peering-notes-message' ).one( 'focus', function( event ){
				var prmt = '{$date} [{$user->getUsername()}]: ';
				$( '#modal-peering-notes-message' ).val( prmt + "\n\n"  + $( '#modal-peering-notes-message' ).val() );
			    $( '#modal-peering-notes-message' ).caretTo( strlen( prmt ) );		
			});

			
			$( '#modal-peering-notes-custid' ).val( custid );
			$( '#modal-peering-notes-message' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
		});
	});

	$( '#modal-peering-notes-footer-save' ).on( 'click', function( event ) {
		$( '#modal-peering-notes-footer-close' ).attr('disabled', 'disabled' ).addClass( 'disabled' );
		$( '#modal-peering-notes-footer-save' ).attr('disabled', 'disabled' ).addClass( 'disabled' );

		var Throb = ossThrobberWithOverlay( 200, 15, 5, "#peering-notes-container" );
		var custid = $( '#modal-peering-notes-custid' ).val();
		
		$.post( '{genUrl controller="peering-manager" action="peering-notes"}', $( '#peering-notes-form' ).serialize(), function( data ) {
			
			if( substr( data, 0, 4 ) == 'ERR:' ) {
				bootbox.dialog( substr( data, 4 ), { "OK": function() {
						Throb.stop();
						$("#overlay").fadeOut( "slow", function(){ $("#overlay").remove(); });
						$( '#modal-peering-notes-footer-close' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
						$( '#modal-peering-notes-footer-save' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
					} }, { "animate": false } 
				);
				return;
			}
				
			if( substr( data, 0, 3 ) == 'OK:' ) {
				Throb.stop();
				$("#overlay").remove(); 
				$( '#modal-peering-notes' ).modal( 'hide' );
				$( '#peering-notes-icon-' + custid ).attr( 'class', 'icon-star' );
				
				bootbox.alert( substr( data, 3 ) );
				return;
			}

			Throb.stop();
			$("#overlay").fadeOut( "slow", function(){ $("#overlay").remove(); });
			$( '#modal-peering-notes-footer-close' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
			$( '#modal-peering-notes-footer-save' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
			
		});
	}); 
	

	
	
	$( '#modal-peering-request-footer-send' ).on( 'click', function( event ){
		$( '#peering-request-form-sendtome' ).val( '0' );
		$( '#peering-request-form-marksent' ).val( '0' );
		ixpSendPeeringRequest( event );
	}); 
	
	$( '#modal-peering-request-footer-sendtome' ).on( 'click', function( event ){
		$( '#peering-request-form-sendtome' ).val( '1' );
		$( '#peering-request-form-marksent' ).val( '0' );
		ixpSendPeeringRequest( event );
	}); 
	
	$( '#modal-peering-request-footer-marksent' ).on( 'click', function( event ){
		$( '#peering-request-form-sendtome' ).val( '0' );
		$( '#peering-request-form-marksent' ).val( '1' );
		ixpSendPeeringRequest( event );
	}); 
	

});
