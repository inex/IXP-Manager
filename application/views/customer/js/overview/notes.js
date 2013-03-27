
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
	
	$( "#co-messages" ).append(
		"<div class=\"alert alert-success\">"
		    + "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>"
		    + "<strong>Success!</strong> Your note has been saved."
		    + "</div>"
	);
	
	$( "#co-notes-table-tbody" ).prepend(
		"<tr>"
		    + "<td>" + $( "#co-notes-ftitle" ).val() + "</td>"
		    + "<td>" + "<span class=\"label label-"
		        + ( $( "#co-notes-fpublic" ).is( ':checked' ) ? "success\">PUBLIC" : "important\">PRIVATE" )
		        + "</span></td>"
	        + "<td>Just Now</td>"
	        + "<td>Fix Me</td"
	        + "</tr>"
	);
	
	coNotesClearDialog();
}

function coNotesClearDialog() {
	$( "#co-notes-ftitle" ).val("");
	$( "#co-notes-fnote" ).val("");
	$( "#co-notes-fpublic" ).prop( 'checked', false );
	$( "#co-notes-warning" ).hide();
}


$(document).ready(function(){

	$( "#co-notes-add-link" ).on( "click", function( event ){
		event.preventDefault();
		$( "#co-notes-dialog-title-action" ).html( 'Add a' );
		$( "#co-notes-fadd" ).html( 'Add' );
		$( "#co-notes-dialog-date" ).html( '' );
		$( "#notes-dialog-noteid" ).val( '0' );
		coNotesOpenDialog( event );
	});

	$( 'button[id|="co-notes-edit"]' ).on( 'click', coNotesEditDialog );
	
	$( "#co-notes-fpublic" ).on( "click", function( event ){
		coNotesPublicCheckbox();
	});

	$( "#co-notes-fadd" ).on( "click", coNotesSubmitDialog );
	
});

