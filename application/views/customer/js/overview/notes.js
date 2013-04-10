
function coNotesOpenDialog( event ) {
	$( "#co-notes-dialog" ).modal();
}

function coNotesEditDialog( event ) {
	var noteid = substr( event.delegateTarget.id, 14 );
	
	$.getJSON( '{genUrl controller="customer-notes" action="ajax-get"}/id/' + noteid, function( data ){
			if( data['error'] ) {
				bootbox.alert( "Error! Error getting the note from the server." );
				return;
			}
			
			$( "#co-notes-fadd"        ).html( 'Save' );
			$( "#co-notes-ftitle"      ).val( data['title'] );
			$( "#co-notes-fnote"       ).val( data['note']  );
			$( "#notes-dialog-noteid"  ).val( data['id'] );
			$( "#co-notes-dialog-date" ).html( 'Note first created: ' + data['created'] );
			
			if( data['private'] )
				$( "#co-notes-fpublic" ).prop( 'checked', false );
			else
				$( "#co-notes-fpublic" ).prop( 'checked', true );
			
			coNotesPublicCheckbox();
			$( "#co-notes-dialog-title-action" ).html( 'Edit' );
			coNotesOpenDialog( event );
		})
		.fail( function() {
			bootbox.alert( "Error! Could not get the note from the server." );
			return;
		});
}

function coNotesViewDialog( event ) {
	var noteid = substr( event.delegateTarget.id, 14 );
	
	$.getJSON( '{genUrl controller="customer-notes" action="ajax-get"}/id/' + noteid, function( data ){
			if( data['error'] ) {
				bootbox.alert( "Error! Error getting the note from the server." );
				return;
			}
			
			$( "#co-notes-view-dialog-title" ).html( data['title'] );
			$( "#co-notes-view-dialog-note"  ).html( data['note'] );
			$( "#co-notes-view-dialog-date"  ).html( 'Note first created: ' + data['created'] );
			$( "#co-notes-view-dialog" ).modal();
		})
		.fail( function() {
			bootbox.alert( "Error! Could not get the note from the server." );
			return;
		});
}

function coNotesDelete( event ) {
	var noteid = substr( event.delegateTarget.id, 15 );

	bootbox.confirm( "Are you sure you want to delete this note?", function(result) {
		if( result ) {
			$.getJSON( '{genUrl controller="customer-notes" action="ajax-delete"}/id/' + noteid, function( data ){
				if( data['error'] ) {
					bootbox.alert( "Error! Server side error deleting the note." );
					return;
				}

				$( "#co-notes-table-row-" + noteid ).fadeOut( 'slow', function() {
					$( "#co-notes-table-row-" + noteid ).remove();	
				});
			})
			.fail( function() {
				bootbox.alert( "Error! Could not delete the note from the server." );
				return;
			});
		}
	}); 
}

function coNotesView( event ) {
	var noteid = substr( event.delegateTarget.id, 15 );
	
}

function coNotesSubmitDialog( event ) {
	event.preventDefault();

	// validation - just make sure there's a title
	if( $( "#co-notes-ftitle" ).val().length == 0 ){
		bootbox.alert( "Error! A title for the note is required.", function() {
			$( "#co-notes-ftitle" ).focus();
		});
		return;
	}
	
	$.post( '{genUrl controller="customer-notes" action="ajax-add"}', $( "#co-notes-form" ).serialize(), coNotesPost, 'json' )
		.fail( function() {
			bootbox.alert( "Error! Could not save your note." );
		});
}

function coNotesPublicCheckbox() {
	if( $( "#co-notes-fpublic" ).is( ':checked' ) )
		$( "#co-notes-warning" ).show();
	else
		$( "#co-notes-warning" ).hide();
}

