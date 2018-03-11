
function coNotesOpenDialog() {
	$( "#co-notes-dialog" ).modal();
}


function coNotesPublicCheckbox() {
    if( $( "#co-notes-fpublic" ).is( ':checked' ) ){
        $( "#co-notes-warning" ).show();
    } else {
        $( "#co-notes-warning" ).hide();
    }
}

function coNotesClearDialog() {
    $( "#co-notes-ftitle" ).val("");
    $( "#co-notes-fnote" ).val("");
    $( "#co-notes-fpublic" ).prop( 'checked', false );
    $( "#co-notes-warning" ).hide();
}

function coNotesEditDialog( event ) {
	let noteid = substr( event.delegateTarget.id, 14 );

    let urlAction = "{url( '/api/v4/customer-note/get' )}/"+ noteid;

    $.ajax( urlAction )
        .done( function( data ) {

            if( data['error'] ) {
                bootbox.alert( "Error! Error getting the note from the server." );
                return;
            }

            $( "#co-notes-fadd"        ).html( 'Save' );
            $( "#co-notes-ftitle"      ).val( data['title'] );
            $( "#co-notes-fnote"       ).val( data['note']  );
            $( "#notes-dialog-noteid"  ).val( data['id'] );
            $( "#co-notes-dialog-date" ).html( 'Note first created: ' + data['created'] );

            if( data['private'] ){
                $( "#co-notes-fpublic" ).prop( 'checked', false );
            } else {
                $( "#co-notes-fpublic" ).prop( 'checked', true );
            }

            coNotesPublicCheckbox();
            $( "#co-notes-dialog-title-action" ).html( 'Edit' );
            coNotesOpenDialog();

        })
        .fail( function(){
            bootbox.alert( "Error running ajax query for " + urlAction );
            throw new Error( "Error running ajax query for " + urlAction );
        })
        .always( function() {

        });
}


function coNotesDelete( event ) {
    let noteid = substr( event.delegateTarget.id, 15 );

    bootbox.confirm( "Are you sure you want to delete this note?", function(result) {
        if( result ) {

            let urlAction = "{url( '/api/v4/customer-note/delete' )}/"+ noteid;

            $.ajax( urlAction , {
                type: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                }
            })
			.done( function( data ) {

				if( data['error'] ) {
					bootbox.alert( "Error! Server side error deleting the note." );
					return;
				}

				$( "#co-notes-table-row-" + noteid ).fadeOut( 'slow', function() {
					$( "#co-notes-table-row-" + noteid ).remove();
				});
			})
			.fail( function(){
				throw new Error( "Error running ajax query for " + urlAction );
				alert( "Error running ajax query for " + urlAction );
			})
			.always( function() {

			});
        }
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

    urlAction = "{route( 'customer-notes@add' )}";

    $.ajax( urlAction, {
        type: 'POST',
        data: $( "#co-notes-form" ).serialize(),
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        }
    })
        .done( function( data ) {
            coNotesPost( data );
        })
        .fail( function(){
            bootbox.alert( "Error! Could not save your note." );
        })
        .always( function() {

        });
}



function coNotesViewDialog( event ) {
	let noteid = substr( event.delegateTarget.id, 14 );

    let urlAction = "{url( '/api/v4/customer-note/get' )}/"+ noteid;

    $.ajax( urlAction )
        .done( function( data ) {

            if( data['error'] ) {
                bootbox.alert( "Error! Error getting the note from the server." );
                return;
            }

            $( "#co-notes-view-dialog-title" ).html( data['title'] );
            $( "#co-notes-view-dialog-note"  ).html( data['noteParsedown'] );
            $( "#co-notes-view-dialog-date"  ).html( 'Note first created: ' + data['created'] );
            $( "#co-notes-view-dialog" ).modal();

        })
        .fail( function(){
            bootbox.alert( "Error running ajax query for " + urlAction );
            throw new Error( "Error running ajax query for " + urlAction );
        })
        .always( function() {

        });
}

