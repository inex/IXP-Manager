
function coNotesOpenDialog( event ) {
	event.preventDefault();
	
	$( "#co-notes-dialog" ).modal();
}

function coNotesSubmitDialog( event ) {
	event.preventDefault();

	$.post( '{genUrl controller="customer-notes" action="ajax-add"}', $( "#co-notes-form" ).serialize(), coNotesPost, 'json' )
		.fail( function() {
			bootbox.alert( "Error! Could not save your note." );
		});
}

function coNotesPost( data, textStatus, jqXHR ) {
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

	$( "#co-notes-add-link" ).on( "click", coNotesOpenDialog );

	
	$( "#co-notes-fpublic" ).on( "click", function( event ){
		if( $( "#co-notes-fpublic" ).is( ':checked' ) )
			$( "#co-notes-warning" ).show();
		else
			$( "#co-notes-warning" ).hide();
	});

	$( "#co-notes-fadd" ).on( "click", coNotesSubmitDialog );
	
});