function coNotesPost( data, textStatus, jqXHR ) {
	
	// server-side form validation fails:
	if( data['error'] ) {
		bootbox.alert( "Error! Your note could not be saved." );
		return;
	}

	$( "#co-notes-dialog" ).modal( 'hide' );
	
	if( $( "#co-notes-fadd" ).html() == 'Add' ) {
		$( "#co-notes-table-tbody" ).prepend(
			"<tr class=\"hide\" id=\"co-notes-table-row-" + data['noteid'] + "\">"
			    + "<td>" + $( "#co-notes-ftitle" ).val() + "</td>"
			    + "<td>" + "<span class=\"label label-"
			        + ( $( "#co-notes-fpublic" ).is( ':checked' ) ? "success\">PUBLIC" : "important\">PRIVATE" )
			        + "</span></td>"
		        + "<td>Just Now</td>"
		        + "<td>"
		            + "<div class=\"btn-group\">"
		            	+ "<button id=\"co-notes-notify-" + data['noteid'] + "\" class=\"btn btn-small\"><i class=\"icon-eye-open\"></i></button>"
		            	+ "<button id=\"co-notes-view-"   + data['noteid'] + "\" class=\"btn btn-small\"><i class=\"icon-zoom-in\"></i></button>"
		            	+ "<button id=\"co-notes-edit-"   + data['noteid'] + "\" class=\"btn btn-small\"><i class=\"icon-pencil\"></i></button>"
		            	+ "<button id=\"co-notes-trash-"  + data['noteid'] + "\" class=\"btn btn-small\"><i class=\"icon-trash\"></i></button>"
		        	+ "</div>"
		        + "</td>"
		        + "</tr>"
		);
        $( "#co-notes-notify-" + data['noteid'] ).on( 'click', coNotesNotifyToggle );		
		$( "#co-notes-view-"   + data['noteid'] ).on( 'click', coNotesViewDialog );
		$( "#co-notes-edit-"   + data['noteid'] ).on( 'click', coNotesEditDialog );
		$( "#co-notes-trash-"  + data['noteid'] ).on( 'click', coNotesDelete );
		
		$( "#co-notes-no-notes-msg" ).hide();
		$( "#co-notes-table" ).show();
		
		$( "#co-notes-table-row-" + data['noteid'] ).fadeIn( 'slow' );
	}
	else {
		var noteid = $( "#notes-dialog-noteid" ).val();
		$( "#co-notes-table-row-title-" + noteid ).html( $( "#co-notes-ftitle" ).val() );
		$( "#co-notes-table-row-updated-" + noteid ).html( "Just Now" );
		$( "#co-notes-table-row-public-" + noteid ).html(
			"<span class=\"label label-"
		        + ( $( "#co-notes-fpublic" ).is( ':checked' ) ? "success\">PUBLIC" : "important\">PRIVATE" )
		        + "</span>"
		);
		
		$( "#co-notes-table-row-" + data['noteid'] ).fadeOut( 'fast', function() {
			$( "#co-notes-table-row-" + data['noteid'] ).fadeIn( 'slow' );	
		});
		
	}
	
	coNotesClearDialog();
}

function coNotesClearDialog() {
	$( "#co-notes-ftitle" ).val("");
	$( "#co-notes-fnote" ).val("");
	$( "#co-notes-fpublic" ).prop( 'checked', false );
	$( "#co-notes-warning" ).hide();
}

function coCustomerNotifyToggle( event ){
    var custid = substr( event.delegateTarget.id, 15 );
    $.get( '{genUrl controller="customer-notes" action="ajax-notify-toggle"}/custid/' + custid, function(data){
        if( data == "ok" )
            $( event.delegateTarget ).toggleClass( "active" );
    });
}

function coNotesNotifyToggle( event ){
    var noteid = substr( event.delegateTarget.id, 16 );
    $.get( '{genUrl controller="customer-notes" action="ajax-notify-toggle"}/id/' + noteid, function(data){
        if( data == "ok" )
            $( event.delegateTarget ).toggleClass( "active" );
    });
}

$(document).ready(function(){

	{if $user->getPrivs() eq USER::AUTH_SUPERUSER}
	
		$( "#co-notes-add-btn" ).on( "click", function( event ){
			event.preventDefault();
			$( "#co-notes-dialog-title-action" ).html( 'Add a' );
			$( "#co-notes-fadd" ).html( 'Add' );
			$( "#co-notes-dialog-date" ).html( '' );
			$( "#notes-dialog-noteid" ).val( '0' );
			coNotesOpenDialog( event );
		});
		
		$( "#co-notes-add-link" ).on( "click", function( event ){
			event.preventDefault();
			$( "#co-notes-add-btn" ).trigger( 'click' );
		});
        
        $( 'button[id|="co-cust-notify"]' ).on( 'click', coCustomerNotifyToggle );
        $( 'button[id|="co-notes-notify"]' ).on( 'click', coNotesNotifyToggle );
        
		$( 'button[id|="co-notes-edit"]' ).on( 'click', coNotesEditDialog );
		$( 'button[id|="co-notes-trash"]' ).on( 'click', coNotesDelete );
		
		$( "#co-notes-fpublic" ).on( "click", function( event ){
			coNotesPublicCheckbox();
		});
	
		$( "#co-notes-fadd" ).on( "click", coNotesSubmitDialog );
		
		$( '#co-notes-form' ).on( 'submit', function( event ) {
			event.preventDefault();
			coNotesSubmitDialog( event );
		    return false;
		});
		
		$( "#co-notes-dialog" ).on( 'shown', function( e ) {
			$( "#co-notes-ftitle" ).focus();
		});

	{/if}

	$( "#tab-notes" ).on( 'shown', function( e ) {
		// mark notes as read and update the users last read time
		$( '#notes-unread-indicator' ).remove();
		{if $user->getPrivs() eq USER::AUTH_SUPERUSER}
			$.get( '{genUrl controller="customer-notes" action="ajax-ping" custid=$cust->getId()}' );
		{else}
		    $.get( '{genUrl controller="customer-notes" action="ajax-ping"}' );
		{/if}
	});
		
	$( 'button[id|="co-notes-view"]' ).on( 'click', coNotesViewDialog );

});