function coNotesPost( data ) {
	// server-side form validation fails:
	if( data.rep['error'] ) {
		bootbox.alert( "Error! Your note could not be saved." );
		return;
	}

	$( "#co-notes-dialog" ).modal( 'hide' );
	
	if( $( "#co-notes-fadd" ).html() == 'Add' ) {
		$( "#co-notes-table-tbody" ).prepend(
			"<tr class=\"hide\" id=\"co-notes-table-row-" + data.rep['noteid'] + "\">"
			    + "<td>" + $( "#co-notes-ftitle" ).val() + "</td>"
			    + "<td>" + "<span class=\"label label-"
			        + ( $( "#co-notes-fpublic" ).is( ':checked' ) ? "success\">PUBLIC" : "important\">PRIVATE" )
			        + "</span></td>"
		        + "<td>Just Now</td>"
		        + "<td>"
		            + "<div class=\"btn-group\">"
		            	+ "<button id=\"co-notes-notify-" + data.rep['noteid'] + "\" class=\"btn btn-small\"><i class=\"icon-eye-open\"></i></button>"
		            	+ "<button id=\"co-notes-view-"   + data.rep['noteid'] + "\" class=\"btn btn-small\"><i class=\"icon-zoom-in\"></i></button>"
		            	+ "<button id=\"co-notes-edit-"   + data.rep['noteid'] + "\" class=\"btn btn-small\"><i class=\"icon-pencil\"></i></button>"
		            	+ "<button id=\"co-notes-trash-"  + data.rep['noteid'] + "\" class=\"btn btn-small\"><i class=\"icon-trash\"></i></button>"
		        	+ "</div>"
		        + "</td>"
		        + "</tr>"
		);
        $( "#co-notes-notify-" + data.rep['noteid'] ).on( 'click', coNotesNotifyToggle );
		$( "#co-notes-view-"   + data.rep['noteid'] ).on( 'click', coNotesViewDialog );
		$( "#co-notes-edit-"   + data.rep['noteid'] ).on( 'click', coNotesEditDialog );
		$( "#co-notes-trash-"  + data.rep['noteid'] ).on( 'click', coNotesDelete );
		
		$( "#co-notes-no-notes-msg" ).hide();
		$( "#co-notes-table" ).show();
		
		$( "#co-notes-table-row-" + data.rep['noteid'] ).fadeIn( 'slow' );
	}
	else {
		let noteid = $( "#notes-dialog-noteid" ).val();
		$( "#co-notes-table-row-title-" + noteid ).html( $( "#co-notes-ftitle" ).val() );
		$( "#co-notes-table-row-updated-" + noteid ).html( "Just Now" );
		$( "#co-notes-table-row-public-" + noteid ).html(
			"<span class=\"label label-"
		        + ( $( "#co-notes-fpublic" ).is( ':checked' ) ? "success\">PUBLIC" : "important\">PRIVATE" )
		        + "</span>"
		);
		
		$( "#co-notes-table-row-" + data.rep['noteid'] ).fadeOut( 'fast', function() {
			$( "#co-notes-table-row-" + data.rep['noteid'] ).fadeIn( 'slow' );
		});
		
	}
	
	coNotesClearDialog();
}



function coCustomerNotifyToggle( event ){
    let custid = substr( event.delegateTarget.id, 15 );

	let urlAction = "{url( '/api/v4/customer-note/ajax-notify-toggle/custid' )}/"+ custid;

    $.ajax( urlAction )
        .done( function( data ) {
            if( data ){
                $( event.delegateTarget ).toggleClass( "active" );
            }
        })
        .fail( function(){
            throw new Error( "Error running ajax query for " + urlAction );
            alert( "Error running ajax query for " + urlAction );
        })
        .always( function() {

        });
}

function coNotesNotifyToggle( event ){
    let noteid = substr( event.delegateTarget.id, 16 );
    let urlAction = "{url( '/api/v4/customer-note/ajax-notify-toggle/id' )}/"+ noteid;

    $.ajax( urlAction )
        .done( function( data ) {
            if( data ){
                $( event.delegateTarget ).toggleClass( "active" );

            }
        })
        .fail( function(){
            throw new Error( "Error running ajax query for " + urlAction );
            alert( "Error running ajax query for " + urlAction );
        })
        .always( function() {

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
            coNotesClearDialog();
			coNotesOpenDialog();
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
			$.get( "{route( 'customerNotes@ping' , [ 'id' => $cust->getId() ] )}" );
		{else}
            $.get( "{route( 'customerNotes@ping' )}" );
		{/if}
	});
		
	$( 'button[id|="co-notes-view"]' ).on( 'click', coNotesViewDialog );

});

